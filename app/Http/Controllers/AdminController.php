<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;
use Response;

use App\Models\Post;
use App\Models\Reply;
use App\User;
use App\Models\Setting;
use App\Libs\MyCheck;
use Auth;
use Vinkla\Hashids\Facades\Hashids;

class AdminController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    public function index(Request $request) {

        if(!in_array($request->input('type'),['users','posts','replies','post','reply'])) abort(404);
        $type = $request->input('type');
        return $this->$type($request);
    }

    private function users(Request $request) {

        if(!MyCheck::IhaveRight('super'))  abort(403);

        $users = User::where('id','!=',Auth::id())->orderBy('id', 'asc')->simplePaginate(40);

        return view('home.admin.users',compact('users'));
    }

    private function posts(Request $request) {
        $forum = $request->input('forum');

        $menu = '帖子管理';
        if($request->input('trash')=='only') {
            $posts = Post::onlyTrashed();
            $trashed =true;
            $menu = '回收站';
        }
        else  if($request->input('trash')=='with') {
            $posts = Post::withTrashed();
            $trashed =false;
        }
        else {
            $posts = Post::where('id','>',0);
            $trashed =false;
        }

        if($forum) $posts = $posts->where('forum',$forum)->orderBy('post_time', 'desc');
        else $posts = $posts->orderBy('post_time', 'desc');
        $posts = $posts->simplePaginate(40);

        return view('home.admin.posts',compact('posts','trashed','menu'));
    }

    private function replies(Request $request) {
        $post_id = $request->input('uid');

        $post = Post::withTrashed()->where('uid',$post_id)->first();
        if(!$post) abort(204);

        $replies = Reply::withTrashed()->where('tid',$post_id)->orderBy('post_time', 'asc')->simplePaginate(40);
        return view('home.admin.replies',compact('replies','post'));
    }

    private function post(Request $request) {
        $uid = $request->input('uid');
        $post = Post::withTrashed()->where('uid',$uid)->first();
        if($post) {
            $emotions = json_decode(Setting::where('name','emotions')->first()->value);
            return view('home.admin.post', ['post' => $post, 'emotions' => $emotions]);
        }
        else
            abort(204);
    }

    private function reply(Request $request) {
        $uid = $request->input('uid');
        $post = Post::withTrashed()->where('uid',$uid)->first();
        if(!$post) abort(204);

        $replies = Reply::withTrashed()->where('tid',$uid)->orderBy('post_time','asc')->simplePaginate(30);

        if($replies && $post)
            return view('home.admin.reply',['replies' => $replies,'post' => $post]);
        else
            abort(204);
    }

    public function submit(Request $request) {

        if(!in_array($request->input('type'),['users','posts','replies'])) abort(404);

        $type = camel_case('submit_'.$request->input('type'));
        return $this->$type($request);

    }

    private function submitUsers(Request $request) {

        if(!MyCheck::IhaveRight('super'))  abort(403);

        $user_id = $request->input('user_id');
        $uid = collect($request->input('user_id'));

        $uid = $uid->map(function($item){
            $item =Hashids::decode($item);
            return $item[0];
        });

        $right = $request->input('right');

        if(!in_array($request->input('action'),['remove','admin'])) abort(404);

        switch($request->input('action')) {
            case 'remove':
                User::whereIn('id',$uid)->delete();
                break;
            case 'admin':
                User::whereIn('id',$uid)->update(['right' => $right]);
                break;
            default:
        }


        return Response::json(
            [
                'status' => 'success',
                'message' => $request->input('action') == 'remove'?'成功删除用户':'成功更新用户身份',
                'items' => $user_id,
                'action' => $request->input('action'),
                'role' => \App\Libs\MyCheck::roleName($right),
                'color' => \App\Libs\MyCheck::roleColor($right)
            ]
        );
    }

    private function submitPosts(Request $request) {

        $post_id = $request->input('post_id');

        if(!in_array($request->input('action'),['remove','top','down','restore'])) abort(404);

        $message= '';

        switch($request->input('action')) {
            case 'remove':
                if(MyCheck::IhaveRight('super')) {
                    Post::whereIn('uid',$post_id)->onlyTrashed()->get()->map(function($item){
                        Reply::where('tid',$item->uid)->onlyTrashed()->forceDelete();
                        $item -> forceDelete();
                    });
                }

                Post::whereIn('uid',$post_id)->update(['deleted_by' => Auth::id()]);
                Post::whereIn('uid',$post_id)->delete();
                Reply::whereIn('tid',$post_id)->update(['deleted_by' => Auth::id()]);
                Reply::whereIn('tid',$post_id)->delete();
                $message= '成功删除主题';
                break;
            case 'restore':
                collect($post_id)->map(function($item){
                    Reply::where('tid',$item)->restore();
                    Reply::where('tid',$item)->update(['deleted_by' => 0]);
                    return  [$item, Reply::where('tid',$item)->count()];
                })->map(function($item){
                    Post::where('uid',$item[0])->restore();
                    Post::where('uid',$item[0])->update(['replies' => $item[1],'deleted_by' => 0]);
                });

                $message= '成功恢复主题';
                break;
            case 'top':
                Post::withTrashed()->whereIn('uid',$post_id)->update(['top_time' => date('Y-m-d H:i:s',time())]);
                $message= '成功设置置顶';
                break;
            case 'down':
                Post::withTrashed()->whereIn('uid',$post_id)->update(['top_time' => date('Y-m-d H:i:s',0)]);
                $message= '成功设置置顶';
                break;
            default:
                $message= '对不起，操作出错！';
        }


        return Response::json(
            [
                'status' => 'success',
                'message' => $message,
                'items' => $post_id,
                'action' => $request->input('action')
            ]
        );
    }

    private function submitReplies(Request $request) {
        $reply_id = $request->input('reply_id');
        $post_id = $request->input('post_id');

        if(!in_array($request->input('action'),['remove'])) abort(404);

        if(MyCheck::IhaveRight('super')) {
            Reply::whereIn('uid',$reply_id)->onlyTrashed()->forceDelete();
        }

        Reply::whereIn('uid',$reply_id)->update(['deleted_by' => Auth::id()]);

        $new_delete =  count($reply_id) - Reply::whereIn('uid',$reply_id)->onlyTrashed()->count();
        Reply::whereIn('uid',$reply_id)->delete();
        Post::where('uid',$post_id)->decrement('replies', $new_delete);


        return Response::json(
            [
                'status' => 'success',
                'message' => $request->input('action') == 'remove'?'成功删除回帖':'成功更新回帖',
                'items' => $reply_id,
                'action' => $request->input('action')
            ]
        );
    }
}

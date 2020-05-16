<?php

namespace App\Http\Controllers;

use App\Libs\MyCheck;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;
use Validator;
use Auth;

use App\Models\Post;
use App\Models\Reply;
use App\Models\Forum;
use App\User;
use App\Models\Setting;
use Hash;

class UserController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    public function index(Request $request) {

        if($request->input('type') == __FUNCTION__ || !$request->input('type'))
            $type = 'home';
        else
            $type = $request->input('type');

        if($request->isMethod('post'))
            $type = camel_case('submit_'.$type);

        if(method_exists($this,$type)) return $this->$type($request);
        else abort(404);

    }

    private function home() {
        $forums = Forum::orderBy('id', 'asc')->get();

        if(MyCheck::IhaveRight('admin'))
            return view('home.admin.index',compact('forums'));
        else
            return view('home.user.index',compact('forums'));
    }

    private function posts(Request $request) {
        $forum = $request->input('forum');

        $menu = '帖子';
        $posts = Post::where('id','>',0);
        if($forum) $posts = $posts->where('forum',$forum)->orderBy('post_time', 'desc');
        else $posts = $posts->orderBy('post_time', 'desc');
        $posts = $posts->simplePaginate(40);

        return view('home.user.posts',compact('posts','menu'));
    }

    private function post(Request $request) {
        $uid = $request->input('uid');
        $post = Post::where('uid',$uid)->first();
        if($post) {
            $emotions = json_decode(Setting::where('name','emotions')->first()->value);
            return view('home.user.post', ['post' => $post, 'emotions' => $emotions]);
        }
        else
            abort(204);  //no content
    }

    private function reply(Request $request) {
        $post_id = $request->input('uid');

        $post = Post::where('uid',$post_id)->first();
        if(!$post) abort(204); //no content

        $replies = Reply::where('tid',$post_id)->orderBy('post_time', 'asc')->simplePaginate(40);
        return view('home.user.reply',compact('replies','post'));
    }

    private function password(Request $request) {
        return view('home.password');
    }

    private function submitPassword(Request $request) {

        if(!$request->isMethod('post')) abort(405);

        $old_password = $request->input('old_password');
        $password = $request->input('password');
        $data = $request->all();
        $rules = [
            'old_password'=>'required|between:6,20',
            'password'=>'required|between:6,20|confirmed',
        ];
        $messages = [
            'required' => '密码不能为空',
            'between' =>  '密码必须是6~20位之间',
            'confirmed' => '新密码和确认密码不匹配'
        ];
        $validator = Validator::make($data, $rules, $messages);
        $user = Auth::user();
        $validator->after(function($validator) use ($old_password, $user) {
            if (!\Hash::check($old_password, $user->password)) {
                $validator->errors()->add('old_password', '原密码错误');
            }
        });
        if ($validator->fails()) {
            return back()->withErrors($validator);  //返回一次性错误
        }
        $user->password = bcrypt($password);
        $user->save();
        Auth::logout();  //更改完这次密码后，退出这个用户
        return redirect('auth/login');

    }

    private function submitSuicide(Request $request) {

        if(!$request->isMethod('post')) abort(405);

        if(!MyCheck::IhaveRight('admin')) {
            User::find(Auth::id())->delete();
            return  redirect('auth/login')->with('status', '你竟然真自杀了？！你有一点冲动啦，骚年！');
        }
        else abort(403);
    }
}

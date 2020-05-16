<?php

namespace App\Http\Controllers\MyApp;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Reply;
use Illuminate\Support\Facades\Response;
use App\Models\Forum;
use App\Models\Setting;
use Psy\Util\Json;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{   
    private $utcOffset = 0;
	
	function __construct(){
        $this->middleware('xsrfToken');
    }
	
	//http or xmlhttp
    function layout(Request $request) {
        return view('myApp.layout',['xsrf_token' => $request->cookie('XSRF-TOKEN')]);
    }
	
	//xmlhttp
	function index() {
		
        $posts = Post::where('top_time', '>', date('Y-m-d',time()))->orderBy('replies', 'desc')->orderBy('post_time','desc')->take(10)->get();
        
		foreach($posts as $index => $post) {
            $posts[$index] -> forum_title = Forum::where('brief',$post->forum)->first()->title;
			$posts[$index] -> content = mb_substr($post->content,0,100);
		}
		
		//return view('myApp.index',['posts' => $posts,'forum' => 'all']);
		return Json::encode(['posts' => $posts,'forum' => 'all','template'=>'list_items']);
    }
	
	//only xmlhttp
	function post($uid) {
		
        $post = Post::where('uid',$uid)->first();

        if($post) {
			$post-> forum_title = Forum::where('brief',$post->forum)->first()->title;
            //return view('myApp.post', ['post' => $post, 'emotions' => $emotions]);
			return Json::encode(['post' => $post,'template' =>'post_item']);
        }
        else
            abort(204); //请求成功，但是内容没有了
		
	}
	
	//only xmlhttp
	function reply($uid,Request $request) {
        $post = Post::where('uid',$uid)->first();
		
		if(!$post) abort(204);
		
        $replies = Reply::where('tid',$uid)->orderBy('post_time','asc')->paginate(20);
		
        if($replies && $post) {
			$post-> forum_title = Forum::where('brief',$post->forum)->first()->title;
			$post-> pages = $replies->lastPage();
			$post-> perPage = $replies -> perPage();
			return Json::encode(['replies' => $replies->toArray(),'post' => $post,'template' =>'replies']);
			//return view('myApp.reply',['replies' => $replies,'post' => $post]);
		}
        else
            abort(204);
	}
	
	//only xmlhttp
	function forum($forum) {
        $entry = DB::table('forums')->where('brief',$forum)->first();

        if($entry) {
            $posts = Post::where('forum', $forum)->where('top_time', '>', date('Y-m-d',time()))->orderBy('top_time', 'desc')->take(10)->get();
            
			foreach($posts as $index => $post) {
			  $posts[$index] -> content = mb_substr($post->content,0,100);
			  $posts[$index] -> forum_title = $entry->title;
		    }
			
			//return view('myApp.forum',['posts' => $posts, 'forum' => $forum]);
			return Json::encode(['posts' => $posts, 'forum' => $forum,'template'=>'list_items']);
        }
        else
            return abort(404);

    }
	
	//only xmlhttp
	function about($id = '') {

        $id = $id?$id:'introduction';
        $html = DB::table('abouts')->where('id',$id)->select('id','title','content')->first();

        if($html)
			return Json::encode(['html' => $html,'template' =>'html_page']);
            //return view('myApp.about',['about' => $about]);
        else
            abort(404);
    }
	
	//only xmlhttp
	function forum_list() {
		$forums = Forum::select('title','brief','color','site_url','affiliated')->orderBy('id', 'asc')->get();
		return Json::encode($forums);
	}
	
	//only xmlhttp
	function  emotion_list() {
        return Setting::where('name','emotions')->first()->value;
    }
	
	//some post methods including hit action and attitude/thumb action
	
	function  post_hit(Request $request) {
        
		$uid = $request->uid;

        DB::table('posts')->where('uid',$uid)->increment('hits');
    }
	
	function  reply_hit(Request $request) {
        $uid = $request->uid;
        $uid = explode('|',$uid);

        DB::table('replies')->whereIn('uid',$uid)->increment('hits');
    }
	
	
	function  attitude(Request $request) {

        $attitude = $request->input('attitude');
        $uid = $request->input('uid');

        $post = Post::where('uid',$uid)->first();
		
		if(!$post) return false;
		
        $emotion = json_decode($post->emotion);

        $emotions = json_decode($this->emotion_list());

        if(array_key_exists($attitude,$emotions)) {
               $emotion ->$attitude =   $emotion ->$attitude + 1;
        }

        $post->emotion = Json::encode($emotion);

        $post -> save();
    }
	
	function  thumb(Request $request) {

        $thumb = $request->input('thumb');

        $uid = $request->input('uid');

        $reply = Reply::where('uid',$uid)->first();
		
		if(!$reply) return false;

        if(in_array($thumb,['support','oppose'])) {
            $reply -> $thumb =   $reply -> $thumb + 1;
        }

        $reply -> save();
    }
}

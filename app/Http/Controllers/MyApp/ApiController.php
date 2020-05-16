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

class ApiController extends Controller
{   
    private $utcOffset = 0;
	
	function __construct(Request $request){
        $this->middleware('signature');
		$this->utcOffset = $request->input('timeLocal') - $request->input('timestamp');
    }
     
    function index(Request $request) {
		
        $posts = Post::where('top_time', '>', date('Y-m-d',time()-$this->utcOffset))->orderBy('replies', 'desc')->orderBy('post_time','desc')->take(10)->get();
        
		foreach($posts as $index => $post) {
            $posts[$index] -> forum_title = Forum::where('brief',$post->forum)->first()->title;
			$posts[$index] -> content = mb_substr($post->content,0,100);
		}

        return response()
            ->json(['result'=>'ok','posts' => $posts,'forum' => 'all','template'=>'list_items'])
            ->setCallback($request->input('callback'));
    }
	
	function post($uid,Request $request) {
		
        $post = Post::where('uid',$uid)->first();

        if($post) {
			$post-> forum_title = Forum::where('brief',$post->forum)->first()->title;
			return response()
            ->json(['result' => 'ok','post' => $post,'template' =>'post_item'])
            ->setCallback($request->input('callback'));
        }
		else 
			return response()
            ->json(['result' => 'null'])
            ->setCallback($request->input('callback'));
		
	}
	
	function reply($uid,Request $request) {
        $post = Post::where('uid',$uid)->first();
		
		if($post) 
			$replies = Reply::where('tid',$uid)->orderBy('post_time','asc')->paginate(20);
		
		if($post && $replies) {
			$post-> forum_title = Forum::where('brief',$post->forum)->first()->title;
			$post-> pages = $replies->lastPage();
			$post-> perPage = $replies -> perPage();
			
			return response()
            ->json(['result' => 'ok','replies' => $replies->toArray(),'post' => $post,'template' =>'replies'])
            ->setCallback($request->input('callback'));
		}
			
        else 
			return response()
            ->json(['result' => 'null'])
            ->setCallback($request->input('callback'));
    }
	
	//only xmlhttp
	function forum($forum,Request $request) {
        $entry = DB::table('forums')->where('brief',$forum)->first();

        if($entry) {
            $posts = Post::where('forum', $forum)->where('top_time', '>', date('Y-m-d',time()-$this->utcOffset))->orderBy('top_time', 'desc')->take(10)->get();
            
			foreach($posts as $index => $post) {
			  $posts[$index] -> content = mb_substr($post->content,0,100);
			  $posts[$index] -> forum_title = $entry->title;
		    }
			return response()
            ->json(['result' => 'ok','posts' => $posts, 'forum' => $forum,'template'=>'list_items'])
            ->setCallback($request->input('callback'));
        }
        else
            return response()
            ->json(['result' => 'null'])
            ->setCallback($request->input('callback'));

    }
	
	//only xmlhttp
	function about(Request $request,$id = '') {

        $id = $id?$id:'introduction';
        $html = DB::table('abouts')->where('id',$id)->select('id','title','content')->first();

        if($html)
		    return response()
            ->json(['result' => 'ok','html' => $html,'template'=>'html_page'])
            ->setCallback($request->input('callback'));
        else
            return response()
            ->json(['result' => 'null'])
            ->setCallback($request->input('callback'));
	}
	
	function  post_hit(Request $request) {
        
		$uid = $request->uid;

        DB::table('posts')->where('uid',$uid)->increment('hits');
		
		return response()
            ->json(['result' => 'ok'])
            ->setCallback($request->input('callback'));
    }
	
	function  reply_hit(Request $request) {
        $uid = $request->uid;
        $uid = explode('|',$uid);

        DB::table('replies')->whereIn('uid',$uid)->increment('hits');
		
		return response()
            ->json(['result' => 'ok'])
            ->setCallback($request->input('callback'));
    }
	
	
	function  attitude(Request $request) {

        $attitude = $request->input('attitude');
        $uid = $request->input('uid');

        $post = Post::where('uid',$uid)->first();
		
		if(!$post) return false; //here, we not response json, what will result in?
		
        $emotion = json_decode($post->emotion);

        $emotions = json_decode(Setting::where('name','emotions')->first()->value);

        if(array_key_exists($attitude,$emotions)) {
               $emotion ->$attitude =   $emotion ->$attitude + 1;
        }

        $post->emotion = Json::encode($emotion);

        $post -> save();
		
		return response()
            ->json(['result' => 'ok'])
            ->setCallback($request->input('callback'));
    }
	
	function  thumb(Request $request) {

        $thumb = $request->input('thumb');

        $uid = $request->input('uid');

        $reply = Reply::where('uid',$uid)->first();
		
		if(!$reply) return false; //not json response?

        if(in_array($thumb,['support','oppose'])) {
            $reply -> $thumb =   $reply -> $thumb + 1;
        }

        $reply -> save();
		
		return response()
            ->json(['result' => 'ok'])
            ->setCallback($request->input('callback'));
    }
	
	function forum_list (Request $request) {
		$forums = Forum::select('title','brief','color','site_url','affiliated')->orderBy('id', 'asc')->get();
		   
		return response()
            ->json(['result' => 'ok','forums' => $forums])
            ->setCallback($request->input('callback'));
	}
	
	function  emotion_list(Request $request) {
        $emotions = json_decode(Setting::where('name','emotions')->first()->value);
		 
		return response()
            ->json(['result' => 'ok','emotions' => $emotions])
            ->setCallback($request->input('callback')); 
    }
	
}

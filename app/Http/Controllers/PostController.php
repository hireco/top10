<?php

namespace App\Http\Controllers;

use Hashids\Hashids;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Reply;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;
use Psy\Util\Json;

class PostController extends Controller
{
    //
    function  post($uid) {
        $post = Post::where('uid',$uid)->first();

        if($post) {
            $emotions = $this->get_emotions_setting();
            return view('post', ['post' => $post, 'emotions' => $emotions]);
        }
        else
            abort(404);
    }

    function reply($uid) {
        $post = Post::where('uid',$uid)->first();
        
		if(!$post) abort(404);
		
		$replies = Reply::where('tid',$uid)->orderBy('post_time','asc')->simplePaginate(30);

        if($replies && $post)
            return view('reply',['replies' => $replies,'post' => $post]);
        else
            abort(404);
    }

    function  attitude(Request $request) {

        $attitude = $request->input('attitude');
        $uid = $request->input('uid');

        $post = Post::where('uid',$uid)->first();
		if(!$post) return false;
        
	    $emotion = json_decode($post->emotion);

        $emotions = $this->get_emotions_setting();

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

    function  post_hit(Request $request) {
        $uid = $request->uid;
		
		DB::table('posts')->where('uid',$uid)->increment('hits');
    }

    function  reply_hit(Request $request) {
        $uid = $request->uid;
        $uid = explode('|',$uid);
		
		DB::table('replies')->whereIn('uid',$uid)->increment('hits');
    }

    function  get_emotions_setting() {
        return json_decode(Setting::where('name','emotions')->first()->value);
    }
}

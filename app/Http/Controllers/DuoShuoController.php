<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Post;

class DuoShuoController extends Controller
{
    function index($uid, Request $request) {
		$post = Post::where('uid',$uid)->first();

        if($post) {
			return view('duoshuo',
			[
			 'thread_id' => $post->uid, 
			 'thread_title' => $post->title, 
			 'thread_url' => url('post/'.$post->uid)
			]
			);

        }
		
	}
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class ForumController extends Controller
{
    //
    function index($forum) {
        $entry = DB::table('forums')->where('brief',$forum)->first();

        if($entry) {
            $posts = Post::where('forum', $forum)->where('top_time', '>', date('Y-m-d',time()))->orderBy('top_time', 'desc')->take(10)->get();
            return view('forum', ['posts' => $posts, 'forum' => $forum]);
        }
        else
            return abort(404);

    }
}

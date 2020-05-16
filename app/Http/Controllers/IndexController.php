<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Post;

class IndexController extends Controller
{
    function index() {
        $posts = Post::where('top_time', '>', date('Y-m-d',time()))->orderBy('replies', 'desc')->orderBy('post_time','desc')->take(10)->get();
        return view('index',['posts' => $posts]);
    }
}

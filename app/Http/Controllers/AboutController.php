<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AboutController extends Controller
{
    function show($id = '') {

        $id = $id?$id:'introduction';
        $about = DB::table('abouts')->where('id',$id)->first();

        if($about)
            return view('about',['about' => $about]);
        else
            abort(404);
    }
}

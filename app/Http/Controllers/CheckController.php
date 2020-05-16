<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;

class CheckController extends Controller
{
    public function isLogged(Request $request) {
        if($request->ajax()) {
            if(Auth::check())
                return view('logged');
        }
        else
            abort(404);
    }
}

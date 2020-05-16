<?php

namespace App\Http\Middleware;

use Closure;
use Request;

class CheckXsrfToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
	    if(!$request->ajax() && !in_array($request->path(),['myApp'])) 
			abort(404);
		else 
			return $next($request);
    }
}

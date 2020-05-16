<?php

namespace App\Http\Middleware;

use Closure;

class CheckSignature
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
	    if(!$this->myCheck($request)) {
			return response()
            ->json(['result' => 'invalid'])
            ->setCallback($request->input('callback'));
		}
		
		return $next($request);
    }
	
	private function myCheck($request) {
		
		$timestamp = $request->input('timestamp');
		$timeLocal = $request->input('timeLocal');
		
		if(time() - $timestamp > 1800) return false; 
		
		$signature = $request->input('signature');
		$nonce = $request->input('nonce');
		
		$secretKey = 'iloveTOP104BBS';
		
		$tmpArr = array($timestamp,$nonce,$secretKey,$timeLocal);
		sort($tmpArr);
		
		$tmpStr = implode($tmpArr);
		
		$tmpStr = sha1($tmpStr);
		
		if($tmpStr == $signature) 
			return true;
		else 
			return false;
		
	}
}

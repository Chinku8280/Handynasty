<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Log;
use Closure;

class AuthenticateAPI
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
       
        if (!$request->bearerToken()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }        

        return $next($request);
    }
}

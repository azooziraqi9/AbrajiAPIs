<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $token= $request->header('Authorization');
        $sessionToken=  Session::get('token');
        return response()->json([
           "token"=>$token,
              "sessionToken"=>$sessionToken
        ], 401);
        if($token!=$sessionToken){
            return response()->json([
                'status' => 401,
                'error' => 'Unauthorized'
            ], 401);
        }
        return $next($request);
    }
}

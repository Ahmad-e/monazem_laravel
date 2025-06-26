<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Users
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && (Auth::user()->type_id == 1 || Auth::user()->type_id == 2)) {
            if(Auth::user()->blocked){
                return response()->json([
                    'status' => false,
                    'error'=> 412 ,
                    'message' => 'Your account is blocked, you cannot use the system'
                ], 412);
            }

            return $next($request);
        }

        return response()->json([
            'status' => false,
            'error'=> 401 ,
            'message' => 'Unauthorized , this api for users'
        ], 401);
    }
}

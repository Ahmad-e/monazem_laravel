<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Employees
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && (Auth::user()->type_id == 1 ) || Auth::user()->type_id == 2 || Auth::user()->type_id == 3) {
            return $next($request);
        }

        return response()->json(['status' => false, 'error' => 'Unauthorized , this api for employees '], 401);
    }
}

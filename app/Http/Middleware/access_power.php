<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Powers_users;

class access_power
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!(Auth::user()->overPower || Auth::user()->is_business_creator  )){
            $test = Powers_users::where('user_id',Auth::user()->id)->where('power_id',5)->first();
            if(!$test)
                return response()->json([
                    'status' => 411 ,
                    'error'=> 411 ,
                    'message' => 'You do not have access permission'
                ], 411);
        }


        return $next($request);
    }
}

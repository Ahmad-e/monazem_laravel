<?php

namespace App\Http\Middleware;

use App\Models\Powers_users;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class setting_power
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!(Auth::user()->overPower)){
            $test = Powers_users::where('user_id',Auth::user()->id)->where('power_id',20)->first();
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

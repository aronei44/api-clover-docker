<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->user()->email_verified_at != null || $request->user()->phone_verified_at  != null){
            return $next($request);
        }
        return response()->json([
            'message'=>'akun belum terverifikasi'
        ],401);
    }
}

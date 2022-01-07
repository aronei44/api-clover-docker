<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsMitra
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
        if($request->user()->role->role == "Seller" || $request->user()->role->role == "Admin" ){
            return $next($request);
        }
        return response()->json([
            'message'=>'anda bukan mitra'
        ],401);
    }
}

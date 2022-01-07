<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsPartner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->user()->profile_k_y_c->kyc_is_approved){
            return $next($request);
        }
        return response()->json([
            'message'=>'Mohon lengkapi KYC'
        ],401);
    }
}

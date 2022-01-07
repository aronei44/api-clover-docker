<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ModifyHeadersMiddleware
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

        $allowedOrigins = explode(',',env('SANCTUM_STATEFUL_DOMAINS'));
        $origin = env('APP_URL');
        if(isset($_SERVER['HTTP_ORIGIN'])){
            $origin = $_SERVER['HTTP_ORIGIN'];
        }

        array_push($allowedOrigins,$origin);
        if (in_array($origin, $allowedOrigins)) {
            return $next( $request )
                ->header('Access-Control-Allow-Origin',$origin)
                ->header('Access-Control-Allow-Method','PUT,POST,GET,FETCH,OPTIONS,DELETE')
                ->header('Access-Control-Allow-Credentials','true')
                ->header("Access-Control-Allow-Headers", "Content-Type,Authorization,X-Requested-With,XSRF-TOKEN,Accept,Origin,Access-Control-Allow-Origin")
                ->header('Content-Type','text/html; charset=UTF-8')
                ->header('Content-Type','multipart/form-data')
                ->header('Content-Type','application/json');
        }
        return $next( $request )->header('Content-Type','text/html; charset=UTF-8');
    }
}

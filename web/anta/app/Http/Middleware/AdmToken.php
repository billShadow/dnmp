<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class AdmToken
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
        $uri = $request->getRequestUri();
        if (isset($_COOKIE['adm_token']) && $_COOKIE['adm_token'] && Redis::get('adm_punch_token_'.$_COOKIE['adm_token'])) {
            if($_COOKIE['account'] == 'anta11' && strpos($uri, 'team11') === false){
                return redirect('adm/login');
            }
            return $next($request);
        } else {
            return redirect('adm/login');
        }
        //return $next($request);
    }
}

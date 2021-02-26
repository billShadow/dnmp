<?php

namespace App\Http\Middleware;

use App\Http\Controllers\VipcenterAdm\LoginController;
use Closure;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redis;

class CenterToken
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
        if (isset($_COOKIE['center_token']) && $_COOKIE['center_token']) {
            /*$info = LoginController::decryptToken();
            $uri = explode('?', $_SERVER['REQUEST_URI'])[0];
            if ($info['phone'] == 'user_account' && !in_array($uri, ['/center/user/userlist','/center/user/userinfo'])) {
                return redirect('center/login');
            }*/
            $uri = explode('?', $_SERVER['REQUEST_URI'])[0];
            if (isset($_COOKIE['account']) && $_COOKIE['account'] == 'user_account' && !in_array($uri, ['/center/user/userlist','/center/user/userinfo'])) {
                return redirect('center/login');
            }
            return $next($request);
        } else {
            return redirect('center/login');
        }
        //return $next($request);
    }
}

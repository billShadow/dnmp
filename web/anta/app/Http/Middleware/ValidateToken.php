<?php

namespace App\Http\Middleware;

use Closure;
use Hashids\Hashids;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redis;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class ValidateToken
{
    public function handle($request, Closure $next)
    {
        $auth = $request->header('Authorization');
        $token = substr($auth, strlen('Bearer '));
        $uri = $request->getRequestUri();
        if (in_array($uri, ['/draw/user/index','/draw/user/detail', '/anta/boy/index', '/auction/user/alert', '/kt/team11/api/index', '/kt/team11/api/detail']) && empty($token)) {
            return $next($request);
        }
        if(!$token){
            if (in_array($uri, ['/vip/user/userinfo'])) {
                fun_respon(1,null);
            }
            fun_respon(0,'token为空');
        }
        $info = Crypt::decrypt($token);

        if (!isset($info['openid']) || empty($info['openid'])) {
            fun_respon(0, 'token错误');
        }
        $redis_token = Redis::get('experience_token_' . $info['openid']);
        if (!$redis_token || $token != $redis_token) {
            fun_respon(0, '授权已过期', 401);
        }
        $hashKey = 'nimade';
        if (strpos($uri,'kt6') !== false) {
            $hashKey = 'o_id=N5&gid=vZ&utm_source=nhjjk1986&uid=';
        } elseif (strpos($uri,'team11') !== false) {
            $hashKey = 'team11_nhjjk1986&uid=';
        } elseif (strpos($uri,'xxl') !== false) {
            $hashKey = 'xxl_nhjjk1986&uid=xxl';
        }
        $hashids = new Hashids($hashKey);
        $request->offsetSet('openid', ($info['openid']));
        if (!empty($info['unionid'])) {
            $request->offsetSet('unionid', $info['unionid']);
        }
        $request->offsetSet('uid', implode("", $hashids->decode($info['id'])));
        return $next($request);
    }
}

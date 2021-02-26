<?php

namespace App\Http\Middleware;

use Closure;

class WrapResponse
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
        $response = $next($request);
        $content = $response->getOriginalContent();
        $base = [];
        if (is_array($content)) {
            $base['code'] = array_get($content, 'code', 200);
            $base['message'] = array_get($content, 'message', 'success');
            $content = array_merge( $base, ['data' => array_except($content, ['code', 'message'])] );
        }
        $response->setContent($content);
        return $next($request);
    }
}

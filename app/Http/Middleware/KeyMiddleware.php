<?php

namespace App\Http\Middleware;

use Closure;

class KeyMiddleware
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
        $key = $request->headers->all()['x-api-key'][0];
        $request->request->add(
            ['key' => $key]
        );
        return $next($request);
    }

}
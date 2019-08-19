<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

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
        $key = DB::table('api_key')->where('secret', '=', $key)->first(['key_name', 'id']);

        $request->request->add(
            ['key' => $key]
        );

        return $next($request);
    }

}
<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;

class JWTMiddleware
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
        $token = $request->bearerToken();

        if(!$token) {
            // Unauthorized response if token not there
            $output['status']  = false;
            $output['message'] = "Authentication Failed.";
            $output['error']   = "Token not provided.";
            return response()->json($output, 401);
        }

        try {
            $token       = decrypt($token);
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        
        } catch(ExpiredException $e) {

            $output['status']  = false;
            $output['message'] = "Authentication Failed.";
            $output['error']   = "Provided token is expired.";
            return response()->json($output, 400);
        
        } catch(\Exception $e) {

            $output['status']  = false;
            $output['message'] = "Authentication Failed.";
            $output['error']   = "An error while decoding token.";
            return response()->json($output, 400);
        }

        $request->request->add(['auth' => ['user' => $credentials->user]]);

        return $next($request);
    }
}

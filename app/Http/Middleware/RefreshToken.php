<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class RefreshToken
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

        try{
            $user = JWTAuth::parseToken()->authenticate();
        }catch(\Exception $e){
            if($e instanceof TokenExpiredException){
                $newToken = auth()->refresh();
                $user = auth()->user();
                return response()->json([
                    'ok' => true,
                    'token' => $newToken,
                    'user' => $user
                ]);
            }elseif($e instanceof TokenInvalidException){
                return response()->json(['ok'=> false,'msg'=> 'Invalid Token'],401);
            }else{
                return response()->json(['ok'=> false,'msg'=> 'Token Not found'],401);
            }
        }

        return $next($request);
    }
}

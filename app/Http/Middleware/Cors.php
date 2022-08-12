<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
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
        $request->header('Access-Controll-Allow-Origin','*');
        $request->header('Access-Controll-Allow-Methods', 'PUT,POST,DELETE,GET,OPTIONS');
        $request->header('Access-Controll-Allow-Headers', 'Content-Type,Accept,Authorization,X-Requested-With,Application');

        return $next($request);
    }
}

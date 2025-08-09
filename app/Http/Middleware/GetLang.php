<?php

namespace App\Http\Middleware;

use Closure;

class GetLang
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
        app()->setLocale('ar');
        if($request->hasHeader('lang')){
            app()->setLocale($request->header('lang'));
        }
        return $next($request);
    }
}

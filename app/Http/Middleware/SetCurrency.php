<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetCurrency
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('currency')) {
            session(['currency' => $request->input('currency')]);
        }

        return $next($request);
    }
}




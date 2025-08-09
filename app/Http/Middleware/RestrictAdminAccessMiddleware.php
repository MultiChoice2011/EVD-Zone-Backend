<?php

namespace App\Http\Middleware;

use App\Enums\GeneralStatusEnum;
use App\Traits\ApiResponseAble;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class RestrictAdminAccessMiddleware
{
    use ApiResponseAble;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $guard=null): Response
    {
        $environment = app()->environment();

        $whiteListedIps = [];
        $origins = [];

        if ($environment == 'production') {
            $whiteListedIps = ["213.136.68.114"];
            $origins = ['https://portal.asuscards.com'];
        } elseif ($environment == 'local') {
            $whiteListedIps = ["161.97.159.57"];
            $origins = [];
        }

        $ip = $request->ip();
        $origin = $request->headers->get('Origin');

        if (in_array($ip, $whiteListedIps)) {
            return $next($request);
        }

        if ($origin && in_array($origin, $origins)) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized.'], 423);
    }
}

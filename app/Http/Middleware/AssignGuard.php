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

class AssignGuard
{
    use ApiResponseAble;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $guard=null): Response
    {
        //return $next($request);

        if($guard != null){
            //set guard with coming in middleware
            auth()->shouldUse($guard);
            try {
                // check if  this token exist and belongs to user
                $user = Auth::guard($guard)->user();
                if ($user) {
                    $token = JWTAuth::parseToken()->authenticate();
                    if(! $token){
                        // if not correct return 401
                        return $this->unAuthenticatedResponse();
                    }
                }else{
                    // if not correct return 401
                    return $this->unAuthenticatedResponse();
                }
                // if user blocked not continue
                if($user->status == GeneralStatusEnum::getStatusInactive()){
                    return $this->ApiErrorResponse(null, "this email blocked");
                }
                return $next($request);

            } catch (TokenExpiredException $e) {
                // if token expired
                return  $this -> unAuthenticatedResponse();
            } catch (JWTException $e) {
                return  $this -> unAuthenticatedResponse();
            }

        }else{
            return  $this -> unAuthenticatedResponse();
        }


    }
}

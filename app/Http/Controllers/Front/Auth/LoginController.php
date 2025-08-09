<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\AuthRequests\LoginRequest;
use App\Http\Requests\Front\AuthRequests\LogoutRequest;
use App\Services\Front\Auth\LoginService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct(private LoginService $loginService)
    {}

    public function login(Request $request)
    {
        return $this->loginService->login($request);
    }

    // public function logout(LogoutRequest $request)
    // {
    //     return $this->loginService->logout($request);
    // }




}

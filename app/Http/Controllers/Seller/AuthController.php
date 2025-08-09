<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\LoginRequest;
use App\Http\Requests\Seller\RegisterRequest;
use App\Http\Requests\Seller\UpdateProfileRequest;
use App\Http\Requests\Seller\Verify2FAuthRequest;
use App\Services\Seller\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(public AuthService $authService){}
    public function register(RegisterRequest $request)
    {
        return $this->authService->register($request->validated());
    }
    public function login(LoginRequest $request){
        return $this->authService->login($request);
    }
    public function profile()
    {
        return $this->authService->profile();
    }
    public function logout()
    {
        return $this->authService->logout();
    }
    public function verifyG2FAuth(Verify2FAuthRequest $request)
    {
        return $this->authService->verifyG2FAuth($request);
    }
    public function updateProfile(UpdateProfileRequest $request)
    {
        return $this->authService->updateProfile($request->validated());
    }
}

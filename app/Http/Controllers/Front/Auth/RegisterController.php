<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\AuthRequests\RegisterRequest;
use App\Services\Front\Auth\RegisterService;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __construct(private RegisterService $registerService)
    {}

    public function generateG2FAuth()
    {
        return $this->registerService->generateG2FAuth();
    }


    public function register(RegisterRequest $request)
    {
        return $this->registerService->register($request);
    }




}

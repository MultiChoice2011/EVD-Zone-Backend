<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\AuthRequests\ForgetPasswordRequest;
use App\Http\Requests\Front\AuthRequests\LoginRequest;
use App\Http\Requests\Front\AuthRequests\UpdateForgetPasswordRequest;
use App\Http\Requests\Front\AuthRequests\VerifyOtpRequest;
use App\Services\Front\Auth\ForgetPasswordService;
use App\Services\Front\Auth\LoginService;
use Illuminate\Http\Request;

class ForgetPasswordController extends Controller
{
    public function __construct(private ForgetPasswordService $forgetPasswordService)
    {}

    public function sendForgetPassword(ForgetPasswordRequest $request)
    {
        return $this->forgetPasswordService->sendForgetPassword($request);
    }

    public function verifyForgetPasswordOtp(VerifyOtpRequest $request)
    {
        return $this->forgetPasswordService->verifyForgetPasswordOtp($request);
    }

    public function updatePassword(UpdateForgetPasswordRequest $request)
    {
        return $this->forgetPasswordService->updatePassword($request);
    }





}

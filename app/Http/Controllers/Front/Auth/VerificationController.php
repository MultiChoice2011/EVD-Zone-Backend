<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\AuthRequests\LoginRequest;
use App\Http\Requests\Front\AuthRequests\ResendOtpRequest;
use App\Http\Requests\Front\AuthRequests\VerifyOtpRequest;
use App\Services\Front\Auth\LoginService;
use App\Services\Front\Auth\SmsVerificationService;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function __construct(private SmsVerificationService $smsVerificationService)
    {}

    public function verifyOtp(VerifyOtpRequest $request)
    {
        return $this->smsVerificationService->verifyOtp($request);
    }

    public function resendOtp(ResendOtpRequest $request)
    {
        return $this->smsVerificationService->resendOtp($request);
    }




}

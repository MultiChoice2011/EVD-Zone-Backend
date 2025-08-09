<?php

namespace App\Http\Controllers\Seller\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\AuthRequests\ResendOtpRequest;
use App\Http\Requests\Seller\AuthRequests\VerifyOtpRequest;
use App\Services\Seller\Auth\SmsVerificationService;
use Illuminate\Http\Request;

class SmsVerificationController extends Controller
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

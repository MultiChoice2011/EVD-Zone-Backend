<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Seller\ResetPasswordRequest;
use App\Http\Requests\Seller\ForgotPasswordRequest;
use App\Services\Seller\ForgotPasswordService;

class ForgotPasswordController extends Controller
{
    public function __construct(public ForgotPasswordService $forgotPasswordService)
    {

    }
     // Send the reset link to the user's email
     public function sendResetLinkEmail(ForgotPasswordRequest $request)
     {
        return $this->forgotPasswordService->sendResetLinkEmail($request->validated());
     }
     public function resetPassword(ResetPasswordRequest $request)
    {
        return $this->forgotPasswordService->resetPassword($request->validated());
    }
}

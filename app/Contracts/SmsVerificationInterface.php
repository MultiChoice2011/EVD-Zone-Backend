<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface SmsVerificationInterface
{
    public function sendFourDigitOtp($phone);
    public function sendSixDigitOtp($phone);

}

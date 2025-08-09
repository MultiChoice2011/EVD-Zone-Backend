<?php

namespace App\Services\General\SmsVerification;

use App\Services\General\PaymentGateways\AlrajhiPaymentService;
use Exception;
use Illuminate\Support\Facades\Log;

class SmsVerificationServiceFactory
{
    public static function create($integrationConfig) {
        switch ($integrationConfig->name) {
            case 'msegat':
                return new MsegatService($integrationConfig);
                break;
            case 'smsmisr':
                return new SmsMisrService($integrationConfig);
                break;
            default:
                return false;
        }
    }

}

<?php

namespace App\Services\General\PaymentGateways;

use Exception;
use Illuminate\Support\Facades\Log;

class PaymentServiceFactory
{
    public static function create($integrationConfig) {
        switch ($integrationConfig->name) {
            case 'alrajhi_payment':
                return new AlrajhiPaymentService($integrationConfig);
                break;
            default:
                return false;
        }
    }

}

<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface ServiceTopupInterface
{
    public function AccountValidation($requestData);

    public function AccountTopUp($requestData, $coinsNumber=null);

}

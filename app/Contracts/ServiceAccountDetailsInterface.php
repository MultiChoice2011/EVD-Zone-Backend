<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface ServiceAccountDetailsInterface
{
    public function checkAccountDetails(array $data): array;

}

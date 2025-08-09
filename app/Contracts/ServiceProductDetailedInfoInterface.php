<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface ServiceProductDetailedInfoInterface
{
    public function productDetailedInfo($productId);
}

<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface OnlineShoppingInterface
{
//    public function productDetailedInfo($productId);
    public function purchaseProduct($requestData);
    public function orderDetails($requestData);
    public function orders($requestData);
}

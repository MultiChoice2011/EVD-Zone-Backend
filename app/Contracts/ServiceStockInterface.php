<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface ServiceStockInterface
{
    public function checkStock(int $productId, int $quantity): bool;

}

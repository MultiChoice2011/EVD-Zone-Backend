<?php

namespace App\Interfaces\ServicesInterfaces\Order;

use App\Models\Order;
use Illuminate\Http\Request;

interface OrderServiceInterface
{
    public function sendEmailsAndNotifications($authCustomer);

    public function orderWithSerials($orderProduct, $order);

    public function orderWithSerialsLiveIntegration($vendorProduct, $orderProduct);

    public function orderWithTopUp($orderProduct, $order);

    public function orderWithTopUpIntegration($vendorProduct, $orderProduct);

    public function increaseCustomerPoint(Order $order): void;

}

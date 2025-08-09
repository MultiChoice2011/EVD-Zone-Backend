<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderRequests\OrderTopUpStatusRequest;
use App\Services\Admin\OrderUserService;
use Illuminate\Http\Request;

class OrderUserController extends Controller
{

    public function __construct(private OrderUserService $orderUserService)
    {}

    public function pullTopUpOrder($orderId)
    {
        return $this->orderUserService->pullTopUpOrder($orderId);
    }

    public function changeStatusTopUp(OrderTopUpStatusRequest $request, $orderProductId)
    {
        return $this->orderUserService->changeStatusTopUp($request, $orderProductId);
    }

}

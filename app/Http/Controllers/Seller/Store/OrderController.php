<?php

namespace App\Http\Controllers\Seller\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\CreateOrderRequest;
use App\Services\Seller\Store\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(public OrderService $orderService){}
    public function index(Request $request){
        return $this->orderService->index($request);
    }
    public function store(CreateOrderRequest $request)
    {
        return $this->orderService->storeOrder($request);
    }
    public function show($id)
    {
        return $this->orderService->show($id);
    }
    public function orderIds()
    {
        return $this->orderService->orderIds();
    }
}

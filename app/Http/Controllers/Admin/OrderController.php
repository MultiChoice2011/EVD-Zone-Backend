<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderRequests\OrderRequest;
use App\Services\Admin\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public $orderService;

    /**
     * Order  Constructor.
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }


    /**
     * All Cats
     */
    public function index(Request $request)
    {
        return $this->orderService->getAllOrders($request);
    }


    /**
     *  Store Order
     */
    public function store(OrderRequest $request)
    {

        return $this->orderService->storeOrder($request);
    }

    /**
     * show the order
     *
     */
    public function show($id)
    {
        return $this->orderService->show($id);
    }



    /**
     * Update the order
     *
     */
    public function update(OrderRequest $request, int $id)
    {
        return $this->orderService->updateOrder($request, $id);
    }

    /**
     *
     * Delete Order Using ID.
     *
     */
    public function destroy(int $id)
    {
        return $this->orderService->deleteOrder($id);

    }
    /**
     *
     * Delete Brand Using ID.
     *
     */
    public function destroy_selected(Request $request)
    {
        return $this->orderService->destroy_selected($request);

    }
    /**
     *
     * trash sellerGroupLevel
     *
     */
    public function trash()
    {
        return $this->orderService->trash();

    }

    /**
     *
     * trash sellerGroupLevel
     *
     */
    public function restore(int $id)
    {
        return $this->orderService->restore($id);

    }
    public function update_status(Request $request, int $id)
    {
        return $this->orderService->update_status($request, $id);
    }
    public function save_notes(Request $request)
    {
        return $this->orderService->save_notes($request);
    }
    public function get_status(Request $request, $status)
    {
        return $this->orderService->get_status($request, $status);
    }
    public function get_customer_orders(int $customer_id)
    {
        return $this->orderService->get_customer_orders($customer_id);
    }
}

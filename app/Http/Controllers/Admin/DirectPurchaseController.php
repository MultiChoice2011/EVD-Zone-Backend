<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DirectPurchaseRequests\DirectPurchaseDeleteVendorRequest;
use App\Http\Requests\Admin\DirectPurchaseRequests\DirectPurchaseRequest;
use App\Http\Requests\Admin\DirectPurchaseRequests\DirectPurchaseStatusRequest;
use App\Services\Admin\DirectPurchaseService;
use Illuminate\Http\Request;

class DirectPurchaseController extends Controller
{

    public function __construct(private DirectPurchaseService $directPurchaseService)
    {}

    public function index(Request $request)
    {
        return $this->directPurchaseService->index($request);
    }

    public function store(DirectPurchaseRequest $request)
    {
        return $this->directPurchaseService->store($request);
    }

    public function changeStatus(DirectPurchaseStatusRequest $request, $directPurchaseId)
    {
        return $this->directPurchaseService->changeStatus($request, $directPurchaseId);
    }

    public function deleteVendor(DirectPurchaseDeleteVendorRequest $request)
    {
        return $this->directPurchaseService->deleteVendor($request);
    }

}

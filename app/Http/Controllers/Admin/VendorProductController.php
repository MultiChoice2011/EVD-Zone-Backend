<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VendorProductRequests\ProviderCostRequest;
use App\Http\Requests\Admin\VendorProductRequests\VendorProductRequest;
use App\Services\Admin\VendorProductService;
use Illuminate\Http\Request;


class VendorProductController extends Controller
{
    public function __construct(
        private VendorProductService $vendorProductService
    )
    {}

    public function index(Request $request)
    {
        return $this->vendorProductService->index($request);
    }


    public function store(VendorProductRequest $request)
    {
        return $this->vendorProductService->storeProduct($request);
    }


    public function update(VendorProductRequest $request, int $id)
    {
        return $this->vendorProductService->updateProduct($request, $id);
    }

    public function getProviderCost(ProviderCostRequest $request)
    {
        return $this->vendorProductService->getProviderCost($request);
    }

    public function delete(int $id)
    {
        return $this->vendorProductService->deleteProduct($id);
    }
    public function import(Request $request)
    {
        return $this->vendorProductService->import($request);
    }


}

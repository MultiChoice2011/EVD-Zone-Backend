<?php

namespace App\Repositories\Admin;

use App\Enums\VendorProductType;
use App\Jobs\VendorProductAutoPriorityJob;
use App\Models\VendorProduct;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Query\JoinClause;
use Prettus\Repository\Eloquent\BaseRepository;

class VendorProductRepository extends BaseRepository
{

    public function __construct(
        Application $app,
        private ProductRepository                   $productRepository,
        private DirectPurchaseRepository            $directPurchaseRepository,
        private DirectPurchasePriorityRepository    $directPurchasePriorityRepository,
    )
    {
        parent::__construct($app);
    }


    public function index($requestData)
    {
        $isPaginate = $requestData->input('is_paginate', 1);
        $vendorId = $requestData->input('vendor_id', null);
        $brandId = $requestData->input('brand_id', null);
        $productId = $requestData->input('product_id', null);
        $vendorProductId = $requestData->input('vendor_product_id', null);
        $productName = $requestData->input('product_name', null);
        $query = $this->model->query();
        $query->with(['product:id,brand_id','brand:id,status', 'vendor:id,name']);
        if (!is_null($vendorId)) {
            $query->where('vendor_id', $vendorId);
        }
        if (!is_null($brandId)) {
            $query->where('brand_id', $brandId);
        }
        if (!is_null($productId)) {
            $query->where('product_id', $productId);
        }
        if (!is_null($vendorProductId)) {
            $query->where('vendor_product_id', $vendorProductId);
        }
        if (!is_null($productName)) {
            $query->whereHas('product.translations', function ($q) use ($productName) {
                $q->where('name', 'LIKE', "%{$productName}%");
            });
        }
        if ($isPaginate)
            return $query->latest()->paginate(PAGINATION_COUNT_ADMIN);
        else
            return $query->latest()->get();
    }

    public function show($id)
    {
        return $this->model->where('id', $id)->first();
    }

    public function storeProduct($requestData)
    {
        $product = $this->productRepository->find($requestData->product_id);
        $vendorProduct = $this->showByVendorIdAndProductId($product->id, $requestData->vendor_id, $requestData->type);
        if ($vendorProduct) {
            return false;
        }
        $vendorProductStored = $this->model->create([
            'vendor_id' => $requestData->vendor_id,
            'product_id' => $requestData->product_id,
            'type' => $requestData->type,
            'brand_id' => $product->brand_id,
            'vendor_product_id' => $requestData->vendor_product_id,
            'provider_cost' => $requestData->provider_cost ?? 0.0000,
        ]);

        // Dispatch the job to vendor product AutoPriority
        VendorProductAutoPriorityJob::dispatch($vendorProductStored);

        return $vendorProductStored;
    }

    public function updateProduct($requestData, $id)
    {
        // check if exist with same input data not update
        $vendorProductCheck = $this->showByVendorIdAndProductId($requestData->product_id, $requestData->vendor_id, $requestData->type, $id);
        if ($vendorProductCheck) {
            return false;
        }
        $vendorProduct = $this->find($id);
        $product = $this->productRepository->find($requestData->product_id);
        $vendorProduct->vendor_id = $requestData->vendor_id;
        $vendorProduct->product_id = $requestData->product_id;
        $vendorProduct->type = $requestData->type;
        $vendorProduct->brand_id = $product->brand_id;
        $vendorProduct->vendor_product_id = $requestData->vendor_product_id;
        $vendorProduct->provider_cost = $requestData->provider_cost ?? 0.0000;
        $vendorProduct->save();

        // Dispatch the job to vendor product AutoPriority
        VendorProductAutoPriorityJob::dispatch($vendorProduct);

        return $vendorProduct;
    }

    public function getFirstByProductId($productId, $type, int $id = null)
    {
        $query = $this->model
            ->where('product_id', $productId)
            ->where('type', $type)
            ->first();
    }

    public function showByVendorIdAndProductId($productId, $vendorId, $type, int $id = null)
    {
        $vendorProductQuery = $this->model->query();
        if ($id){
            $vendorProductQuery->where('id', '!=', $id);
        }

        return $vendorProductQuery
            ->where('vendor_id', $vendorId)
            ->where('product_id', $productId)
            ->where('type', $type)
            ->first();
    }

    public function deleteProduct($id)
    {
        $vendorProduct = $this->find($id);
        if (! $vendorProduct) {
            return false;
        }
        $vendorProduct->delete();

        return true;
    }

    public function model(): string
    {
        return VendorProduct::class;
    }
}

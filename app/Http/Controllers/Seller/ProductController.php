<?php

namespace App\Http\Controllers\Seller;

use App\Enums\General\RequestSource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\ProductRequests\AccountOptionDetailsRequest;
use App\Http\Requests\Seller\ProductRequests\ProductListRequest;
use App\Http\Requests\Seller\ProductRequests\ShowProductsRequest;
use App\Services\Product\ProductAccountService;
use App\Services\Seller\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductService          $productService,
        private ProductAccountService   $productAccountService,
    )
    {}

    public function index(ProductListRequest $request)
    {
        return $this->productService->index($request);
    }

    public function productsByCategoryId(ShowProductsRequest $request, $categoryId)
    {
        return $this->productService->productsByCategoryId($request, $categoryId);
    }

    public function productByCategoryId($productId, $categoryId)
    {
        return $this->productService->productByCategoryId($productId, $categoryId);
    }

    public function search(Request $request)
    {
        return $this->productService->search($request);
    }

    public function checkOptionsAccount(AccountOptionDetailsRequest $request)
    {
        return $this->productAccountService->checkOptionsAccount($request->validated(), RequestSource::getSourceMobile());
    }
}

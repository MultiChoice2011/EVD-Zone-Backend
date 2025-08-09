<?php

namespace App\Http\Controllers\Seller\Store;

use App\Http\Controllers\Controller;
use App\Services\Seller\Store\BrandService;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function __construct(public BrandService $brandService){}
    public function getBrands()
    {
        return $this->brandService->getBrands();
    }
    public function index(Request $request)
    {
        return $this->brandService->index($request);
    }
    public function getCategories($brandId)
    {
        return $this->brandService->getCategories($brandId);
    }
    public function getProducts($categoryId)
    {
        return $this->brandService->getProducts($categoryId);
    }
}

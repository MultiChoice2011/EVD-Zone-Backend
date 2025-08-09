<?php

namespace App\Http\Controllers\Seller\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\CategoryRequests\SubcategoryRequest;
use App\Services\Seller\Store\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(public CategoryService $categoryService){}
    public function index()
    {
        return $this->categoryService->index();
    }

    public function getMainCategories()
    {
        return $this->categoryService->getMainCategories();
    }

    public function getSubCategories(SubcategoryRequest $request)
    {
        return $this->categoryService->getSubCategories($request);
    }

}

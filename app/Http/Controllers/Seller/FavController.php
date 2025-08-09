<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\AddProductToFavRequest;
use App\Http\Requests\Seller\FavoriteRequests\FavoriteProductsRequest;
use App\Services\Seller\Store\FavService;
use Illuminate\Http\Request;

class FavController extends Controller
{
    public function __construct(public FavService $favService){}
    public function store(AddProductToFavRequest $request)
    {
        $favoritable = auth('sellerApi')->user();
        // Check if the product is already favorited
        return $this->favService->store($request->validated(),$favoritable);
    }
    public function getProducts(FavoriteProductsRequest $request)
    {
        return $this->favService->getProducts($request);
    }
    public function destroy($productId, $categoryId)
    {
        return $this->favService->destroy($productId, $categoryId);
    }
}

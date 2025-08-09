<?php
namespace App\Services\Seller\Store;

use App\Http\Requests\Seller\FavoriteRequests\FavoriteProductsRequest;
use App\Http\Resources\Seller\FavCollection;
use App\Http\Resources\Seller\FavoriteResource;
use App\Http\Resources\Seller\ProductDetailsResource;
use App\Http\Resources\Seller\ProductResource;
use App\Models\Favorite;
use App\Models\Product;
use App\Repositories\Seller\FavoriteRepository;
use App\Repositories\Seller\ProductRepository;
use App\Traits\ApiResponseAble;

class FavService
{
    use ApiResponseAble;

    public function __construct(
        private FavoriteRepository $favouriteRepository,
        private ProductRepository $productRepository
    )
    {}

    public function store($data,$favoritable)
    {
        try{
            $product = $this->productRepository->showProductByIdAndCategoryId($data['product_id'], $data['category_id']);
            if (! $product)
                return $this->ApiErrorResponse(null, 'Product id for category id not found');

            $existingFavorite = Favorite::where([
                ['product_id', $data['product_id']],
                ['category_id', $data['category_id']],
                ['favoritable_type', get_class($favoritable)],
                ['favoritable_id', $favoritable->id],
            ])->first();
            if ($existingFavorite) {
                return $this->ApiErrorResponse([],trans("seller.fav.product_exist"));
            }
            // Add the product to the favoritable's favorites
            $this->favouriteRepository->store($data, $favoritable);

            return $this->ApiSuccessResponse(null, trans("seller.fav.product_added"));
        }catch(\Exception $e){
            return $this->ApiErrorResponse($e->getMessage(),trans("admin.general_error"));
        }
    }
    public function getProducts(FavoriteProductsRequest $request)
    {
        try{
            $authSeller = auth('sellerApi')->user();
            // Load the favorites along with related products
            $favorites = $this->favouriteRepository->favourites($request, $authSeller->id);
            return $this->ApiSuccessResponse(FavCollection::make($favorites));

        }catch(\Exception $e){
            return $this->ApiErrorResponse($e->getMessage(),trans("admin.general_error"));
        }
    }
    public function destroy($productId, $categoryId)
    {
        $user = auth('sellerApi')->user(); // Assuming the user is authenticated via API

        // Find the favorite record by product ID
        $favorite = $user->favorites()->where('product_id', $productId)->where('category_id', $categoryId)->first();

        if ($favorite) {
            // Delete the favorite entry
            $favorite->delete();

            return $this->ApiSuccessResponse([],'product delete success');
        }

        return $this->ApiErrorResponse([],'Product not found in favorites.');
    }
}

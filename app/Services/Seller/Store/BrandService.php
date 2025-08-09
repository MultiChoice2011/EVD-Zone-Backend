<?php
namespace App\Services\Seller\Store;

use App\Http\Resources\Seller\BrandsResource;
use App\Http\Resources\Seller\CategoriesResource;
use App\Http\Resources\Seller\ProductResource;
use App\Models\CategoryBrand;
use App\Repositories\Seller\BrandRepository;
use App\Repositories\Seller\CategoryRepository;
use App\Traits\ApiResponseAble;
class BrandService{
    use ApiResponseAble;
    public function __construct(
        public BrandRepository          $brandRepository,
    ){}
    public function getBrands()
    {
        try{
            $brands = $this->brandRepository->getBrandsWithoutPaginate();
            if($brands->count() > 0)
            {
                return $this->ApiSuccessResponse($brands);
            }
            return $this->listResponse([]);
        }catch(\Exception $exception){
            return $this->ApiErrorResponse($exception->getMessage(), trans('admin.general_error'));
        }
    }
    public function index($request)
    {
        try{
            $brands = $this->brandRepository->getAllBrands($request);
            return $this->ApiSuccessResponse($brands);
        }catch(\Exception $ex)
        {
            return $this->ApiErrorResponse($ex->getMessage(), trans('admin.general_error'));
        }
    }
    public function getCategories($brandId)
    {
        try{
            $categories = CategoryBrand::with('brand')->where('brand_id',$brandId)->get();
            if($categories->count() > 0)
                return $this->ApiSuccessResponse(CategoriesResource::collection($categories));
            return $this->ApiErrorResponse([],'data not found');

        }catch(\Exception $ex)
        {
            return $this->ApiErrorResponse($ex->getMessage(),trans('admin.general_error'));
        }
    }
    public function getProducts($categoryId)
    {
        try{
            // Fetch product IDs from the pivot table
            $productIds = $this->brandRepository->getProductIds($categoryId);
            // Fetch products using the retrieved IDs
            $products = $this->brandRepository->getProducts($productIds);
            if($products->count() > 0)
                return $this->ApiSuccessResponse(ProductResource::collection($products));
            return $this->ApiErrorResponse([],'data not found');
        }catch(\Exception $exception)
        {
            return $this->ApiErrorResponse($exception->getMessage(),trans('admin.general_error'));
        }
    }
}

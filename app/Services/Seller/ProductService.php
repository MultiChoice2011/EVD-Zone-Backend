<?php
namespace App\Services\Seller;

use App\Http\Resources\Seller\CategoryResource;
use App\Http\Resources\Seller\ProductDetailsResource;
use App\Http\Resources\Seller\ProductResource;
use App\Models\Product;
use App\Repositories\Seller\ProductRepository;
use App\Repositories\Seller\BrandRepository;
use App\Repositories\Seller\CategoryRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Support\Facades\DB;

class ProductService{

    use ApiResponseAble;

    public function __construct(
        public ProductRepository        $productRepository,
        public BrandRepository          $brandRepository,
        public CategoryRepository       $categoryRepository,
    ){}

    public function index($request)
    {
        try{
            $data = $this->productRepository->getAllProducts($request);
            return $this->ApiSuccessResponse(ProductResource::collection($data)->resource);
            // return $this->ApiErrorResponse([],trans("seller.products.not_found"));
        }catch (Exception $e){
            return $this->ApiErrorResponse($e->getMessage(),trans('admin.general_error'));
        }
    }

    public function productsByCategoryId($request, $categoryId)
    {
        try {
            DB::beginTransaction();
            $data = [];
            $brandId = $request->input('brand_id', null);
            $data['brand'] = $brandId ? $this->brandRepository->getBrandDetails($brandId) : null;
            $data['category'] = $this->categoryRepository->show($categoryId);
            if (! $data['category']){
                return $this->ApiErrorResponse(null, 'Product for category id not found');
            }
            $products = $this->productRepository->productsByCategory($request, $data['category']);
            $data['products'] = ProductDetailsResource::collection($products)->resource;
            if (!$data['products'])
                return $this->ApiErrorResponse();

            DB::commit();
            return $this->showResponse($data);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function productByCategoryId($productId, $categoryId)
    {
        try {
            DB::beginTransaction();
            $data = [];
            $data['category'] = new CategoryResource( $this->categoryRepository->show($categoryId) );
            $data['product'] = $this->productRepository->getProductDetailsByCategory($productId, $data['category']);
            if (!$data['product'])
                return $this->ApiErrorResponse();
            $data['product'] = new ProductDetailsResource($data['product']);

            DB::commit();
            return $this->showResponse($data);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function search($request)
    {
        return $this->productRepository->search($request);
    }
}

<?php

namespace App\Services\Admin;

use App\Helpers\FileUpload;
use App\Http\Requests\Admin\ProductRequests\ProductRequest;
use App\Http\Resources\Admin\ProductResource;
use App\Models\ProductImage;
use App\Repositories\Admin\AttributeRepository;
use App\Repositories\Admin\CategoryRepository;
use App\Repositories\Admin\LanguageRepository;
use App\Repositories\Admin\ProductRepository;
use App\Repositories\Admin\VendorRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductService
{

    use FileUpload, ApiResponseAble;

    private $productRepository;
    private $languageRepository;
    private $categoryRepository;
    private $attributeRepository;
    private $vendorRepository;

    public function __construct(ProductRepository $productRepository, LanguageRepository $languageRepository, CategoryRepository $categoryRepository
        , VendorRepository                        $vendorRepository, AttributeRepository $attributeRepository)
    {
        $this->productRepository = $productRepository;
        $this->languageRepository = $languageRepository;
        $this->categoryRepository = $categoryRepository;
        $this->attributeRepository = $attributeRepository;
        $this->vendorRepository = $vendorRepository;
    }

    /**
     *
     * All  Products.
     *
     */
    public function getAllProducts($request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $products = $this->productRepository->getAllProducts($request);
            if (count($products) > 0)
                return $this->listResponse(ProductResource::collection($products)->resource);
                //return $this->listResponse($products);
            else
                return $this->listResponse([]);
        } catch (Exception $e) {
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     *
     * All  Products.
     *
     */
    public function get_brand_products($brand_id): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $products = $this->productRepository->get_brand_products($brand_id);
            if (count($products) > 0)
                return $this->listResponse($products);
            else
                return $this->listResponse([]);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


    public function upload_image(Request $request)
    {
        $image_name = $request->file;
        $image = ProductImage::create(['image' => $image_name]);

        return $image->id;
    }

    /**
     *
     * Create New Product.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeProduct(ProductRequest $request): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        $data_request = $request->except('image');
        if (isset($request->image))
            $data_request['image'] = $request->image;
        $data_request_images = [];
        if (isset($request->images))
            $data_request_images = $this->uploadImages($request);


        $data_request['images'] = $data_request_images;

        $product = $this->productRepository->store($data_request);
        try {
            if ($product) {
                DB::commit();
                return $this->showResponse($product);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     *
     * Create New Product.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function serials(Request $request): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->except('file');
        if (isset($request->file))
            $data_request['file'] = $request->file;

        try {
            $product = $this->productRepository->serials($data_request);
            if ($product) {
                return $this->showResponse($product);
            }
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    /**
     *
     * Create New Product.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyPriceAll(Request $request): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->all();
        try {
            $product = $this->productRepository->applyPriceAll($data_request);
            if ($product) {
                return $this->showResponse($product);
            }
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    /**
     *
     * Create New Product.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyPriceAllGroups(Request $request): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->all();
        try {
            $product = $this->productRepository->applyPriceAllGroups($data_request);
            if ($product) {
                return $this->showResponse($product);
            }
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    /**
     *
     * Create New Product.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function prices(Request $request)
    {
        $data_request = $request->all();
        try {
            $productPrices = $this->productRepository->prices($data_request);
            if (! $productPrices['success']) {
                return $this->ApiErrorResponse(null, $productPrices['error']);
            }
            return $this->createdResponse($productPrices);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


    public function show($id): \Illuminate\Http\JsonResponse
    {
        try {
            $product = $this->productRepository->show($id);
            if (isset($product))
                return $this->showResponse($product);

            return $this->notFoundResponse();

        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Update Product.
     *
     * @param integer $product_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProduct(ProductRequest $request, int $product_id): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->except('image');
        if (isset($request->image))
            $data_request['image'] = $request->image;
        $data_request_images = [];
        if (isset($request->images))
            $data_request_images = $this->uploadImages($request);
        $data_request['images'] = $data_request_images;

        $product = $this->productRepository->update($data_request, $product_id);
        try {
            if ($product)
                return $this->showResponse($product);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Update Product.
     *
     * @param integer $product_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Request $request, int $product_id): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->all();

        try {
            $product = $this->productRepository->changeStatus($data_request, $product_id);
            if ($product)
                return $this->showResponse($product);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Delete Product.
     *
     * @param int $product_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProduct(int $product_id): \Illuminate\Http\JsonResponse
    {
        try {
            $product = $this->productRepository->show($product_id);
            if (!$product)
                return $this->notFoundResponse();
            if ($this->productRepository->destroy($product_id)) {
                return $this->ApiSuccessResponse([], 'Success');
            }
            return $this->ApiErrorResponse(null, __('admin.related_items'));

        }
        catch (QueryException $e) {
            if ($e->getCode() === '23000')
                return $this->ApiErrorResponse(null, __('admin.related_items'));
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
        catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function multiDelete($request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            $this->productRepository->multiDelete($request);

            DB::commit();
            return $this->ApiSuccessResponse(null, 'deleted Successfully...!');
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function delete_image_product(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            if (ProductImage::where('id', $id)->delete()) {
                return $this->ApiSuccessResponse([], 'Success');
            }
            return $this->ApiErrorResponse([], 'Failed to destroy product image');
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


    protected function uploadImages(Request $request)
    {
        $images = array();
        if ($files = $request->images) {
            foreach ($files as $file) {
                $name = $file;
                $images[] = $name;
            }
        }
        return $images;
    }
}

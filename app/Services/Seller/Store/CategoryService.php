<?php
namespace App\Services\Seller\Store;

use App\Http\Resources\Seller\CategoriesResource;
use App\Repositories\Seller\CategoryRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryService{
    use ApiResponseAble;
    public function __construct(public CategoryRepository $categoryRepository){}
    public function index()
    {
        $categories = $this->categoryRepository->getAllCategories();
        if($categories->count() > 0)
            return $this->ApiSuccessResponse($categories);
        return $this->ApiErrorResponse([],'data not found');
    }

    public function getMainCategories()
    {
        try {
            DB::beginTransaction();
            // get main categories
            $categories = $this->categoryRepository->getMainCategories();

            DB::commit();
            return $this->showResponse(CategoriesResource::collection($categories)->resource);
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function getSubCategories(Request $request)
    {
        try {
            DB::beginTransaction();
            // get sub categories
            $subCategories = $this->categoryRepository->getSubCategories($request);

            DB::commit();
            return $this->showResponse(CategoriesResource::collection($subCategories)->resource);
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


}

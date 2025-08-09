<?php

namespace App\Services\Admin;

use App\Helpers\FileUpload;
use App\Http\Requests\Admin\CategoryRequest;
use App\Http\Resources\Admin\CategoryResource;
use App\Repositories\Admin\CategoryRepository;
use App\Repositories\Admin\LanguageRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryService
{

    use FileUpload, ApiResponseAble;

    private $categoryRepository;
    private $languageRepository;

    public function __construct(CategoryRepository $categoryRepository, LanguageRepository $languageRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->languageRepository = $languageRepository;
    }

    /**
     *
     * All  Categories.
     *
     */
    public function getAllCategories($request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $categories = $this->categoryRepository->getAllCategories($request);
            if (count($categories) > 0)
                return $this->listResponse(CategoryResource::collection($categories)->resource);
            else
                return $this->listResponse([]);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     *
     * All  Categories.
     *
     */
    public function getAllCategoriesForm($request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $categories = $this->categoryRepository->getAllCategoriesForm($request);
            if (count($categories) > 0)
                return $this->listResponse(CategoryResource::collection($categories)->resource);
                //return $this->listResponse($categories);
            else
                return $this->listResponse([]);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


    /**
     *
     * Create New Category.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeCategory(CategoryRequest $request): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->except('image');
        if (isset($request->image))
            $data_request['image'] = $request->image;

        try {
            $category = $this->categoryRepository->store($data_request);
            if ($category)
                return $this->showResponse($category);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


    public function show($id): \Illuminate\Http\JsonResponse
    {
        try {
            $category = $this->categoryRepository->show($id);
            if (isset($category))
                //return $this->showResponse($category);
                return $this->showResponse(new CategoryResource($category));
            else
                return $this->notFoundResponse();
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Update Category.
     *
     * @param integer $category_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCategory(CategoryRequest $request, int $category_id): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->except('image');
        if (isset($request->image)){
            $data_request['image'] = $request->image;
        }

        if ($request->parent_id == $category_id){
            return $this->ApiErrorResponse(null, 'parent and category are the same');
        }
        try {
            $category = $this->categoryRepository->update($data_request, $category_id);
            if ($category)
                return $this->showResponse($category);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    /**
     * Update Category.
     *
     * @param integer $category_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_status(Request $request, int $category_id): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->all();

        try {
            $category = $this->categoryRepository->update_status($data_request, $category_id);
            if ($category)
                return $this->showResponse($category);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Delete Category.
     *
     * @param int $category_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCategory(int $category_id): \Illuminate\Http\JsonResponse
    {
        try {
            $category = $this->categoryRepository->show($category_id);
            if (!$category)
                return $this->notFoundResponse();
            $deleted = $this->categoryRepository->destroy($category_id);
            if (!$deleted)
                return $this->ApiErrorResponse(null, __('admin.related_items'));
            return $this->ApiSuccessResponse([], 'Success');
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function destroy_selected(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data_request = $request->all();
            $data_request = explode(',', $data_request['category_ids']);
            $this->categoryRepository->destroy_selected($data_request);

            return $this->ApiSuccessResponse([], 'Success');
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }

    }

    /**
     * restore Category.
     *
     * @param int $category_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(int $category_id): \Illuminate\Http\JsonResponse
    {
        try {
            $category = $this->categoryRepository->restore($category_id);
            if (!$category)
                return $this->ApiErrorResponse(null, __('admin.unable_restore'));
            return $this->showResponse($category);

        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * trash Category.
     *
     * @param int $category_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function trash()
    {

        try {
            $trashes = $this->categoryRepository->trash();
            if (count($trashes) > 0)
                return $this->listResponse($trashes);
            else
                return $this->listResponse([]);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
}

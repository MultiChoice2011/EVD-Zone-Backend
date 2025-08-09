<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\CategoryRequest;
use App\Services\Admin\CategoryService;

class CategoryController extends Controller
{
    public $categoryService;

    /**
     * Category  Constructor.
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }


    /**
     * All Cats
     */
    public function index(Request $request)
    {
        return $this->categoryService->getAllCategories($request);
    }

    /**
     * All Cats
     */
    public function getAllCategoriesForm(Request $request)
    {
        return $this->categoryService->getAllCategoriesForm($request);
    }


    /**
     *  Store Category
     */
    public function store(CategoryRequest $request)
    {

        return $this->categoryService->storeCategory($request);
    }

    /**
     * show the category..
     *
     */
    public function show($id)
    {
        return $this->categoryService->show($id);
    }


    /**
     * Update the category..
     *
     */
    public function update(CategoryRequest $request, int $id)
    {
        return $this->categoryService->updateCategory($request, $id);
    }

    /**
     * Update the category..
     *
     */
    public function update_status(Request $request, int $id)
    {
        return $this->categoryService->update_status($request, $id);
    }

    /**
     *
     * Delete Category Using ID.
     *
     */
    public function destroy(int $id)
    {
        return $this->categoryService->deleteCategory($id);

    }
    public function destroy_selected(Request $request)
    {
        return $this->categoryService->destroy_selected($request);

    }

    /**
     *
     * trash Category
     *
     */
    public function trash()
    {
        return $this->categoryService->trash();

    }

    /**
     *
     * trash Category
     *
     */
    public function restore(int $id)
    {
        return $this->categoryService->restore($id);

    }

}
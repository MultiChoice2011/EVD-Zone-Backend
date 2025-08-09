<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BrandRequests\BrandRequest;
use App\Http\Requests\Admin\BrandRequests\BrandStatusRequest;
use App\Services\Admin\BrandService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Prettus\Validator\Exceptions\ValidatorException;


class BrandController extends Controller
{
    public $brandService;

    /**
     * Brand  Constructor.
     */
    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }


    /**
     * All Brand
     */
    public function index(Request $request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        return $this->brandService->getAllBrands($request);
    }

    public function getHomeSectionsBrands(Request $request): JsonResponse
    {
        return $this->brandService->getAllBrands($request);
    }


    /**
     *  Store Brand
     */
    public function store(BrandRequest $request): \Illuminate\Http\JsonResponse
    {

        return $this->brandService->storeBrand($request);
    }

    /**
     * show the brand..
     *
     */
    public function show(int $id): \Illuminate\Http\JsonResponse
    {
        return $this->brandService->show($id);
    }

    /**
     * Update the brand..
     *
     * @throws ValidatorException
     */
    public function update(BrandRequest $request, int $id): \Illuminate\Http\JsonResponse
    {
        return $this->brandService->updateBrand($request, $id);
    }


    public function changeStatus(BrandStatusRequest $request, int $id): \Illuminate\Http\JsonResponse
    {
        return $this->brandService->changeStatus($request, $id);
    }

    /**
     *
     * Delete Brand Using ID.
     *
     */
    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        return $this->brandService->deleteBrand($id);

    }
    /**
     *
     * Delete Brand Using ID.
     *
     */
    public function destroy_selected(Request $request): \Illuminate\Http\JsonResponse
    {
        return $this->brandService->destroy_selected($request);

    }

}

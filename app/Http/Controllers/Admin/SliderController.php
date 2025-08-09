<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SliderRequests\SliderOrderRequest;
use App\Http\Requests\Admin\SliderRequests\SliderRequest;
use App\Http\Requests\Admin\SliderRequests\SliderStatusRequest;
use App\Services\Admin\SliderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class SliderController extends Controller
{
    public function __construct(
        private SliderService $sliderService
    )
    {}


    public function index(Request $request): JsonResponse
    {
        return $this->sliderService->index($request);
    }

    public function store(SliderRequest $request): JsonResponse
    {
        return $this->sliderService->storeSlider($request);
    }

    public function show($id): JsonResponse
    {
        return $this->sliderService->show($id);
    }

    public function update(SliderRequest $request, $id): JsonResponse
    {
        return $this->sliderService->updateSlider($request, $id);
    }

    public function changeStatus(SliderStatusRequest $request, $id): JsonResponse
    {
        return $this->sliderService->changeStatus($request, $id);
    }

    public function changeOrder(SliderOrderRequest $request): JsonResponse
    {
        return $this->sliderService->changeOrder($request);
    }

    public function delete($id): JsonResponse
    {
        return $this->sliderService->deleteSlider($id);
    }


}

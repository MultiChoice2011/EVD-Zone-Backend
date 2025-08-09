<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomeSectionRequests\HomeSectionRequest;
use App\Http\Requests\Admin\HomeSectionRequests\SectionOrderRequest;
use App\Http\Requests\Admin\HomeSectionRequests\SectionStatusRequest;
use App\Services\Admin\HomeSectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class HomeSectionController extends Controller
{
    public function __construct(
        private HomeSectionService $homeSectionService
    )
    {}


    public function index(Request $request): JsonResponse
    {
        return $this->homeSectionService->index($request);
    }

    public function store(HomeSectionRequest $request): JsonResponse
    {
        return $this->homeSectionService->storeSection($request);
    }

    public function show($id): JsonResponse
    {
        return $this->homeSectionService->show($id);
    }

    public function update(HomeSectionRequest $request, $id): JsonResponse
    {
        return $this->homeSectionService->updateSection($request, $id);
    }

    public function changeStatus(SectionStatusRequest $request, $id): JsonResponse
    {
        return $this->homeSectionService->changeStatus($request, $id);
    }

    public function changeOrder(SectionOrderRequest $request): JsonResponse
    {
        return $this->homeSectionService->changeOrder($request);
    }

    public function delete($id): JsonResponse
    {
        return $this->homeSectionService->deleteSection($id);
    }


}

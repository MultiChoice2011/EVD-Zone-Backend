<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IntegrationRequests\IntegrationRequest;
use App\Http\Requests\Admin\IntegrationRequests\IntegrationStatusRequest;
use App\Http\Requests\Admin\RatingRequests\ReplayRequest;
use App\Services\Admin\IntegrationService;
use App\Services\Admin\RatingService;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    public function __construct(private IntegrationService $integrationService)
    {}

    public function index()
    {
        return $this->integrationService->index();
    }

    public function updateIntegration(IntegrationRequest $request, $integrationId)
    {
        return $this->integrationService->updateIntegration($request, $integrationId);
    }

    public function changeStatus(IntegrationStatusRequest $request, $integrationId)
    {
        return $this->integrationService->changeStatus($request, $integrationId);
    }




}

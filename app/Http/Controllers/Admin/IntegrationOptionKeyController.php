<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IntegrationRequests\IntegrationKeysFilterRequest;
use App\Services\Integration\IntegrationOptionKeyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IntegrationOptionKeyController extends Controller
{

    public function __construct(private readonly IntegrationOptionKeyService $integrationOptionKeyService)
    {
    }

    public function index(IntegrationKeysFilterRequest $request): JsonResponse
    {
        return $this->integrationOptionKeyService->index($request);
    }

}

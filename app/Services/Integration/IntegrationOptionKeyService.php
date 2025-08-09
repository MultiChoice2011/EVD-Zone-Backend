<?php

namespace App\Services\Integration;

use App\Repositories\Integration\IntegrationOptionKeyRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IntegrationOptionKeyService
{

    use ApiResponseAble;

    public function __construct(
        private IntegrationOptionKeyRepository $integrationOptionKeyRepository,
    )
    {
    }

    public function index($request): JsonResponse
    {
        try {
            $keys = $this->integrationOptionKeyRepository->index($request);
            return $this->showResponse($keys);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

}

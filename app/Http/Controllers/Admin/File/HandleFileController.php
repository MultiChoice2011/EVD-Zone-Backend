<?php

namespace App\Http\Controllers\Admin\File;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FileRequests\DestroyFileRequest;
use App\Services\File\HandleFileService;
use Illuminate\Http\JsonResponse;

class HandleFileController extends Controller
{
    public function __construct(private HandleFileService $handleFileService)
    {}

    public function destroyFile(DestroyFileRequest $request): JsonResponse
    {
        return $this->handleFileService->destroyFile($request->validated());
    }
}

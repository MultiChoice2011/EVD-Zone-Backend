<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NotificationTokenRequests\FirebaseStoreRequest;
use App\Services\Admin\NotificationTokenService;
use App\Services\General\NotificationServices\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationTokenController extends Controller
{
    public function __construct(
        private NotificationTokenService $notificationTokenService,
        private FirebaseService $firebaseService
    )
    {}

    public function firebaseStore(FirebaseStoreRequest $request): JsonResponse
    {
        return $this->notificationTokenService->firebaseStore($request);
    }


}

<?php

namespace App\Services\Admin;

use App\Repositories\Admin\NotificationRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    use ApiResponseAble;

    public function __construct(
        private NotificationRepository $notificationRepository
    )
    {}


    public function index(): JsonResponse
    {
        try {
            DB::beginTransaction();
            // get authed customer
            $authAdmin = Auth::guard('adminApi')->user();
            $notifications = $this->notificationRepository->index($authAdmin->id);

            DB::commit();
            return $this->ApiSuccessResponse($notifications, "Notifications Data");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function count(): JsonResponse
    {
        try {
            DB::beginTransaction();
            $notificationsCount = 0;
            $authAdmin = Auth::guard('adminApi')->user() ?? null;
            if ($authAdmin)
                $notificationsCount = $this->notificationRepository->NotificationsCount($authAdmin->id);

            DB::commit();
            return $this->ApiSuccessResponse($notificationsCount, "Notifications Count");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function read($notificationId): JsonResponse
    {
        try {
            DB::beginTransaction();
            // get authed customer
            $authAdmin = Auth::guard('adminApi')->user();
            $notification = $this->notificationRepository->read($notificationId, $authAdmin->id);
            if (! $notification)
                return $this->ApiErrorResponse(null, 'Notification id not found');

            DB::commit();
            return $this->ApiSuccessResponse(null, "Read Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

}

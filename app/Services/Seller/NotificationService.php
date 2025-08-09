<?php

namespace App\Services\Seller;

use App\Repositories\Seller\NotificationRepository;
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
            // get authed seller
            $authSeller = Auth::guard('sellerApi')->user();
            $notifications = $this->notificationRepository->index($authSeller->id);

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
            $authSeller = Auth::guard('sellerApi')->user() ?? null;
            if ($authSeller)
                $notificationsCount = $this->notificationRepository->NotificationsCount($authSeller->id);

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
            // get authed seller
            $authSeller = Auth::guard('sellerApi')->user();
            $notification = $this->notificationRepository->read($notificationId, $authSeller->id);
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

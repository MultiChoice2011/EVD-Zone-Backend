<?php

namespace App\Services\Admin;

use App\Enums\OrderProductStatus;
use App\Notifications\CustomNotification;
use App\Notifications\OrderProductStatusNotification;
use App\Repositories\Admin\SellerRepository;
use App\Repositories\Admin\OrderProductRepository;
use App\Repositories\Admin\OrderRepository;
use App\Repositories\Admin\OrderUserRepository;
use App\Services\General\NotificationServices\EmailsAndNotificationService;
use App\Services\General\NotificationServices\FirebaseService;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class OrderUserService
{
    use ApiResponseAble;

    public function __construct(
        private OrderRepository                     $orderRepository,
        private OrderUserRepository                 $orderUserRepository,
        private OrderProductRepository              $orderProductRepository,
        private FirebaseService                     $firebaseService,
        private SellerRepository                    $sellerRepository,
        private EmailsAndNotificationService        $emailsAndNotificationService,
    )
    {}


    public function pullTopUpOrder($orderId): JsonResponse
    {
        try {
            DB::beginTransaction();
            $authAdmin = Auth::guard('adminApi')->user();
            $order = $this->orderRepository->orderById($orderId);
            if (! $order)
                return $this->notFoundResponse();

            $orderUser = $this->orderUserRepository->showByOrderId($order->id);
            if ($orderUser)
                return $this->ApiErrorResponse(null, 'This order already pulled up.');

            $this->orderUserRepository->store($order->id, $authAdmin->id);
            $this->orderProductRepository->changeTopUpStatusByOrderId($order->id);

            DB::commit();
            return $this->ApiSuccessResponse(null, "Pulled up Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }

    }

    public function changeStatusTopUp($request, $orderProductId): JsonResponse
    {
        try {
            DB::beginTransaction();
            $authAdmin = Auth::guard('adminApi')->user();
            $orderProduct = $this->orderProductRepository->show($orderProductId);
            if (! $orderProduct)
                return $this->notFoundResponse();

            $orderUser = $this->orderUserRepository->checkByOrderIdAndUserId($orderProduct->order_id, $authAdmin->id);
            if (! $orderUser)
                return $this->ApiErrorResponse(null, __('admin.general_error'));

            $request->user_id = $authAdmin->id;
            $orderProductStatus = $this->orderProductRepository->changeStatusTopUp($request, $orderProductId);
            if (! $orderProductStatus)
                return $this->ApiErrorResponse(null, __('admin.general_error'));

            // Here send Notification for this seller
            $requestData = [
                'notificationClass' => CustomNotification::class,
                'type' => 'order',
                'type_id' => $orderProduct->order_id,
            ];
            if ($request->status == OrderProductStatus::getTypeCompleted()) {
                $requestData['notification_translations'] = 'seller_order_topup_status.complete_status';
            }elseif ($request->status == OrderProductStatus::getTypeRejected()) {
                $requestData['notification_translations'] = 'seller_order_topup_status.reject_status';
            }

            if ($orderProduct->order?->owner){
                $this->emailsAndNotificationService->sendSellerNotifications($orderProduct->order->owner, $requestData);
            }

            DB::commit();
            return $this->ApiSuccessResponse(null, "Changed Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }

    }




}

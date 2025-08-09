<?php

namespace App\Listeners;

use App\Events\WalletRecharged;
use App\Http\Resources\Seller\RechargeBalanceResource;
use App\Notifications\CustomNotification;
use App\Services\General\NotificationServices\EmailsAndNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWalletRechargeNotificationListner
{
    /**
     * Create the event listener.
     */
    public function __construct(protected EmailsAndNotificationService $emailsAndNotificationService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WalletRecharged $event): void
    {
        $wallet = $event->wallet;
        // send notification for admin
        $requestData = [
            'notification_permission_name' => 'notifications-new-orders',
            'notificationClass' => CustomNotification::class,
            'notification_translations' => 'seller_recharge_wallet',
            'type' => 'Wallet',
            'type_id' => $wallet->id,
        ];
        $this->emailsAndNotificationService->sendAdminNotifications($requestData);
    }
}

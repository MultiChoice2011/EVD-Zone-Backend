<?php

namespace App\Listeners;

use App\Events\SellerRegisterd;
use App\Services\General\NotificationServices\EmailsAndNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAdminNotificationListner
{
    /**
     * Create the event listener.
     */
    public function __construct(protected EmailsAndNotificationService $emailsAndNotificationService)
    {

    }

    /**
     * Handle the event.
     */
    public function handle(SellerRegisterd $event): void
    {
        $seller = $event->seller;

        $requestData = [
            'notification_permission_name' => 'notifications-new-orders',
            'notificationClass' => \App\Notifications\CustomNotification::class,
            'notification_translations' => 'seller_register',
            'type' => 'seller',
            'type_id' => $seller->id,
        ];

        $this->emailsAndNotificationService->sendAdminNotifications($requestData);
    }
}

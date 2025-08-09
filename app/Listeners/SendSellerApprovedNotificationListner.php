<?php

namespace App\Listeners;

use App\Events\SellerApproved;
use App\Notifications\CustomNotification;
use App\Services\General\NotificationServices\EmailsAndNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendSellerApprovedNotificationListner
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
    public function handle(SellerApproved $event): void
    {
        $seller = $event->seller;
        $type = $event->type;
        // send notification for admin
        $requestData = [
            'notificationClass' => CustomNotification::class,
            'notification_translations' => $type == 'rejected' ? 'seller_rejected_status' : 'seller_approved_status',
            'type' => 'seller',
            'type_id' => $seller->id,
        ];
        $this->emailsAndNotificationService->sendSellerNotifications($seller,$requestData);
    }
}

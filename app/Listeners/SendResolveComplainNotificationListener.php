<?php

namespace App\Listeners;

use App\Events\ResolveComplain;
use App\Notifications\CustomNotification;
use App\Services\General\NotificationServices\EmailsAndNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendResolveComplainNotificationListener
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
    public function handle(ResolveComplain $event): void
    {
        $complain = $event->complain;
        $seller = $event->complain->customer;
        // send notification for admin
        $requestData = [
            'notificationClass' => CustomNotification::class,
            'notification_translations' => 'seller_resolve_complain',
            'type' => 'SupportTicket',
            'type_id' => $complain->id,
        ];
        $this->emailsAndNotificationService->sendSellerNotifications($seller,$requestData);
    }
}

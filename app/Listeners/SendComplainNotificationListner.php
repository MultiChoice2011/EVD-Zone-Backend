<?php

namespace App\Listeners;

use App\Events\CreateComplain;
use App\Notifications\CustomNotification;
use App\Services\General\NotificationServices\EmailsAndNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendComplainNotificationListner
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
    public function handle(CreateComplain $event): void
    {
        $ticket = $event->ticket;
        $requestData = [
            'notification_permission_name' => 'notifications-new-orders',
            'notificationClass' => CustomNotification::class,
            'notification_translations' => 'seller_create_complain',
            'type' => 'SupportTicket',
            'type_id' => $ticket->id,
        ];
        $this->emailsAndNotificationService->sendAdminNotifications($requestData);
    }
}

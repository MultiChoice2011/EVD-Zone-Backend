<?php

namespace App\Services\Order\Helpers;



use App\Jobs\EmailJobs\EmailMessageJob;
use App\Jobs\NotificationJobs\NotificationMessageJob;
use App\Mail\AdminOrderCreatedEmail;
use App\Mail\OrderCreatedEmail;
use App\Models\Seller;
use App\Models\Order;
use App\Models\User;
use App\Notifications\CustomNotification;

class OrderDependenciesHelper
{

    public function __construct(
    )
    {}

    /**
     * @param Order $order
     * @return void
     */
    public static function executeDependencies(Order $order): void
    {
        // prepare data for messaging to admin
        $adminRequestData = [
            'to'  => User::class,
            'notification_permission_name' => 'notifications-new-customers',
            'notificationClass' => CustomNotification::class,
            'notification_translations' => 'admin_order_created',
            'type' => 'order',
            'type_id' => $order->id,
            //////////////
            'emailClass' => AdminOrderCreatedEmail::class,
            'emailData' => ['order' => $order],
        ];
        // send emails
        // EmailMessageJob::dispatch($adminRequestData);             Stopped until now
        // send notifications
        NotificationMessageJob::dispatch($adminRequestData);

        // prepare data for messaging to Customer
        $customerRequestData = [
            'to'  => Seller::class,
            'email' => $order->customer?->email ?? null,
            'notificationClass' => CustomNotification::class,
            'notification_translations' => 'admin_order_created',
            'type' => 'order',
            'type_id' => $order->id,
            //////////////
            'emailClass' => OrderCreatedEmail::class,
            'emailData' => ['order' => $order],
        ];
        // send emails
        // EmailMessageJob::dispatch($customerRequestData);             Stopped until now

        // send whatsapp message
        // SendWhatsAppMessage::dispatch($order->customer?->phone, trans("whatsapp.purchase_done", ['order_id' => $order?->id]))->delay(now()->addSeconds(5));
        // SendWhatsAppMessage::dispatch($order->customer?->phone, trans("whatsapp.order_product_serial"), $order)->delay(now()->addSeconds(45));

    }
}


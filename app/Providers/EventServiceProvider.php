<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \App\Events\OtpRequested::class => [
            \App\Listeners\SendOtpEmailListener::class,
        ],
        \App\Events\SellerRegisterd::class => [
            \App\Listeners\SendAdminNotificationListner::class,
        ],
        \App\Events\WalletRecharged::class => [
            \App\Listeners\SendWalletRechargeNotificationListner::class,
        ],
        \App\Events\CreateComplain::class => [
            \App\Listeners\SendComplainNotificationListner::class,
        ],
        \App\Events\SellerApproved::class => [
            \App\Listeners\SendSellerApprovedNotificationListner::class,
        ],
        \App\Events\ResolveComplain::class => [
            \App\Listeners\SendResolveComplainNotificationListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

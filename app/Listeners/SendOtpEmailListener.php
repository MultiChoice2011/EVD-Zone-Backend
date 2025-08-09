<?php

namespace App\Listeners;

use App\Events\OtpRequested;
use App\Jobs\SendOtpEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOtpEmailListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OtpRequested $event): void
    {
        dispatch(new SendOtpEmail($event->email, $event->otp));
    }
}

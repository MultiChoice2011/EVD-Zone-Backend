<?php

namespace App\Jobs\NotificationJobs;

use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Notification;
use App\Services\General\NotificationServices\FirebaseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Exception;

class SendDatabaseNotificationJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private $recipient,
        private array $data
    )
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // send database message
        Notification::send($this->recipient, new $this->data['notificationClass']($this->data));
    }
}

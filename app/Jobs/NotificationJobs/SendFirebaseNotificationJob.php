<?php

namespace App\Jobs\NotificationJobs;

use App\Services\General\NotificationServices\FirebaseService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Exception;

class SendFirebaseNotificationJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $topic,
        private array $data
    )
    {}

    /**
     * Execute the job.
     */
    public function handle(FirebaseService $firebaseService): void
    {
        // send firebase message
        $firebaseService->sendNotification(
            $this->topic,
            __('notifications.' . $this->data['notification_translations'] . '.title', ['id' => $this->data['type_id']]),
            __('notifications.' . $this->data['notification_translations'] . '.body', ['id' => $this->data['type_id']])
        );
    }
}

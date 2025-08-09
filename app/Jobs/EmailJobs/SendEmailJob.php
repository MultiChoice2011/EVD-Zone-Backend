<?php

namespace App\Jobs\EmailJobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Exception;

class SendEmailJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string      $email,
        private string      $emailClass,
        private array       $emailData,
    )
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // send email message
        try {
            Mail::to($this->email)->send( new $this->emailClass( $this->emailData) );
        } catch (Exception $e) {
            Log::info($e);
            Log::info($e->getMessage());
        }
    }
}

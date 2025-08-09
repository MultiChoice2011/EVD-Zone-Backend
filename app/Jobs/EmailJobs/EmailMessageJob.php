<?php

namespace App\Jobs\EmailJobs;

use App\Models\Seller;
use App\Models\User;
use Exception;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EmailMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private array $data)
    {
        // $this->onQueue('email-messaging');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // check if email for admins or customers
        if ($this->data['to'] == User::class &&  $this->data['notification_permission_name']) {
            // get all admins that have permissions to send emails
            $admins = User::where(function ($query) {
                $query->whereHas('roles', function ($roleQuery) {
                    $roleQuery->where('name', 'Super Admin');
                })
                ->orWhereHas('permissions', function ($permissionQuery){
                    $permissionQuery->where('name', $this->data['notification_permission_name']);
                });
            })->get();
            // send email for every admin that has this permission
            if ($admins->isNotEmpty()) {
                // get a batch of jobs
                $jobs = $admins->map(function ($admin) {
                    return new SendEmailJob($admin->email, $this->data['emailClass'], $this->data['emailData']);
                });
                // use Bus tech.
                Bus::batch($jobs)
                    ->then(function (Batch $batch) {
                        Log::info('All admin emails sent successfully.');
                    })
                    ->catch(function (Batch $batch, Throwable $e) {
                        Log::error('A job in the batch failed.', ['error' => $e->getMessage()]);
                    })
                    ->finally(function (Batch $batch) {
                        Log::info('Admin email batch processing completed.');
                    })
                    ->dispatch();
            }
        }
        elseif ($this->data['to'] == Seller::class &&  $this->data['email']) {
            // send email directly to a single customer
            SendEmailJob::dispatch($this->data['email'], $this->data['emailClass'], $this->data['emailData']);
        }
        else{
            return;
        }

    }

}

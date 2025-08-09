<?php

namespace App\Providers;

use App\Services\General\SmsVerification\MsegatService;
use App\Services\General\SmsVerification\SmsMisrService;
use Illuminate\Support\ServiceProvider;

class SmsVerificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
//        $this->app->bind(SmsMisrService::class);
//        $this->app->bind(MsegatService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

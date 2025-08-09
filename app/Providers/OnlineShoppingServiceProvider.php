<?php

namespace App\Providers;

use App\Services\General\OnlineShoppingIntegration\LikeCardService;
use App\Services\General\OnlineShoppingIntegration\OneCardService;
use Illuminate\Support\ServiceProvider;

class OnlineShoppingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
//        $this->app->bind(LikeCardService::class, function ($app) {
//            return new LikeCardService();
//        });

//        $this->app->bind(OneCardService::class);
//        $this->app->bind(LikeCardService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

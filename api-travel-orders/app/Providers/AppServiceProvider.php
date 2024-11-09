<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(\App\Services\Auth\IAuthService::class, \App\Services\Auth\AuthService::class);
        $this->app->bind(\App\Services\Travel\ITravelOrderService::class, \App\Services\Travel\TravelOrderService::class);
        $this->app->bind(\App\Services\Travel\IOrderStatusService::class, \App\Services\Travel\OrderStatusService::class);
    }
}

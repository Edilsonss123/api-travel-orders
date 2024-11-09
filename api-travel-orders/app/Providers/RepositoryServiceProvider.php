<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
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
        $this->app->bind(\App\Repositories\User\IUserRepository::class, \App\Repositories\User\UserRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Travel\ITravelOrderRepository::class, \App\Repositories\Travel\TravelOrderRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\Travel\IOrderStatusRepository::class, \App\Repositories\Travel\OrderStatusRepositoryEloquent::class);
    }
}

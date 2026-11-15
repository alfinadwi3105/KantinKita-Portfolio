<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Broker\Penerbit;
use App\Broker\PenerbitRabbit;
use App\Broker\PenerbitUji;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(Penerbit::class, fn () =>
            config('broker.driver') === 'uji'
                ? new PenerbitUji()
                : new PenerbitRabbit());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

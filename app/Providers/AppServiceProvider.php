<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AuthenticationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar servicio de autenticación como singleton
        $this->app->singleton(AuthenticationService::class, function ($app) {
            return new AuthenticationService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

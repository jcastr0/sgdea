<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Services\AuthenticationService;
use App\Models\Tenant;

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
        // Compartir el tenant del usuario autenticado con todas las vistas
        View::composer('*', function ($view) {
            $tenant = null;
            $themeConfig = null;

            if (Auth::check()) {
                $user = Auth::user();

                // Cargar el tenant con su configuración de tema
                if ($user->tenant_id) {
                    $tenant = Tenant::with('themeConfiguration')->find($user->tenant_id);
                    $themeConfig = $tenant?->themeConfiguration;
                }
            }

            // Solo compartir si no están ya definidas
            if (!$view->offsetExists('tenant')) {
                $view->with('tenant', $tenant);
            }
            if (!$view->offsetExists('themeConfig')) {
                $view->with('themeConfig', $themeConfig);
            }
        });
    }
}

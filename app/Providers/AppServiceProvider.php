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
            $tenantPrimaryColor = '#1a56db';
            $tenantSecondaryColor = '#1e3a5f';

            if (Auth::check()) {
                $user = Auth::user();

                // Cargar el tenant directamente (sin relaciones de tema)
                if ($user->tenant_id) {
                    $tenant = Tenant::find($user->tenant_id);

                    // Obtener colores DIRECTAMENTE del tenant
                    if ($tenant) {
                        $tenantPrimaryColor = $tenant->primary_color ?? '#1a56db';
                        $tenantSecondaryColor = $tenant->secondary_color ?? '#1e3a5f';
                    }
                }
            }

            // Solo compartir si no están ya definidas
            if (!$view->offsetExists('tenant')) {
                $view->with('tenant', $tenant);
            }
            if (!$view->offsetExists('tenantPrimaryColor')) {
                $view->with('tenantPrimaryColor', $tenantPrimaryColor);
            }
            if (!$view->offsetExists('tenantSecondaryColor')) {
                $view->with('tenantSecondaryColor', $tenantSecondaryColor);
            }
        });
    }
}

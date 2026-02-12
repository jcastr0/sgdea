<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        /**
         * Directiva @canAccess('permiso')
         * Muestra contenido solo si el usuario tiene el permiso
         */
        Blade::if('canAccess', function (string $permission) {
            return auth()->check() && auth()->user()->hasPermission($permission);
        });

        /**
         * Directiva @canAccessAny('permiso1', 'permiso2')
         * Muestra contenido si tiene al menos uno de los permisos
         */
        Blade::if('canAccessAny', function (...$permissions) {
            return auth()->check() && auth()->user()->hasAnyPermission($permissions);
        });

        /**
         * Directiva @hasRole('role')
         * Muestra contenido si el usuario tiene el rol
         */
        Blade::if('hasRole', function (string $roleName) {
            return auth()->check() && auth()->user()->role && auth()->user()->role->name === $roleName;
        });

        /**
         * Componente: permiso requerido para botón/enlace
         */
        Blade::component('permission-button', \App\View\Components\PermissionButton::class);
    }
}


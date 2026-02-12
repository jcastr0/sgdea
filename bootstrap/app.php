<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middlewares globales
        $middleware->append(\App\Http\Middleware\DetectTenant::class);
        $middleware->append(\App\Http\Middleware\CheckSetupStatus::class);

        // Middleware para rutas protegidas
        $middleware->alias([
            'verify.tenant' => \App\Http\Middleware\VerifyTenantAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

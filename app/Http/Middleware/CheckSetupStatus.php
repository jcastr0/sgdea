<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSetupStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $setupCompleted = file_exists(storage_path('.setup_completed'));

        // Si el setup está completado, permitir acceso normal (excepto a /setup)
        if ($setupCompleted) {
            // Bloquear acceso a rutas de setup si ya está configurado
            if (str_starts_with($request->path(), 'setup')) {
                abort(403, 'El sistema ya ha sido configurado.');
            }
            return $next($request);
        }

        // Setup NO completado - verificar si debe redirigir
        // Excepciones: permitir rutas de setup y assets
        $setupRoutes = ['setup', 'setup/process', 'setup/test-db-connection', 'setup/validate-access', 'setup/go-back'];
        $assetPaths = ['css', 'js', 'images', 'fonts', 'vendor', 'build', 'favicon'];
        $otherExceptions = ['health', 'api/health', '', 'up', 'login', 'storage'];

        $currentPath = $request->path();

        // Permitir rutas de setup
        if (in_array($currentPath, $setupRoutes) || str_starts_with($currentPath, 'setup/')) {
            return $next($request);
        }

        // Permitir assets
        foreach ($assetPaths as $assetPath) {
            if (str_starts_with($currentPath, $assetPath)) {
                return $next($request);
            }
        }

        // Permitir otras excepciones
        if (in_array($currentPath, $otherExceptions)) {
            return $next($request);
        }

        // Redirigir a setup
        return redirect()->route('setup.show');
    }
}


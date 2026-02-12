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

        // Si el setup está completado y acceden a /setup, bloquear
        if ($setupCompleted && $request->path() === 'setup') {
            abort(403, 'El sistema ya ha sido configurado.');
        }

        // Si el setup NO está completado y acceden a otro lado (que no sea /setup), redirigir a /setup
        if (!$setupCompleted && $request->path() !== 'setup' && $request->path() !== 'setup/process') {
            // Excepciones: permitir acceso a assets, health checks, etc.
            $exceptions = ['health', 'api/health', '', 'up'];

            if (!in_array($request->path(), $exceptions)) {
                return redirect()->route('setup.show');
            }
        }

        return $next($request);
    }
}


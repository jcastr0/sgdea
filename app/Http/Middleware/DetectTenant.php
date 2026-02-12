<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectTenant
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Extraer dominio de la URL
        $host = $request->getHost();

        // Buscar tenant por dominio
        $tenant = Tenant::where('domain', $host)
            ->orWhere('domain', 'like', '%' . $host)
            ->first();

        // Si no encuentra tenant específico, usar el primero (default)
        if (!$tenant) {
            $tenant = Tenant::active()->first();
        }

        // Guardar en sesión
        if ($tenant) {
            session(['tenant_id' => $tenant->id, 'tenant' => $tenant]);
            $request->attributes->add(['tenant' => $tenant]);
        }

        return $next($request);
    }
}


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

        // Buscar tenant por dominio exacto o parcial
        $tenant = Tenant::where('domain', $host)
            ->orWhere('domain', 'like', '%' . $host)
            ->first();

        // Si encuentra tenant por dominio, guardar en sesión
        // Si NO encuentra, NO asignar tenant por defecto (será página genérica SGDEA)
        if ($tenant) {
            session(['tenant_id' => $tenant->id, 'tenant' => $tenant]);
            $request->attributes->add(['tenant' => $tenant]);
        } else {
            // Limpiar cualquier tenant previo de la sesión
            session()->forget(['tenant_id', 'tenant', 'current_tenant']);
        }

        return $next($request);
    }
}


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
        $host = $request->getHost(); // ej: sgdea.maritimosarboleda.com

        // Buscar tenant por dominio
        $tenant = $this->findTenantByHost($host);

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

    /**
     * Buscar tenant por host de diferentes maneras
     *
     * Ejemplos:
     * - Host: sgdea.maritimosarboleda.com -> Busca: maritimosarboleda.com
     * - Host: sgdea.bauprespilotos.com -> Busca: bauprespilotos.com
     * - Host: localhost -> No encuentra tenant
     */
    private function findTenantByHost(string $host): ?Tenant
    {
        // 1. Buscar por dominio exacto
        $tenant = Tenant::where('domain', $host)->first();
        if ($tenant) {
            return $tenant;
        }

        // 2. Buscar si el host contiene el dominio del tenant
        // Ejemplo: host "sgdea.maritimosarboleda.com" contiene dominio "maritimosarboleda.com"
        $tenant = Tenant::whereRaw("? LIKE CONCAT('%', domain)", [$host])->first();
        if ($tenant) {
            return $tenant;
        }

        // 3. Extraer dominio base quitando prefijo "sgdea."
        if (str_starts_with($host, 'sgdea.')) {
            $baseDomain = substr($host, 6); // Quita "sgdea."
            $tenant = Tenant::where('domain', $baseDomain)->first();
            if ($tenant) {
                return $tenant;
            }
        }

        // 4. Buscar si el dominio del tenant está contenido en el host
        // Útil para subdominios como "app.empresa.com" buscando "empresa.com"
        $tenant = Tenant::where(function($query) use ($host) {
            $query->whereRaw("? LIKE CONCAT('%', domain, '%')", [$host])
                  ->orWhereRaw("domain LIKE CONCAT('%', ?, '%')", [$host]);
        })->first();

        return $tenant;
    }
}


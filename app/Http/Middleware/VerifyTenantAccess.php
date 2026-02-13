<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyTenantAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si no está autenticado, redirigir a login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Si es superadmin global (tenant_id = NULL y rol superadmin_global), permitir acceso total
        if ($user->isSuperadminGlobal()) {
            return $next($request);
        }

        $currentTenantId = session('tenant_id');

        // Si no hay tenant_id en sesión, establecerlo desde el usuario
        if (!$currentTenantId && $user->tenant_id) {
            session(['tenant_id' => $user->tenant_id]);
            $currentTenantId = $user->tenant_id;
        }

        // Si el usuario no tiene tenant_id asignado, permitir pasar (podría ser admin)
        if (!$user->tenant_id) {
            return $next($request);
        }

        // Verificar que el usuario pertenece al tenant actual
        if ((int)$user->tenant_id !== (int)$currentTenantId) {
            // Corregir la sesión en lugar de sacar al usuario
            session(['tenant_id' => $user->tenant_id]);
        }

        return $next($request);
    }
}


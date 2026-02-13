<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware: IsSuperadminGlobal
 *
 * Verifica que el usuario autenticado sea un Superadmin Global.
 * Un superadmin global es un usuario con:
 * - tenant_id = NULL
 * - role.slug = 'superadmin_global'
 */
class IsSuperadminGlobal
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Verificar si es superadmin global
        if (!$user->isSuperadminGlobal()) {
            abort(403, 'No tienes permiso para acceder a esta sección de administración global');
        }

        return $next($request);
    }
}


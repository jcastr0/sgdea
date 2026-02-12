<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsSuperadminGlobal
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario autenticado es superadmin global
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('auth.login');
        }

        // El usuario debe estar en tabla system_users con is_superadmin = true
        $isSuperadmin = \App\Models\SystemUser::where('id', $user->id)
            ->where('is_superadmin', true)
            ->exists();

        if (!$isSuperadmin) {
            abort(403, 'No tienes permiso para acceder a esta sección');
        }

        return $next($request);
    }
}


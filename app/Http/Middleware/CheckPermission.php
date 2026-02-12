<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $permission
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }

        $user = auth()->user();

        // Si no tiene rol, denegar
        if (!$user->role) {
            abort(403, 'Usuario sin rol asignado');
        }

        // Verificar si tiene el permiso
        if (!$user->hasPermission($permission)) {
            abort(403, 'No tienes permiso para acceder a esta sección');
        }

        return $next($request);
    }
}


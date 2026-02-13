<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    /**
     * Detectar tenant por dominio de la URL actual
     */
    public function detectTenant()
    {
        $host = request()->getHost();

        // Buscar tenant por dominio exacto
        $tenant = Tenant::where('domain', $host)->first();

        // Si no encuentra, buscar tenant por defecto (el primero activo)
        if (!$tenant) {
            $tenant = Tenant::where('status', 'active')->first();
        }

        // Guardar en sesión para acceso rápido
        if ($tenant) {
            session(['current_tenant' => $tenant]);
        }

        return $tenant;
    }

    /**
     * Obtener tema del tenant
     */
    public function getTenantTheme(Tenant $tenant)
    {
        return $tenant->themeConfiguration ?? null;
    }

    /**
     * Obtener logo del tenant
     */
    public function getTenantLogo(Tenant $tenant, $useDark = false)
    {
        return $tenant->getLogo($useDark);
    }

    /**
     * Obtener favicon del tenant
     */
    public function getTenantFavicon(Tenant $tenant)
    {
        return $tenant->getFavicon();
    }

    /**
     * Obtener dominio de email del tenant
     */
    public function getTenantEmailDomain(Tenant $tenant): string
    {
        // Extraer dominio del email del tenant
        // Si domain es "maritimosarboleda.com" → "@maritimosarboleda.com"
        return '@' . $tenant->domain;
    }

    /**
     * Validar que email pertenece al tenant
     */
    public function validateEmailDomain($email, Tenant $tenant): bool
    {
        $emailDomain = $this->getTenantEmailDomain($tenant);
        return Str::endsWith($email, $emailDomain);
    }

    /**
     * Extraer dominio de un email
     */
    public function extractEmailDomain($email): string
    {
        return substr($email, strpos($email, '@'));
    }

    /**
     * Validar credenciales de login
     */
    public function validateLogin($email, $password, Tenant $tenant)
    {
        // Validar que email pertenece al tenant
        if (!$this->validateEmailDomain($email, $tenant)) {
            return [
                'success' => false,
                'message' => 'El email no pertenece a esta empresa',
            ];
        }

        // Buscar usuario en el tenant
        $user = User::byEmailInTenant($email, $tenant->id)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Email o contraseña incorrectos',
            ];
        }

        // Validar contraseña
        if (!Hash::check($password, $user->password)) {
            return [
                'success' => false,
                'message' => 'Email o contraseña incorrectos',
            ];
        }

        // Validar estado
        if ($user->isPendingApproval()) {
            return [
                'success' => false,
                'message' => 'Tu cuenta está pendiente de aprobación del administrador',
                'pending_approval' => true,
            ];
        }

        if (!$user->isActive()) {
            return [
                'success' => false,
                'message' => 'Tu cuenta no está activa',
            ];
        }

        return [
            'success' => true,
            'user' => $user,
        ];
    }

    /**
     * Crear usuario nuevo con registro
     */
    public function registerUser($name, $email, $password, Tenant $tenant)
    {
        // Validar dominio de email
        if (!$this->validateEmailDomain($email, $tenant)) {
            return [
                'success' => false,
                'message' => 'El email debe terminar con ' . $this->getTenantEmailDomain($tenant),
            ];
        }

        // Verificar si email ya existe en este tenant
        if (User::byEmailInTenant($email, $tenant->id)->first()) {
            return [
                'success' => false,
                'message' => 'Este email ya está registrado en la empresa',
            ];
        }

        // Verificar si el email es de un superadmin global existente
        $existingGlobalUser = User::where('email', $email)
            ->whereNull('tenant_id')
            ->whereHas('role', function($q) {
                $q->where('slug', 'superadmin_global');
            })
            ->exists();

        // Crear usuario
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'tenant_id' => $tenant->id,
            'status' => $existingGlobalUser ? 'active' : 'pending_approval',
        ]);

        return [
            'success' => true,
            'user' => $user,
            'is_superadmin' => $existingGlobalUser,
        ];
    }

    /**
     * Aprobar usuario (superadmin)
     */
    public function approveUser(User $user, User $approver)
    {
        $user->update([
            'status' => 'active',
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        return $user;
    }

    /**
     * Rechazar usuario (superadmin)
     */
    public function rejectUser(User $user, User $approver)
    {
        $user->update([
            'status' => 'inactive',
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        $user->forceDelete();

        return true;
    }

    /**
     * Registrar login exitoso
     */
    public function recordLogin(User $user)
    {
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
    }

    /**
     * Obtener usuarios pendientes de aprobación en un tenant
     */
    public function getPendingUsers(Tenant $tenant)
    {
        return User::ofTenant($tenant->id)
            ->pendingApproval()
            ->orderBy('created_at', 'desc')
            ->get();
    }
}


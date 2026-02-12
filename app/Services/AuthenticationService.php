<?php

namespace App\Services;

use App\Models\LoginAttempt;
use App\Models\SecurityEvent;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;

class AuthenticationService
{
    // Máximo número de intentos fallidos permitidos
    const MAX_LOGIN_ATTEMPTS = 5;

    // Tiempo de bloqueo en minutos
    const LOCK_TIME_MINUTES = 30;

    /**
     * Realiza el login de un usuario
     */
    public function login(string $email, string $password): ?User
    {
        $user = User::where('email', $email)->first();

        // Registrar intento de login (sin revelar si el usuario existe)
        $this->recordLoginAttempt($email, false, 'Invalid credentials');

        if (!$user) {
            return null;
        }

        // Verificar si el usuario está bloqueado
        if ($user->isBlocked()) {
            return null;
        }

        // Desbloquear si el tiempo de bloqueo ha expirado
        $user->unlockIfExpired();

        // Verificar contraseña
        if (!Hash::check($password, $user->password)) {
            $this->handleFailedLoginAttempt($user);
            return null;
        }

        // Verificar estado del usuario
        if (!$user->isActive()) {
            return null;
        }

        // Éxito del login
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => Request::ip(),
        ]);

        // Registrar intento exitoso
        $this->recordLoginAttempt($email, true);

        // Registrar evento de seguridad
        $this->recordSecurityEvent($user, 'login');

        return $user;
    }

    /**
     * Registra un usuario nuevo
     */
    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'department' => $data['department'] ?? null,
            'status' => 'pending_approval',
        ]);

        // Asignar rol por defecto (si existe)
        $defaultRole = \App\Models\Role::where('name', 'User')->first();
        if ($defaultRole) {
            $user->roles()->attach($defaultRole);
        }

        // Registrar evento de seguridad
        $this->recordSecurityEvent(null, 'user_created', 'User', $user->id);

        return $user;
    }

    /**
     * Maneja los intentos fallidos de login
     */
    private function handleFailedLoginAttempt(User $user): void
    {
        // Contar intentos fallidos en los últimos 30 minutos
        $failedAttempts = LoginAttempt::where('email', $user->email)
            ->where('success', false)
            ->where('created_at', '>', now()->subMinutes(self::LOCK_TIME_MINUTES))
            ->count();

        // Si se alcanza el máximo, bloquear al usuario
        if ($failedAttempts >= self::MAX_LOGIN_ATTEMPTS) {
            $user->update([
                'blocked_until' => now()->addMinutes(self::LOCK_TIME_MINUTES),
                'status' => 'blocked',
            ]);

            $this->recordSecurityEvent($user, 'account_blocked', reason: 'Too many failed login attempts');
        }
    }

    /**
     * Registra un intento de login
     */
    private function recordLoginAttempt(string $email, bool $success, ?string $reason = null): void
    {
        LoginAttempt::create([
            'email' => $email,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'success' => $success,
            'reason' => $reason,
        ]);
    }

    /**
     * Registra un evento de seguridad
     */
    public function recordSecurityEvent(
        ?User $user,
        string $eventType,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $reason = null
    ): SecurityEvent {
        $deviceInfo = $this->parseUserAgent(Request::userAgent());

        return SecurityEvent::create([
            'user_id' => $user?->id,
            'event_type' => $eventType,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'browser' => $deviceInfo['browser'] ?? null,
            'operating_system' => $deviceInfo['os'] ?? null,
            'device_type' => $deviceInfo['device_type'] ?? null,
            'description' => $reason,
        ]);
    }

    /**
     * Extrae información del User-Agent
     */
    private function parseUserAgent(?string $userAgent): array
    {
        $result = [
            'browser' => 'Unknown',
            'os' => 'Unknown',
            'device_type' => 'Desktop',
        ];

        if (!$userAgent) {
            return $result;
        }

        // Detectar navegador
        if (preg_match('/MSIE|Trident|Edge/i', $userAgent)) {
            $result['browser'] = 'Internet Explorer';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $result['browser'] = 'Firefox';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $result['browser'] = 'Chrome';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $result['browser'] = 'Safari';
        }

        // Detectar SO
        if (preg_match('/Windows/i', $userAgent)) {
            $result['os'] = 'Windows';
        } elseif (preg_match('/Mac/i', $userAgent)) {
            $result['os'] = 'macOS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $result['os'] = 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $result['os'] = 'Android';
        } elseif (preg_match('/iPhone|iPad|iOS/i', $userAgent)) {
            $result['os'] = 'iOS';
        }

        // Detectar tipo de dispositivo
        if (preg_match('/Mobile|Android|iPhone/i', $userAgent)) {
            $result['device_type'] = 'Mobile';
        } elseif (preg_match('/Tablet|iPad/i', $userAgent)) {
            $result['device_type'] = 'Tablet';
        }

        return $result;
    }
}


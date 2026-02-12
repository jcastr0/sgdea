<?php

namespace App\Services;

use App\Models\SecurityEvent;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class AuditSecurityService
{
    /**
     * Registra un evento de auditoría de seguridad
     */
    public static function logSecurityEvent(
        string $eventType,
        ?User $user = null,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $metadata = null,
        ?string $description = null
    ): SecurityEvent {
        $deviceInfo = self::parseUserAgent(Request::userAgent());

        return SecurityEvent::create([
            'user_id' => $user?->id ?? auth()->id(),
            'event_type' => $eventType,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'browser' => $deviceInfo['browser'] ?? null,
            'operating_system' => $deviceInfo['os'] ?? null,
            'device_type' => $deviceInfo['device_type'] ?? null,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Registra el login de un usuario
     */
    public static function logLogin(User $user): SecurityEvent
    {
        return self::logSecurityEvent(
            'login',
            $user,
            description: "Usuario {$user->email} inició sesión"
        );
    }

    /**
     * Registra el logout de un usuario
     */
    public static function logLogout(User $user): SecurityEvent
    {
        return self::logSecurityEvent(
            'logout',
            $user,
            description: "Usuario {$user->email} cerró sesión"
        );
    }

    /**
     * Registra un intento fallido de login
     */
    public static function logFailedLogin(string $email, string $reason): SecurityEvent
    {
        return self::logSecurityEvent(
            'failed_login',
            null,
            description: "Intento fallido de login para {$email}: {$reason}"
        );
    }

    /**
     * Registra la creación de un usuario
     */
    public static function logUserCreated(User $createdUser, ?User $createdBy = null): SecurityEvent
    {
        return self::logSecurityEvent(
            'user_created',
            $createdBy,
            'User',
            $createdUser->id,
            [
                'user_email' => $createdUser->email,
                'user_name' => $createdUser->name,
            ],
            "Usuario {$createdUser->email} fue creado"
        );
    }

    /**
     * Registra la actualización de un usuario
     */
    public static function logUserUpdated(User $user, array $oldValues, array $newValues): SecurityEvent
    {
        return self::logSecurityEvent(
            'user_updated',
            auth()->user(),
            'User',
            $user->id,
            [
                'old_values' => $oldValues,
                'new_values' => $newValues,
            ],
            "Usuario {$user->email} fue actualizado"
        );
    }

    /**
     * Registra la aprobación de un usuario
     */
    public static function logUserApproved(User $user, User $approvedBy): SecurityEvent
    {
        return self::logSecurityEvent(
            'user_approved',
            $approvedBy,
            'User',
            $user->id,
            [
                'user_email' => $user->email,
            ],
            "Usuario {$user->email} fue aprobado por {$approvedBy->email}"
        );
    }

    /**
     * Registra el bloqueo de una cuenta
     */
    public static function logAccountBlocked(User $user, string $reason): SecurityEvent
    {
        return self::logSecurityEvent(
            'account_blocked',
            $user,
            description: "Cuenta bloqueada por: {$reason}"
        );
    }

    /**
     * Registra la asignación de un rol
     */
    public static function logRoleAssigned(User $user, string $roleName, ?User $assignedBy = null): SecurityEvent
    {
        return self::logSecurityEvent(
            'role_assigned',
            $assignedBy,
            'Role',
            null,
            [
                'user_email' => $user->email,
                'role_name' => $roleName,
            ],
            "Rol {$roleName} asignado a {$user->email}"
        );
    }

    /**
     * Registra la remoción de un rol
     */
    public static function logRoleRemoved(User $user, string $roleName, ?User $removedBy = null): SecurityEvent
    {
        return self::logSecurityEvent(
            'role_removed',
            $removedBy,
            'Role',
            null,
            [
                'user_email' => $user->email,
                'role_name' => $roleName,
            ],
            "Rol {$roleName} removido de {$user->email}"
        );
    }

    /**
     * Extrae información del User-Agent
     */
    private static function parseUserAgent(?string $userAgent): array
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
            $result['browser'] = 'Internet Explorer/Edge';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $result['browser'] = 'Firefox';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $result['browser'] = 'Chrome';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $result['browser'] = 'Safari';
        } elseif (preg_match('/Opera/i', $userAgent)) {
            $result['browser'] = 'Opera';
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

    /**
     * Registra una violación de seguridad (intento de acceso no autorizado)
     */
    public static function logSecurityViolation(
        ?User $user,
        string $violationType,
        string $description,
        $empresa = null
    ): SecurityEvent {
        $deviceInfo = self::parseUserAgent(Request::userAgent());

        return SecurityEvent::create([
            'user_id' => $user?->id ?? null,
            'company_id' => $empresa?->id ?? ($user?->company_id ?? null),
            'event_type' => $violationType,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'browser' => $deviceInfo['browser'] ?? null,
            'operating_system' => $deviceInfo['os'] ?? null,
            'device_type' => $deviceInfo['device_type'] ?? null,
            'description' => $description,
            'metadata' => json_encode([
                'severity' => 'high',
                'timestamp' => now()->toIso8601String(),
                'ip' => Request::ip(),
                'attempted_company_id' => $empresa?->id,
                'user_company_id' => $user?->company_id,
            ]),
        ]);
    }

    /**
     * Registra intentos fallidos de login con información de empresa
     */
    public static function logFailedLoginWithCompany(
        string $email,
        string $reason,
        $empresa = null
    ): SecurityEvent {
        return SecurityEvent::create([
            'user_id' => null,
            'company_id' => $empresa?->id ?? null,
            'event_type' => 'failed_login',
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'description' => "Intento fallido de login para {$email}: {$reason}",
        ]);
    }
}


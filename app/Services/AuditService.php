<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;

class AuditService
{
    /**
     * Registrar evento de auditoría
     */
    public static function log(
        string $action,
        string $entityType,
        $entityId,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): AuditLog {
        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id ?? null;
        $userId = auth()->id();
        $ipAddress = request()->ip();
        $userAgent = request()->userAgent();

        // Crear descripción automática si no se proporciona
        if (!$description) {
            $description = self::generarDescripcion($action, $entityType, $entityId, $oldValues, $newValues);
        }

        // Generar hash para verificar integridad
        $dataToHash = json_encode([
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => $userId,
            'ip_address' => $ipAddress,
        ]);
        $hash = hash('sha256', $dataToHash);

        // Registrar en BD
        return AuditLog::create([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'hash' => $hash,
            'description' => $description,
        ]);
    }

    /**
     * Generar descripción automática del evento
     */
    private static function generarDescripcion(
        string $action,
        string $entityType,
        $entityId,
        ?array $oldValues = null,
        ?array $newValues = null
    ): string {
        $userName = auth()->user()->name ?? 'Sistema';
        $entityName = self::traducirEntidad($entityType);
        $actionName = self::traducirAccion($action);

        return "{$userName} {$actionName} {$entityName} #{$entityId}";
    }

    /**
     * Traducir tipo de entidad a legible
     */
    private static function traducirEntidad(string $type): string
    {
        $translations = [
            'factura' => 'Factura',
            'tercero' => 'Tercero/Cliente',
            'usuario' => 'Usuario',
            'tenant' => 'Empresa',
            'importacion' => 'Importación',
            'login' => 'Acceso',
            'configuracion' => 'Configuración',
            'tema' => 'Tema',
        ];

        return $translations[$type] ?? ucfirst($type);
    }

    /**
     * Traducir acción a legible
     */
    private static function traducirAccion(string $action): string
    {
        $translations = [
            'create' => 'creó',
            'update' => 'editó',
            'delete' => 'eliminó',
            'approve' => 'aprobó',
            'reject' => 'rechazó',
            'login' => 'inició sesión',
            'logout' => 'cerró sesión',
            'download' => 'descargó',
            'export' => 'exportó',
            'import' => 'importó',
            'restore' => 'restauró',
            'publish' => 'publicó',
            'archive' => 'archivó',
        ];

        return $translations[$action] ?? $action;
    }

    /**
     * Verificar integridad de registro
     */
    public static function verificarIntegridad(AuditLog $log): bool
    {
        $dataToHash = json_encode([
            'action' => $log->action,
            'entity_type' => $log->entity_type,
            'entity_id' => $log->entity_id,
            'old_values' => json_decode($log->old_values, true),
            'new_values' => json_decode($log->new_values, true),
            'user_id' => $log->user_id,
            'ip_address' => $log->ip_address,
        ]);
        $calculatedHash = hash('sha256', $dataToHash);

        return $calculatedHash === $log->hash;
    }

    /**
     * Obtener auditoría de un período
     */
    public static function obtenerPeriodo($tenantId, $fechaInicio, $fechaFin)
    {
        return AuditLog::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener auditoría de una entidad específica
     */
    public static function obtenerEntidad($tenantId, $entityType, $entityId)
    {
        return AuditLog::where('tenant_id', $tenantId)
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Registrar acceso de usuario
     */
    public static function registrarLogin(string $email, string $ip, bool $exitoso = true)
    {
        $user = \App\Models\User::where('email', $email)->first();

        return AuditLog::create([
            'tenant_id' => $user->tenant_id ?? null,
            'user_id' => $user->id ?? null,
            'action' => 'login',
            'entity_type' => 'login',
            'entity_id' => $user->id ?? null,
            'ip_address' => $ip,
            'user_agent' => request()->userAgent(),
            'description' => $exitoso
                ? "{$email} inició sesión exitosamente"
                : "{$email} intentó ingresar sin éxito",
            'new_values' => json_encode(['exitoso' => $exitoso]),
        ]);
    }

    /**
     * Registrar logout
     */
    public static function registrarLogout()
    {
        if (!auth()->check()) {
            return;
        }

        return AuditLog::create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id' => auth()->id(),
            'action' => 'logout',
            'entity_type' => 'login',
            'entity_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => auth()->user()->name . ' cerró sesión',
        ]);
    }

    /**
     * Registrar descarga de PDF
     */
    public static function registrarDescarga($entityType, $entityId, $nombreArchivo)
    {
        return self::log('download', $entityType, $entityId, null, null,
            auth()->user()->name . " descargó {$nombreArchivo}");
    }

    /**
     * Registrar exportación
     */
    public static function registrarExportacion($entityType, $cantidad, $formato)
    {
        return self::log('export', $entityType, null, null,
            ['cantidad' => $cantidad, 'formato' => $formato],
            auth()->user()->name . " exportó {$cantidad} registros en {$formato}");
    }

    /**
     * Registrar acceso no autorizado
     */
    public static function registrarAccesoNoAutorizado($ruta, $razon = '')
    {
        return AuditLog::create([
            'tenant_id' => session('tenant_id'),
            'user_id' => auth()->id(),
            'action' => 'unauthorized_access',
            'entity_type' => 'security',
            'entity_id' => null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => "Intento de acceso no autorizado a {$ruta}: {$razon}",
        ]);
    }
}


<?php

namespace App\Services\MultiTenant;

use App\Models\Company;
use App\Models\User;
use App\Models\SecurityEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantService
{
    /**
     * Obtener empresa actual basada en dominio
     */
    public static function getCurrentCompany(): ?Company
    {
        $dominio = request()->getHost();

        // Remover 'www.' si existe
        $dominio = preg_replace('/^www\./', '', $dominio);

        return Company::findByDomain($dominio);
    }

    /**
     * Obtener empresa del usuario autenticado
     */
    public static function getUserCompany(): ?Company
    {
        if (auth()->check()) {
            return auth()->user()->company;
        }

        return self::getCurrentCompany();
    }

    /**
     * Crear nueva entidad (empresa)
     */
    public static function crearEmpresa(array $datos, int $usuarioCreadorId): array
    {
        try {
            DB::beginTransaction();

            // Crear empresa
            $empresa = Company::create([
                'nombre_legal' => $datos['nombre_legal'],
                'nit' => $datos['nit'],
                'dominio_principal' => $datos['dominio_principal'],
                'dominios_adicionales' => $datos['dominios_adicionales'] ?? [],
                'descripcion' => $datos['descripcion'] ?? null,
                'direccion_legal' => $datos['direccion_legal'] ?? null,
                'ciudad' => $datos['ciudad'] ?? null,
                'departamento' => $datos['departamento'] ?? null,
                'pais' => $datos['pais'] ?? 'Colombia',
                'telefono' => $datos['telefono'] ?? null,
                'email_contacto' => $datos['email_contacto'] ?? null,
                'representante_legal' => $datos['representante_legal'] ?? null,
                'superadmin_email' => $datos['superadmin_email'],
                'configuracion_metadatos' => $datos['configuracion_metadatos'] ?? [],
                'reglas_retencion_documental' => $datos['reglas_retencion_documental'] ?? [],
                'paleta_colores' => $datos['paleta_colores'] ?? [],
                'estado' => 'activa',
                'fecha_inicio_operaciones' => now(),
            ]);

            // Crear usuario superadmin para la empresa
            $superadmin = User::create([
                'name' => 'Administrador ' . $empresa->nombre_legal,
                'email' => $datos['superadmin_email'],
                'password' => bcrypt(str_random(32)), // Será cambiado en primer login
                'company_id' => $empresa->id,
                'estado' => 'activa',
                'email_verificado' => true, // Auto-verificado
            ]);

            // Asignar rol Superadmin
            $superadmin->roles()->attach(
                \App\Models\Role::whereName('Superadmin')->first()->id ?? 1
            );

            // Registrar evento de auditoría
            SecurityEvent::create([
                'user_id' => $usuarioCreadorId,
                'company_id' => $empresa->id,
                'event_type' => 'company_created',
                'description' => "Nueva empresa creada: {$empresa->nombre_legal}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => json_encode([
                    'empresa_id' => $empresa->id,
                    'empresa_uuid' => $empresa->uuid,
                    'empresa_nit' => $empresa->nit,
                    'superadmin_email' => $superadmin->email,
                ]),
            ]);

            DB::commit();

            return [
                'success' => true,
                'empresa' => $empresa,
                'superadmin' => $superadmin,
                'mensaje' => "Empresa '{$empresa->nombre_legal}' creada exitosamente",
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creando empresa: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Actualizar configuración de marca de empresa
     */
    public static function actualizarConfiguracionMarca(Company $empresa, array $datos): array
    {
        try {
            $empresa->update([
                'logo_url' => $datos['logo_url'] ?? $empresa->logo_url,
                'favicon_url' => $datos['favicon_url'] ?? $empresa->favicon_url,
                'css_personalizado' => $datos['css_personalizado'] ?? $empresa->css_personalizado,
                'paleta_colores' => $datos['paleta_colores'] ?? $empresa->paleta_colores,
            ]);

            // Registrar evento
            SecurityEvent::create([
                'user_id' => auth()->id(),
                'company_id' => $empresa->id,
                'event_type' => 'company_branding_updated',
                'description' => "Configuración de marca actualizada para {$empresa->nombre_legal}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => json_encode([
                    'empresa_id' => $empresa->id,
                    'campos_actualizados' => array_keys($datos),
                ]),
            ]);

            return [
                'success' => true,
                'empresa' => $empresa,
                'mensaje' => 'Configuración de marca actualizada',
            ];

        } catch (\Exception $e) {
            Log::error('Error actualizando branding: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Obtener configuración de metadatos contextuales
     */
    public static function obtenerMetadatosContextuales(Company $empresa): array
    {
        return $empresa->obtenerMetadatosContextuales();
    }

    /**
     * Actualizar reglas de retención documental
     */
    public static function actualizarReglasRetencion(Company $empresa, array $reglas): array
    {
        try {
            $empresa->update([
                'reglas_retencion_documental' => $reglas,
            ]);

            SecurityEvent::create([
                'user_id' => auth()->id(),
                'company_id' => $empresa->id,
                'event_type' => 'company_retention_rules_updated',
                'description' => "Reglas de retención documental actualizadas",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => json_encode([
                    'empresa_id' => $empresa->id,
                    'series_configuradas' => array_keys($reglas),
                ]),
            ]);

            return [
                'success' => true,
                'reglas' => $reglas,
                'mensaje' => 'Reglas de retención actualizadas',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Listar todas las empresas (solo para superadmin global)
     */
    public static function listarEmpresas(int $pagina = 1)
    {
        return Company::paginate(15, ['*'], 'page', $pagina);
    }

    /**
     * Obtener estadísticas de empresa
     */
    public static function obtenerEstadisticas(Company $empresa): array
    {
        return [
            'total_usuarios' => $empresa->usuarios()->count(),
            'usuarios_activos' => $empresa->usuarios()->where('estado', 'activa')->count(),
            'total_terceros' => $empresa->terceros()->count(),
            'total_facturas' => $empresa->facturas()->count(),
            'facturas_aceptadas' => $empresa->facturas()->where('estado', 'aceptada')->count(),
            'facturas_rechazadas' => $empresa->facturas()->where('estado', 'rechazada')->count(),
            'eventos_seguridad' => $empresa->eventosSeguridad()->count(),
            'eventos_ultimas_24h' => $empresa->eventosSeguridad()
                ->where('created_at', '>=', now()->subHours(24))
                ->count(),
            'trabajos_importacion' => $empresa->trabajosImportacion()->count(),
        ];
    }
}


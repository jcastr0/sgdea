<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Company extends Model
{
    protected $table = 'companies';

    protected $fillable = [
        'uuid',
        'nombre_legal',
        'nit',
        'dominio_principal',
        'dominios_adicionales',
        'descripcion',
        'direccion_legal',
        'ciudad',
        'departamento',
        'pais',
        'telefono',
        'email_contacto',
        'representante_legal',
        'logo_url',
        'favicon_url',
        'css_personalizado',
        'paleta_colores',
        'configuracion_metadatos',
        'reglas_retencion_documental',
        'superadmin_email',
        'estado', // 'activa', 'inactiva', 'suspendida'
        'fecha_inicio_operaciones',
        'fecha_vencimiento',
        'metadata',
    ];

    protected $casts = [
        'dominios_adicionales' => 'array',
        'configuracion_metadatos' => 'array',
        'reglas_retencion_documental' => 'array',
        'paleta_colores' => 'array',
        'metadata' => 'array',
        'fecha_inicio_operaciones' => 'datetime',
        'fecha_vencimiento' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Generar UUID al crear
     */
    protected static function booting()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    /**
     * Relación: Una empresa tiene muchos usuarios
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'company_id');
    }

    /**
     * Relación: Una empresa tiene muchos terceros
     */
    public function terceros(): HasMany
    {
        return $this->hasMany(Tercero::class, 'company_id');
    }

    /**
     * Relación: Una empresa tiene muchas facturas
     */
    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class, 'company_id');
    }

    /**
     * Relación: Una empresa tiene muchos eventos de seguridad
     */
    public function eventosSeguridad(): HasMany
    {
        return $this->hasMany(SecurityEvent::class, 'company_id');
    }

    /**
     * Relación: Una empresa tiene muchos trabajos de importación
     */
    public function trabajosImportacion(): HasMany
    {
        return $this->hasMany(ImportJob::class, 'company_id');
    }

    /**
     * Scope: Encontrar empresa por dominio
     */
    public function scopePorDominio($query, string $dominio)
    {
        return $query->where('dominio_principal', $dominio)
                     ->orWhereJsonContains('dominios_adicionales', $dominio);
    }

    /**
     * Encontrar empresa por dominio
     */
    public static function findByDomain(string $dominio): ?self
    {
        return self::porDominio($dominio)->first();
    }

    /**
     * Obtener configuración de marca para esta empresa
     */
    public function getConfiguracionMarca(): array
    {
        return [
            'logo_url' => $this->logo_url,
            'favicon_url' => $this->favicon_url,
            'css_personalizado' => $this->css_personalizado,
            'paleta_colores' => $this->paleta_colores,
            'nombre_empresa' => $this->nombre_legal,
            'dominio' => request()->getHost(),
        ];
    }

    /**
     * Obtener reglas de retención documental para una serie
     */
    public function obtenerReglaRetencion(string $serieDocs): ?array
    {
        $reglas = $this->reglas_retencion_documental ?? [];
        return $reglas[$serieDocs] ?? null;
    }

    /**
     * Obtener metadatos contextuales configurados
     */
    public function obtenerMetadatosContextuales(): array
    {
        return $this->configuracion_metadatos ?? [];
    }

    /**
     * Verificar si está activa
     */
    public function estaActiva(): bool
    {
        return $this->estado === 'activa' &&
               (!$this->fecha_vencimiento || $this->fecha_vencimiento->isFuture());
    }

    /**
     * Obtener superadmin de la empresa
     */
    public function obtenerSuperadmin(): ?User
    {
        return $this->usuarios()
                    ->whereHas('roles', function ($q) {
                        $q->where('nombre', 'Superadmin');
                    })
                    ->first();
    }
}


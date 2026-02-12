<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Factura;

class Tercero extends Model
{
    use Auditable;

    protected $table = 'terceros';

    protected $fillable = [
        'tenant_id',
        'nit',
        'nombre_razon_social',
        'direccion',
        'telefono',
        'email',
        'notas',
        'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación: Un tercero pertenece a un tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relación: Un tercero puede tener muchas facturas
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'tercero_id');
    }

    /**
     * Relación: Un tercero puede tener muchas facturas (alias en español)
     */
    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class, 'tercero_id');
    }

    /**
     * Scope: Filtrar por tenant
     */
    public function scopeByTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope: Filtrar por estado
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope: Buscar por nombre
     */
    public function scopeSearchByName($query, $nombre)
    {
        return $query->where('nombre_razon_social', 'like', "%{$nombre}%");
    }

    /**
     * Scope: Buscar por NIT
     */
    public function scopeSearchByNit($query, $nit)
    {
        return $query->where('nit', 'like', "%{$nit}%");
    }

    /**
     * Scope: Buscar por rango de fechas
     */
    public function scopeDateRange($query, $desde, $hasta)
    {
        return $query->whereBetween('created_at', [$desde, $hasta]);
    }

    /**
     * Verificar si tiene facturas asociadas
     */
    public function tieneFacturas(): bool
    {
        return $this->invoices()->count() > 0;
    }

    /**
     * Calcular similitud entre strings usando Levenshtein
     */
    public static function calcularSimilitud($str1, $str2): float
    {
        $len1 = strlen($str1);
        $len2 = strlen($str2);

        if ($len1 === 0) return $len2 === 0 ? 1.0 : 0.0;
        if ($len2 === 0) return 0.0;

        $d = array_fill(0, $len2 + 1, array_fill(0, $len1 + 1, 0));

        for ($i = 0; $i <= $len1; $i++) {
            $d[$i][0] = $i;
        }

        for ($j = 0; $j <= $len2; $j++) {
            $d[0][$j] = $j;
        }

        for ($i = 1; $i <= $len1; $i++) {
            for ($j = 1; $j <= $len2; $j++) {
                $cost = $str1[$i - 1] === $str2[$j - 1] ? 0 : 1;
                $d[$i][$j] = min(
                    $d[$i - 1][$j] + 1,      // eliminación
                    $d[$i][$j - 1] + 1,      // inserción
                    $d[$i - 1][$j - 1] + $cost // sustitución
                );
            }
        }

        $maxLen = max($len1, $len2);
        return 1 - ($d[$len1][$len2] / $maxLen);
    }

    /**
     * Buscar posibles duplicados por nombre y NIT
     */
    public static function buscarDuplicados($tenantId, $nombre, $nit, $excludeId = null): array
    {
        $duplicados = [];
        $umbralSimilitud = 0.75; // 75% de similitud

        // Buscar por nombre similar
        $tercerosPorNombre = self::byTenant($tenantId)
            ->searchByName($nombre)
            ->get();

        foreach ($tercerosPorNombre as $tercero) {
            if ($excludeId && $tercero->id === $excludeId) {
                continue;
            }

            $similitud = self::calcularSimilitud(
                strtolower($nombre),
                strtolower($tercero->nombre_razon_social)
            );

            if ($similitud >= $umbralSimilitud) {
                $duplicados[] = [
                    'id' => $tercero->id,
                    'nit' => $tercero->nit,
                    'nombre_razon_social' => $tercero->nombre_razon_social,
                    'telefono' => $tercero->telefono,
                    'razon' => 'Nombre similar',
                    'similitud' => round($similitud * 100, 1),
                ];
            }
        }

        // Buscar por NIT exacto
        $terceroPorNit = self::byTenant($tenantId)
            ->where('nit', $nit)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->first();

        if ($terceroPorNit) {
            $duplicados[] = [
                'id' => $terceroPorNit->id,
                'nit' => $terceroPorNit->nit,
                'nombre_razon_social' => $terceroPorNit->nombre_razon_social,
                'telefono' => $terceroPorNit->telefono,
                'razon' => 'NIT exacto',
                'similitud' => 100,
            ];
        }

        // Eliminar duplicados en el array resultado
        $ids = [];
        $resultado = [];
        foreach ($duplicados as $dup) {
            if (!in_array($dup['id'], $ids)) {
                $resultado[] = $dup;
                $ids[] = $dup['id'];
            }
        }

        return $resultado;
    }

    /**
     * Validar NIT colombiano
     */
    public static function validarNitColombia($nit): bool
    {
        // Remover puntos y guiones
        $nit = str_replace(['.', '-'], '', $nit);

        // Debe ser solo números
        if (!ctype_digit($nit)) {
            return false;
        }

        // Entre 6 y 15 dígitos
        if (strlen($nit) < 6 || strlen($nit) > 15) {
            return false;
        }

        return true;
    }
}


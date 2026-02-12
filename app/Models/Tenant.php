<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use Auditable;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'database_name',
        'status',
        'superadmin_id',
        'logo_path',
        'logo_path_light',
        'logo_path_dark',
        'favicon_path',
    ];

    /**
     * Relación: Un tenant tiene una configuración de tema
     */
    public function themeConfiguration()
    {
        return $this->hasOne(ThemeConfiguration::class);
    }

    /**
     * Relación: Un tenant tiene muchos usuarios
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relación: Un tenant pertenece a un SystemUser (superadmin)
     */
    public function systemUser()
    {
        return $this->belongsTo(SystemUser::class, 'superadmin_id');
    }

    /**
     * Scope: Obtener tenant activo
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Obtener tenant por dominio
     */
    public function scopeByDomain($query, $domain)
    {
        return $query->where('domain', $domain)->first();
    }

    /**
     * Obtener logo del tenant (con fallback)
     */
    public function getLogo($useDark = false)
    {
        // Si existe logo específico, usarlo
        if ($useDark && $this->logo_path_dark) {
            return $this->logo_path_dark;
        }
        if (!$useDark && $this->logo_path_light) {
            return $this->logo_path_light;
        }

        // Fallback al logo original
        if ($this->logo_path) {
            return $this->logo_path;
        }

        // Default SGDEA - Usar logos SVG nuevos
        // Para modo oscuro usar logo claro (blanco), para modo claro usar logo oscuro (con colores)
        return $useDark ? '/images/logo-light.svg' : '/images/logo-dark.svg';
    }

    /**
     * Obtener favicon del tenant (con fallback)
     */
    public function getFavicon()
    {
        return $this->favicon_path ?? '/favicon.ico';
    }

    /**
     * Verificar si el tenant tiene logo personalizado
     */
    public function hasCustomLogo()
    {
        return $this->logo_path || $this->logo_path_light || $this->logo_path_dark;
    }

    /**
     * Obtener URL del logo (asset helper)
     */
    public function getLogoUrl($useDark = false)
    {
        return asset($this->getLogo($useDark));
    }

    /**
     * Obtener URL del favicon
     */
    public function getFaviconUrl()
    {
        return asset($this->getFavicon());
    }
}

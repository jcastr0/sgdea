<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThemeConfiguration extends Model
{
    use Auditable;

    protected $fillable = [
        'tenant_id',
        'color_primary',
        'color_secondary',
        'color_accent',
        'color_error',
        'color_success',
        'color_warning',
        'color_bg_light',
        'color_bg_dark',
        'color_text_primary',
        'color_text_secondary',
        'color_border',
        'font_header',
        'font_body',
        'font_mono',
        'size_base',
        'size_h1',
        'size_h2',
        'size_h3',
        'size_small',
        'spacing_sm',
        'spacing_md',
        'spacing_lg',
        'radius_sm',
        'radius_md',
        'shadow_enabled',
        'shadow_intensity',
        'gradient_enabled',
        'logo_height',
        'show_company_name',
        'dark_mode_enabled',
    ];

    protected $casts = [
        'shadow_enabled' => 'boolean',
        'gradient_enabled' => 'boolean',
        'show_company_name' => 'boolean',
        'dark_mode_enabled' => 'boolean',
        'logo_height' => 'integer',
    ];

    /**
     * Relación: Una configuración de tema pertenece a un tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Obtener variables CSS personalizadas
     */
    public function getCssVariables(): array
    {
        return [
            '--color-primary' => $this->color_primary,
            '--color-secondary' => $this->color_secondary,
            '--color-error' => $this->color_error,
            '--color-success' => $this->color_success,
            '--color-warning' => $this->color_warning,
            '--color-bg-light' => $this->color_bg_light,
            '--color-bg-dark' => $this->color_bg_dark,
            '--color-text-primary' => $this->color_text_primary,
            '--color-text-secondary' => $this->color_text_secondary,
            '--color-border' => $this->color_border,
            '--font-header' => $this->font_header,
            '--font-body' => $this->font_body,
            '--font-mono' => $this->font_mono,
            '--size-base' => $this->size_base,
            '--size-h1' => $this->size_h1,
            '--size-h2' => $this->size_h2,
            '--size-h3' => $this->size_h3,
            '--size-small' => $this->size_small,
            '--spacing-sm' => $this->spacing_sm,
            '--spacing-md' => $this->spacing_md,
            '--spacing-lg' => $this->spacing_lg,
            '--radius-sm' => $this->radius_sm,
            '--radius-md' => $this->radius_md,
        ];
    }

    /**
     * Obtener CSS en línea para inyectar en <head>
     */
    public function getInlineCss(): string
    {
        $variables = $this->getCssVariables();
        $css = ':root {' . PHP_EOL;

        foreach ($variables as $name => $value) {
            $css .= "  $name: $value;" . PHP_EOL;
        }

        $css .= '}';

        return $css;
    }

    /**
     * Reset a configuración default
     */
    public function resetToDefault(): void
    {
        $this->update([
            'color_primary' => '#2767C6',
            'color_secondary' => '#102544',
            'color_error' => '#B23A3A',
            'color_success' => '#009F6B',
            'color_warning' => '#F5B400',
            'color_bg_light' => '#F5F7FA',
            'color_bg_dark' => '#102544',
            'color_text_primary' => '#1F2933',
            'color_text_secondary' => '#6B7280',
            'color_border' => '#D4D9E2',
            'font_header' => 'Inter, Roboto, sans-serif',
            'font_body' => 'Inter, Roboto, sans-serif',
            'font_mono' => 'Courier New, monospace',
            'size_base' => '16px',
            'size_h1' => '2.25rem',
            'size_h2' => '1.75rem',
            'size_h3' => '1.5rem',
            'size_small' => '0.875rem',
            'spacing_sm' => '8px',
            'spacing_md' => '16px',
            'spacing_lg' => '24px',
            'radius_sm' => '6px',
            'radius_md' => '8px',
            'shadow_enabled' => true,
            'shadow_intensity' => 'light',
            'gradient_enabled' => false,
            'logo_height' => 40,
            'show_company_name' => true,
        ]);
    }

    /**
     * Scope: Obtener configuración por tenant
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}


<?php

namespace App\Services;

use App\Models\ThemeConfiguration;
use App\Models\Tenant;

class ThemeService
{
    /**
     * Obtener configuración de tema del tenant actual
     */
    public static function getThemeForTenant($tenantId): ThemeConfiguration
    {
        $theme = ThemeConfiguration::where('tenant_id', $tenantId)->first();

        if (!$theme) {
            // Crear tema default si no existe
            $theme = self::createDefaultTheme($tenantId);
        }

        return $theme;
    }

    /**
     * Crear tema default para un tenant
     */
    public static function createDefaultTheme($tenantId): ThemeConfiguration
    {
        $tenant = Tenant::find($tenantId);

        return ThemeConfiguration::create([
            'tenant_id' => $tenantId,
            'color_primary' => '#2767C6',
            'color_primary_dark' => '#0F3F5F',
            'color_primary_darker' => '#102544',
            'color_accent' => '#B23A3A',
            'color_neutral_warm' => '#E3D2B5',
            'color_bg_light' => '#F5F7FA',
            'color_border' => '#D4D9E2',
            'color_text_primary' => '#1F2933',
            'color_text_secondary' => '#6B7280',
            'font_family' => "'Inter', 'Roboto', 'Poppins', sans-serif",
            'font_size_base' => '16px',
            'spacing_base' => '16px',
            'border_radius' => '6px',
            'company_name' => $tenant->name ?? 'SGDEA',
            'is_custom' => false,
        ]);
    }

    /**
     * Obtener CSS en línea para inyectar en HTML
     */
    public static function getInlineCss($tenantId): string
    {
        $theme = self::getThemeForTenant($tenantId);
        return $theme->getInlineCss();
    }

    /**
     * Actualizar colores del tema
     */
    public static function updateColors($tenantId, array $colors): ThemeConfiguration
    {
        $theme = self::getThemeForTenant($tenantId);

        $theme->update([
            'color_primary' => $colors['color_primary'] ?? $theme->color_primary,
            'color_primary_dark' => $colors['color_primary_dark'] ?? $theme->color_primary_dark,
            'color_primary_darker' => $colors['color_primary_darker'] ?? $theme->color_primary_darker,
            'color_accent' => $colors['color_accent'] ?? $theme->color_accent,
            'color_neutral_warm' => $colors['color_neutral_warm'] ?? $theme->color_neutral_warm,
            'color_bg_light' => $colors['color_bg_light'] ?? $theme->color_bg_light,
            'color_border' => $colors['color_border'] ?? $theme->color_border,
            'color_text_primary' => $colors['color_text_primary'] ?? $theme->color_text_primary,
            'color_text_secondary' => $colors['color_text_secondary'] ?? $theme->color_text_secondary,
            'is_custom' => true,
        ]);

        // Registrar en auditoría
        AuditService::log('update', 'theme', $theme->id, null, $colors);

        return $theme;
    }

    /**
     * Actualizar tipografía del tema
     */
    public static function updateTypography($tenantId, array $typography): ThemeConfiguration
    {
        $theme = self::getThemeForTenant($tenantId);

        $theme->update([
            'font_family' => $typography['font_family'] ?? $theme->font_family,
            'font_size_base' => $typography['font_size_base'] ?? $theme->font_size_base,
            'is_custom' => true,
        ]);

        AuditService::log('update', 'theme', $theme->id, null, $typography);

        return $theme;
    }

    /**
     * Actualizar espaciado del tema
     */
    public static function updateSpacing($tenantId, array $spacing): ThemeConfiguration
    {
        $theme = self::getThemeForTenant($tenantId);

        $theme->update([
            'spacing_base' => $spacing['spacing_base'] ?? $theme->spacing_base,
            'border_radius' => $spacing['border_radius'] ?? $theme->border_radius,
            'is_custom' => true,
        ]);

        AuditService::log('update', 'theme', $theme->id, null, $spacing);

        return $theme;
    }

    /**
     * Actualizar logos del tema
     */
    public static function updateLogos($tenantId, array $logos): ThemeConfiguration
    {
        $theme = self::getThemeForTenant($tenantId);

        $theme->update([
            'logo_path' => $logos['logo_path'] ?? $theme->logo_path,
            'logo_dark_path' => $logos['logo_dark_path'] ?? $theme->logo_dark_path,
            'favicon_path' => $logos['favicon_path'] ?? $theme->favicon_path,
            'is_custom' => true,
        ]);

        AuditService::log('update', 'theme', $theme->id, null, ['logos_updated' => true]);

        return $theme;
    }

    /**
     * Resetear tema a valores default
     */
    public static function resetToDefault($tenantId): ThemeConfiguration
    {
        $theme = self::getThemeForTenant($tenantId);
        $theme->resetToDefault();

        AuditService::log('update', 'theme', $theme->id, null, ['action' => 'reset_to_default']);

        return $theme;
    }

    /**
     * Obtener información completa del tema
     */
    public static function getThemeInfo($tenantId): array
    {
        $theme = self::getThemeForTenant($tenantId);

        return [
            'id' => $theme->id,
            'colors' => [
                'primary' => $theme->color_primary,
                'primary_dark' => $theme->color_primary_dark,
                'primary_darker' => $theme->color_primary_darker,
                'accent' => $theme->color_accent,
                'neutral_warm' => $theme->color_neutral_warm,
                'bg_light' => $theme->color_bg_light,
                'border' => $theme->color_border,
                'text_primary' => $theme->color_text_primary,
                'text_secondary' => $theme->color_text_secondary,
            ],
            'typography' => [
                'font_family' => $theme->font_family,
                'font_size_base' => $theme->font_size_base,
            ],
            'spacing' => [
                'base' => $theme->spacing_base,
                'border_radius' => $theme->border_radius,
            ],
            'logos' => [
                'logo' => $theme->logo_path,
                'logo_dark' => $theme->logo_dark_path,
                'favicon' => $theme->favicon_path,
            ],
            'is_custom' => $theme->is_custom,
            'company_name' => $theme->company_name,
        ];
    }
}


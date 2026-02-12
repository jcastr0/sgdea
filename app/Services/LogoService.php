<?php

namespace App\Services;

class LogoService
{
    /**
     * Obtener URL del logo principal (por defecto)
     */
    public static function getMainLogo(): string
    {
        return asset('images/logo/logo_sgdea.png');
    }

    /**
     * Obtener URL del logo en blanco (para fondos azules/oscuros)
     */
    public static function getWhiteLogo(): string
    {
        return asset('images/logo/logo_sgdea_blanco.png');
    }

    /**
     * Obtener URL del logo con letras (texto + símbolo)
     */
    public static function getLogoWithText(): string
    {
        return asset('images/logo/log_letras.png');
    }

    /**
     * Obtener URL del logo con letras en blanco
     */
    public static function getWhiteLogoWithText(): string
    {
        return asset('images/logo/logo_letras_blanco.png');
    }

    /**
     * Obtener logo según contexto (automático)
     * $isDark: true para fondo oscuro (retorna blanco), false para fondo claro (retorna normal)
     */
    public static function getLogoBranding($isDark = false, $withText = false): string
    {
        if ($isDark && $withText) {
            return self::getWhiteLogoWithText();
        } elseif ($isDark) {
            return self::getWhiteLogo();
        } elseif ($withText) {
            return self::getLogoWithText();
        } else {
            return self::getMainLogo();
        }
    }

    /**
     * Obtener todos los logos disponibles
     */
    public static function getAvailableLogos(): array
    {
        return [
            'main' => [
                'url' => self::getMainLogo(),
                'name' => 'Logo Principal',
                'description' => 'Logo símbolo (solo icono)',
            ],
            'white' => [
                'url' => self::getWhiteLogo(),
                'name' => 'Logo Blanco',
                'description' => 'Logo símbolo blanco para fondos oscuros',
            ],
            'text' => [
                'url' => self::getLogoWithText(),
                'name' => 'Logo con Texto',
                'description' => 'Logo símbolo + letras SGDEA',
            ],
            'white_text' => [
                'url' => self::getWhiteLogoWithText(),
                'name' => 'Logo Blanco con Texto',
                'description' => 'Logo símbolo + letras SGDEA (blanco)',
            ],
        ];
    }

    /**
     * Obtener logo del tenant personalizado o default
     */
    public static function getTenantLogo($tenantId, $isDark = false, $withText = false): string
    {
        $themeConfig = \App\Models\ThemeConfiguration::where('tenant_id', $tenantId)->first();

        if ($themeConfig && $isDark && $themeConfig->logo_dark_path) {
            return asset('storage/' . $themeConfig->logo_dark_path);
        } elseif ($themeConfig && $themeConfig->logo_path) {
            return asset('storage/' . $themeConfig->logo_path);
        }

        // Fallback a logo default
        return self::getLogoBranding($isDark, $withText);
    }

    /**
     * HTML completo del logo (img tag)
     */
    public static function renderLogoHtml(
        $isDark = false,
        $withText = false,
        $alt = 'SGDEA Logo',
        $class = '',
        $style = ''
    ): string {
        $src = self::getLogoBranding($isDark, $withText);

        return sprintf(
            '<img src="%s" alt="%s" class="logo %s" style="%s" />',
            $src,
            $alt,
            $class,
            $style
        );
    }

    /**
     * HTML completo del logo del tenant
     */
    public static function renderTenantLogoHtml(
        $tenantId,
        $isDark = false,
        $withText = false,
        $alt = 'Tenant Logo',
        $class = '',
        $style = ''
    ): string {
        $src = self::getTenantLogo($tenantId, $isDark, $withText);

        return sprintf(
            '<img src="%s" alt="%s" class="logo %s" style="%s" />',
            $src,
            $alt,
            $class,
            $style
        );
    }
}


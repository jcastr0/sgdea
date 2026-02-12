<?php

/**
 * HELPERS GLOBALES PARA LOGOS SGDEA
 * Funciones de utilidad para acceder a logos desde vistas
 */

if (!function_exists('logo')) {
    /**
     * Obtener URL del logo
     * logo() → logo principal
     * logo('white') → logo blanco
     * logo('text') → logo con texto
     * logo('white_text') → logo blanco con texto
     */
    function logo($type = 'main'): string
    {
        return \App\Services\LogoService::getLogoBranding(
            isDark: str_contains($type, 'white'),
            withText: str_contains($type, 'text')
        );
    }
}

if (!function_exists('logo_tag')) {
    /**
     * Renderizar logo como HTML <img>
     * logo_tag() → <img> del logo principal
     * logo_tag('white', 'lg') → <img> del logo blanco grande
     */
    function logo_tag($type = 'main', $size = 'md', $class = ''): string
    {
        $isDark = str_contains($type, 'white');
        $withText = str_contains($type, 'text');

        return \App\Services\LogoService::renderLogoHtml(
            isDark: $isDark,
            withText: $withText,
            class: $class
        );
    }
}

if (!function_exists('logo_html')) {
    /**
     * Alias para logo_tag()
     */
    function logo_html($type = 'main', $size = 'md', $class = ''): string
    {
        return logo_tag($type, $size, $class);
    }
}

if (!function_exists('sgdea_logo')) {
    /**
     * Obtener logo SGDEA default
     */
    function sgdea_logo(): string
    {
        return \App\Services\LogoService::getMainLogo();
    }
}

if (!function_exists('sgdea_logo_white')) {
    /**
     * Obtener logo SGDEA blanco
     */
    function sgdea_logo_white(): string
    {
        return \App\Services\LogoService::getWhiteLogo();
    }
}

if (!function_exists('sgdea_logo_text')) {
    /**
     * Obtener logo SGDEA con texto
     */
    function sgdea_logo_text(): string
    {
        return \App\Services\LogoService::getLogoWithText();
    }
}

if (!function_exists('sgdea_logo_white_text')) {
    /**
     * Obtener logo SGDEA blanco con texto
     */
    function sgdea_logo_white_text(): string
    {
        return \App\Services\LogoService::getWhiteLogoWithText();
    }
}


<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\ThemeConfiguration;
use Illuminate\Database\Seeder;

/**
 * SystemSeeder
 *
 * Crea la configuración base del sistema:
 * - Tenant de demostración
 * - Configuración de tema por defecto
 *
 * NOTA: El superadmin global se crea después con RoleSeeder y UserSeeder
 */
class SystemSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔧 Configurando sistema base...');

        // ========================================
        // 1. CREAR TENANT DE DEMOSTRACIÓN
        // ========================================
        $tenant = Tenant::updateOrCreate(
            ['slug' => 'demo'],
            [
                'name' => 'Empresa Demo SGDEA',
                'domain' => 'demo.sgdea.local',
                'status' => 'active',
            ]
        );
        $this->command->info('   ✅ Tenant demo creado: ' . $tenant->name);

        // ========================================
        // 3. CREAR CONFIGURACIÓN DE TEMA
        // ========================================
        ThemeConfiguration::updateOrCreate(
            ['tenant_id' => $tenant->id],
            [
                // Colores principales
                'color_primary' => '#2563eb',      // Azul
                'color_secondary' => '#0f172a',    // Slate oscuro
                'color_accent' => '#10b981',       // Esmeralda

                // Colores de estado
                'color_error' => '#ef4444',        // Rojo
                'color_success' => '#22c55e',      // Verde
                'color_warning' => '#f59e0b',      // Ámbar

                // Colores de fondo
                'color_bg_light' => '#f8fafc',     // Slate 50
                'color_bg_dark' => '#0f172a',      // Slate 900

                // Colores de texto
                'color_text_primary' => '#1e293b',   // Slate 800
                'color_text_secondary' => '#64748b', // Slate 500
                'color_border' => '#e2e8f0',         // Slate 200

                // Tipografía
                'font_header' => 'Inter, sans-serif',
                'font_body' => 'Inter, sans-serif',
                'font_mono' => 'JetBrains Mono, monospace',

                // Tamaños
                'size_base' => '16px',
                'size_h1' => '2.25rem',
                'size_h2' => '1.875rem',
                'size_h3' => '1.5rem',
                'size_small' => '0.875rem',

                // Espaciado
                'spacing_sm' => '0.5rem',
                'spacing_md' => '1rem',
                'spacing_lg' => '1.5rem',

                // Bordes
                'radius_sm' => '0.375rem',
                'radius_md' => '0.5rem',

                // Efectos
                'shadow_enabled' => true,
                'shadow_intensity' => 'light',
                'gradient_enabled' => false,

                // Logo
                'logo_height' => 40,
                'show_company_name' => true,

                // Modo oscuro
                'dark_mode_enabled' => true,
            ]
        );
        $this->command->info('   ✅ Tema configurado para el tenant');
    }
}


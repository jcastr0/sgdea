<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Tabla Theme Configurations
 *
 * Configuración visual personalizada por tenant.
 * Permite que cada empresa tenga sus propios colores, fuentes y estilos.
 */
return new class extends Migration
{
    public function up(): void
    {
        // =========================================
        // TABLA: theme_configurations
        // Personalización visual por tenant
        // =========================================
        Schema::create('theme_configurations', function (Blueprint $table) {
            $table->id()
                ->comment('ID único de la configuración');

            $table->foreignId('tenant_id')
                ->comment('FK al tenant dueño de esta configuración')
                ->constrained('tenants')
                ->onDelete('cascade');

            // ---- Colores principales ----
            $table->string('color_primary', 7)->default('#2563eb')
                ->comment('Color primario HEX (botones, links activos)');

            $table->string('color_secondary', 7)->default('#0f172a')
                ->comment('Color secundario HEX (sidebar, headers)');

            $table->string('color_accent', 7)->default('#10b981')
                ->comment('Color de acento HEX (badges de éxito, highlights)');

            // ---- Colores de estado ----
            $table->string('color_error', 7)->default('#ef4444')
                ->comment('Color para errores y alertas peligrosas');

            $table->string('color_success', 7)->default('#22c55e')
                ->comment('Color para éxito y confirmaciones');

            $table->string('color_warning', 7)->default('#f59e0b')
                ->comment('Color para advertencias');

            // ---- Colores de fondo ----
            $table->string('color_bg_light', 7)->default('#f8fafc')
                ->comment('Color de fondo en modo claro');

            $table->string('color_bg_dark', 7)->default('#0f172a')
                ->comment('Color de fondo en modo oscuro');

            // ---- Colores de texto ----
            $table->string('color_text_primary', 7)->default('#1e293b')
                ->comment('Color de texto principal');

            $table->string('color_text_secondary', 7)->default('#64748b')
                ->comment('Color de texto secundario/muted');

            $table->string('color_border', 7)->default('#e2e8f0')
                ->comment('Color de bordes');

            // ---- Tipografía ----
            $table->string('font_header')->default('Inter, sans-serif')
                ->comment('Fuente para títulos y encabezados');

            $table->string('font_body')->default('Inter, sans-serif')
                ->comment('Fuente para texto del cuerpo');

            $table->string('font_mono')->default('JetBrains Mono, monospace')
                ->comment('Fuente monoespaciada para código');

            // ---- Tamaños de fuente ----
            $table->string('size_base', 10)->default('16px')
                ->comment('Tamaño base de fuente');

            $table->string('size_h1', 10)->default('2.25rem')
                ->comment('Tamaño de H1');

            $table->string('size_h2', 10)->default('1.875rem')
                ->comment('Tamaño de H2');

            $table->string('size_h3', 10)->default('1.5rem')
                ->comment('Tamaño de H3');

            $table->string('size_small', 10)->default('0.875rem')
                ->comment('Tamaño de texto pequeño');

            // ---- Espaciado ----
            $table->string('spacing_sm', 10)->default('0.5rem')
                ->comment('Espaciado pequeño');

            $table->string('spacing_md', 10)->default('1rem')
                ->comment('Espaciado mediano');

            $table->string('spacing_lg', 10)->default('1.5rem')
                ->comment('Espaciado grande');

            // ---- Bordes ----
            $table->string('radius_sm', 10)->default('0.375rem')
                ->comment('Border radius pequeño');

            $table->string('radius_md', 10)->default('0.5rem')
                ->comment('Border radius mediano');

            // ---- Sombras ----
            $table->boolean('shadow_enabled')->default(true)
                ->comment('Habilitar sombras en elementos');

            $table->enum('shadow_intensity', ['light', 'medium', 'strong'])->default('light')
                ->comment('Intensidad de las sombras');

            // ---- Efectos ----
            $table->boolean('gradient_enabled')->default(false)
                ->comment('Habilitar fondos con gradiente');

            // ---- Logo settings ----
            $table->integer('logo_height')->default(40)
                ->comment('Altura del logo en píxeles');

            $table->boolean('show_company_name')->default(true)
                ->comment('Mostrar nombre de empresa junto al logo');

            // ---- Modo oscuro ----
            $table->boolean('dark_mode_enabled')->default(true)
                ->comment('Permitir a usuarios cambiar a modo oscuro');

            $table->timestamps();

            // ---- Constraints ----
            $table->unique('tenant_id', 'uniq_theme_tenant');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theme_configurations');
    }
};


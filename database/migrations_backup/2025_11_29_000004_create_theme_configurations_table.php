<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('theme_configurations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Colores principales
            $table->string('color_primary')->default('#2767C6');
            $table->string('color_secondary')->default('#102544');
            $table->string('color_error')->default('#B23A3A');
            $table->string('color_success')->default('#009F6B');
            $table->string('color_warning')->default('#F5B400');

            // Colores de fondo y texto
            $table->string('color_bg_light')->default('#F5F7FA');
            $table->string('color_bg_dark')->default('#102544');
            $table->string('color_text_primary')->default('#1F2933');
            $table->string('color_text_secondary')->default('#6B7280');
            $table->string('color_border')->default('#D4D9E2');

            // Tipografía
            $table->string('font_header')->default('Inter, Roboto, sans-serif');
            $table->string('font_body')->default('Inter, Roboto, sans-serif');
            $table->string('font_mono')->default('Courier New, monospace');

            // Tamaños
            $table->string('size_base')->default('16px');
            $table->string('size_h1')->default('2.25rem');
            $table->string('size_h2')->default('1.75rem');
            $table->string('size_h3')->default('1.5rem');
            $table->string('size_small')->default('0.875rem');

            // Espaciado
            $table->string('spacing_sm')->default('8px');
            $table->string('spacing_md')->default('16px');
            $table->string('spacing_lg')->default('24px');

            // Bordes y sombras
            $table->string('radius_sm')->default('6px');
            $table->string('radius_md')->default('8px');
            $table->boolean('shadow_enabled')->default(true);
            $table->string('shadow_intensity')->default('light'); // light, medium, strong
            $table->boolean('gradient_enabled')->default(false);

            // Logo y branding
            $table->integer('logo_height')->default(40);
            $table->boolean('show_company_name')->default(true);

            $table->timestamps();
            $table->unique('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theme_configurations');
    }
};


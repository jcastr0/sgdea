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
        // Actualizar tabla tenants con campos para logos y favicons
        Schema::table('tenants', function (Blueprint $table) {
            // Si no existen, agregarlas
            if (!Schema::hasColumn('tenants', 'logo_path_light')) {
                $table->string('logo_path_light')->nullable()->comment('Logo para fondo claro');
            }
            if (!Schema::hasColumn('tenants', 'logo_path_dark')) {
                $table->string('logo_path_dark')->nullable()->comment('Logo para fondo oscuro');
            }
            if (!Schema::hasColumn('tenants', 'favicon_path')) {
                $table->string('favicon_path')->nullable()->comment('Favicon de la empresa');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'logo_path_light',
                'logo_path_dark',
                'favicon_path',
            ]);
        });
    }
};


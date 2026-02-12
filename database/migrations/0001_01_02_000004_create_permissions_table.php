<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Tabla Permissions
 *
 * Define los permisos disponibles en el sistema.
 * Los permisos son globales y se asignan a roles.
 */
return new class extends Migration
{
    public function up(): void
    {
        // =========================================
        // TABLA: permissions
        // Permisos del sistema (globales)
        // =========================================
        Schema::create('permissions', function (Blueprint $table) {
            $table->id()
                ->comment('ID único del permiso');

            $table->string('name')->unique()
                ->comment('Nombre técnico único (ej: factura.crear, usuario.aprobar)');

            $table->string('slug')->unique()
                ->comment('Slug normalizado del permiso');

            $table->string('display_name')
                ->comment('Nombre legible para mostrar en UI');

            $table->text('description')->nullable()
                ->comment('Descripción detallada del permiso');

            $table->string('resource', 50)
                ->comment('Recurso al que aplica (factura, tercero, usuario, etc)');

            $table->string('action', 50)
                ->comment('Acción que permite (ver, crear, editar, eliminar, etc)');

            $table->timestamps();

            // ---- Índices ----
            $table->index('resource', 'idx_permissions_resource');
            $table->index('action', 'idx_permissions_action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};


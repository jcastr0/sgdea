<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Tabla Roles
 *
 * Define los roles disponibles por cada tenant.
 * Cada tenant puede tener sus propios roles personalizados.
 */
return new class extends Migration
{
    public function up(): void
    {
        // =========================================
        // TABLA: roles
        // Roles de usuario por tenant
        // =========================================
        Schema::create('roles', function (Blueprint $table) {
            $table->id()
                ->comment('ID único del rol');

            $table->foreignId('tenant_id')
                ->nullable()
                ->comment('FK al tenant dueño del rol. NULL = rol global del sistema')
                ->constrained('tenants')
                ->onDelete('cascade');

            $table->string('name')
                ->comment('Nombre del rol (ej: Administrador, Contador)');

            $table->string('slug')
                ->comment('Slug normalizado (ej: administrador, contador)');

            $table->text('description')->nullable()
                ->comment('Descripción del rol y sus responsabilidades');

            $table->boolean('is_system')->default(false)
                ->comment('TRUE si es un rol del sistema que no se puede eliminar');

            $table->boolean('is_default')->default(false)
                ->comment('TRUE si es el rol por defecto para nuevos usuarios');

            $table->integer('priority')->default(0)
                ->comment('Prioridad del rol (mayor=más permisos)');

            $table->timestamps();

            // ---- Índices ----
            $table->unique(['tenant_id', 'slug'], 'uniq_role_tenant_slug');
            $table->index(['tenant_id', 'name'], 'idx_roles_tenant_name');
        });

        // =========================================
        // TABLA PIVOTE: role_permission
        // Relación muchos a muchos entre roles y permisos
        // =========================================
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id()
                ->comment('ID único de la relación');

            $table->foreignId('role_id')
                ->comment('FK al rol')
                ->constrained('roles')
                ->onDelete('cascade');

            $table->foreignId('permission_id')
                ->comment('FK al permiso')
                ->constrained('permissions')
                ->onDelete('cascade');

            $table->timestamps();

            // ---- Constraints ----
            $table->unique(['role_id', 'permission_id'], 'uniq_role_permission');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('roles');
    }
};


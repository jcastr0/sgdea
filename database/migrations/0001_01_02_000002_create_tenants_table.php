<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Tabla Tenants (Empresas/Organizaciones)
 *
 * Cada tenant representa una empresa u organización que usa el sistema.
 * El sistema es multi-tenant, donde cada empresa tiene sus propios datos aislados.
 */
return new class extends Migration
{
    public function up(): void
    {
        // =========================================
        // TABLA: tenants
        // Empresas/organizaciones del sistema
        // =========================================
        Schema::create('tenants', function (Blueprint $table) {
            $table->id()
                ->comment('ID único del tenant');

            // ---- Información básica ----
            $table->string('name')
                ->comment('Nombre comercial de la empresa');

            $table->string('slug')->unique()
                ->comment('Identificador URL-friendly único (ej: maritimos-arboleda)');

            $table->string('domain')->unique()->nullable()
                ->comment('Dominio personalizado (ej: facturacion.miempresa.com)');

            $table->string('database_name')->nullable()
                ->comment('Nombre de BD separada si usa multi-database (futuro)');

            // ---- Estado ----
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')
                ->comment('active=operativo, inactive=deshabilitado, suspended=suspendido por pago');

            // ---- Administrador ----
            $table->unsignedBigInteger('superadmin_id')->nullable()
                ->comment('FK al system_user que administra este tenant');

            // ---- Branding/Logos ----
            $table->string('logo_path')->nullable()
                ->comment('Ruta al logo principal de la empresa');

            $table->string('logo_path_light')->nullable()
                ->comment('Logo versión clara (para fondos oscuros)');

            $table->string('logo_path_dark')->nullable()
                ->comment('Logo versión oscura (para fondos claros)');

            $table->string('favicon_path')->nullable()
                ->comment('Ruta al favicon de la empresa');

            $table->timestamps();

            // ---- Índices ----
            $table->index('domain', 'idx_tenants_domain');
            $table->index('status', 'idx_tenants_status');
            $table->index('superadmin_id', 'idx_tenants_superadmin');

            // ---- Foreign Keys ----
            $table->foreign('superadmin_id')
                ->references('id')->on('system_users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};


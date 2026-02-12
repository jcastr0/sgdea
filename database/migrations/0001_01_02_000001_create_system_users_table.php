<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Tabla System Users (Administradores Globales)
 *
 * Administradores del sistema que pueden gestionar múltiples tenants.
 * Estos usuarios NO pertenecen a ningún tenant específico.
 */
return new class extends Migration
{
    public function up(): void
    {
        // =========================================
        // TABLA: system_users
        // Superadministradores globales del sistema
        // =========================================
        Schema::create('system_users', function (Blueprint $table) {
            $table->id()
                ->comment('ID único del administrador global');

            $table->string('name')
                ->comment('Nombre completo del administrador');

            $table->string('email')->unique()
                ->comment('Email único para login (ej: admin@sgdea.com)');

            $table->timestamp('email_verified_at')->nullable()
                ->comment('Fecha de verificación del email');

            $table->string('password')
                ->comment('Contraseña hasheada con bcrypt');

            $table->enum('status', ['active', 'inactive'])->default('active')
                ->comment('Estado: active=puede acceder, inactive=bloqueado');

            $table->rememberToken()
                ->comment('Token para "Recordarme" en login');

            $table->timestamps();

            // Índices
            $table->index('status', 'idx_system_users_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_users');
    }
};


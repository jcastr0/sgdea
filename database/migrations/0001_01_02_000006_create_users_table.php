<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Tabla Users (Usuarios del Sistema)
 *
 * Usuarios que pertenecen a un tenant específico.
 * Incluye autenticación, perfil y estado de cuenta.
 */
return new class extends Migration
{
    public function up(): void
    {
        // =========================================
        // TABLA: users
        // Usuarios del sistema por tenant
        // =========================================
        Schema::create('users', function (Blueprint $table) {
            $table->id()
                ->comment('ID único del usuario');

            // ---- Relaciones ----
            $table->foreignId('tenant_id')->nullable()
                ->comment('FK al tenant al que pertenece el usuario')
                ->constrained('tenants')
                ->onDelete('cascade');

            $table->foreignId('role_id')->nullable()
                ->comment('FK al rol asignado al usuario')
                ->constrained('roles')
                ->onDelete('set null');

            // ---- Información básica ----
            $table->string('name')
                ->comment('Nombre completo del usuario');

            $table->string('email')->unique()
                ->comment('Email único para login');

            $table->timestamp('email_verified_at')->nullable()
                ->comment('Fecha de verificación del email');

            $table->string('password')
                ->comment('Contraseña hasheada con bcrypt');

            // ---- Información de perfil ----
            $table->string('phone', 20)->nullable()
                ->comment('Teléfono de contacto');

            $table->string('department', 100)->nullable()
                ->comment('Departamento o área (ej: Contabilidad, Ventas)');

            $table->json('preferences')->nullable()
                ->comment('Preferencias del usuario (tema, idioma, notificaciones) en JSON');

            // ---- Estado de cuenta ----
            $table->enum('status', ['active', 'pending_approval', 'blocked', 'inactive'])
                ->default('pending_approval')
                ->comment('active=puede acceder, pending_approval=espera aprobación, blocked=bloqueado, inactive=desactivado');

            // ---- Aprobación ----
            $table->unsignedBigInteger('approved_by')->nullable()
                ->comment('ID del usuario que aprobó esta cuenta');

            $table->timestamp('approved_at')->nullable()
                ->comment('Fecha/hora de aprobación');

            // ---- Información de acceso ----
            $table->timestamp('last_login_at')->nullable()
                ->comment('Fecha/hora del último login');

            $table->string('last_login_ip', 45)->nullable()
                ->comment('Dirección IP del último login');

            // ---- Laravel auth ----
            $table->rememberToken()
                ->comment('Token para "Recordarme" en login');

            $table->timestamps();

            // ---- Índices ----
            $table->index('tenant_id', 'idx_users_tenant');
            $table->index('role_id', 'idx_users_role');
            $table->index('status', 'idx_users_status');
            $table->index(['tenant_id', 'email'], 'idx_users_tenant_email');

            // ---- Foreign Key para approved_by (self-reference) ----
            $table->foreign('approved_by')
                ->references('id')->on('users')
                ->onDelete('set null');
        });

        // =========================================
        // TABLA: password_reset_tokens
        // Tokens para reseteo de contraseña
        // =========================================
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary()
                ->comment('Email del usuario');

            $table->string('token')
                ->comment('Token único de reseteo');

            $table->timestamp('created_at')->nullable()
                ->comment('Fecha de creación del token');
        });

        // =========================================
        // TABLA: sessions
        // Sesiones activas de usuarios
        // =========================================
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary()
                ->comment('ID único de la sesión');

            $table->foreignId('user_id')->nullable()->index()
                ->comment('FK al usuario (null si no autenticado)');

            $table->string('ip_address', 45)->nullable()
                ->comment('Dirección IP del cliente');

            $table->text('user_agent')->nullable()
                ->comment('User agent del navegador');

            $table->longText('payload')
                ->comment('Datos de la sesión serializados');

            $table->integer('last_activity')->index()
                ->comment('Timestamp UNIX de última actividad');
        });

        // =========================================
        // TABLA PIVOTE: user_role
        // Relación muchos a muchos entre usuarios y roles (para roles múltiples)
        // =========================================
        Schema::create('user_role', function (Blueprint $table) {
            $table->id()
                ->comment('ID único de la relación');

            $table->foreignId('user_id')
                ->comment('FK al usuario')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('role_id')
                ->comment('FK al rol')
                ->constrained('roles')
                ->onDelete('cascade');

            $table->timestamps();

            // ---- Constraints ----
            $table->unique(['user_id', 'role_id'], 'uniq_user_role');
        });

        // =========================================
        // FK DIFERIDA: tenants.created_by -> users.id
        // Se agrega aquí porque tenants se crea antes que users
        // =========================================
        Schema::table('tenants', function (Blueprint $table) {
            $table->foreign('created_by')
                ->references('id')->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Eliminar FK de tenants primero
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        Schema::dropIfExists('user_role');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};


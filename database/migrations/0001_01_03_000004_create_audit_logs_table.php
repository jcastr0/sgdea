<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Tabla Audit Logs
 *
 * Registro de auditoría de todas las acciones del sistema.
 * Permite rastrear quién hizo qué, cuándo y desde dónde.
 *
 * CARACTERÍSTICAS:
 * - Los registros son INMUTABLES (no se pueden modificar ni eliminar)
 * - Solo tiene created_at (sin updated_at)
 * - El usuario SYSTEM (id=1) se usa para acciones sin usuario autenticado
 * - Incluye hash SHA256 para verificar integridad
 */
return new class extends Migration
{
    public function up(): void
    {
        // =========================================
        // TABLA: audit_logs
        // Registro de auditoría inmutable
        // =========================================
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id()
                ->comment('ID único del registro de auditoría');

            // ---- Relaciones ----
            $table->foreignId('tenant_id')
                ->nullable()
                ->comment('FK al tenant donde ocurrió la acción (NULL para acciones globales o sin autenticación)')
                ->constrained('tenants')
                ->onDelete('cascade');

            $table->unsignedBigInteger('user_id')
                ->comment('FK al usuario que realizó la acción (1=SYSTEM para acciones sin auth)');

            // ---- Información de la acción ----
            $table->string('action', 50)
                ->comment('Acción: login, login_failed, logout, create, update, delete, restore, view, export, import, execute');

            $table->string('model_type', 100)->nullable()
                ->comment('Clase completa del modelo afectado (App\\Models\\Factura)');

            $table->unsignedBigInteger('model_id')->nullable()
                ->comment('ID del registro afectado');

            // ---- Valores para rastrear cambios ----
            $table->json('old_values')->nullable()
                ->comment('Valores anteriores al cambio (JSON)');

            $table->json('new_values')->nullable()
                ->comment('Valores nuevos después del cambio (JSON)');

            // ---- Información del request ----
            $table->string('ip_address', 45)->nullable()
                ->comment('Dirección IP desde donde se realizó la acción');

            $table->text('user_agent')->nullable()
                ->comment('User agent del navegador/cliente');

            $table->string('url', 2048)->nullable()
                ->comment('URL completa del request');

            $table->string('method', 10)->nullable()
                ->comment('Método HTTP: GET, POST, PUT, DELETE, etc');

            // ---- Contexto adicional ----
            $table->json('context')->nullable()
                ->comment('Datos adicionales relevantes (JSON): source, accion_especial, etc');

            // ---- Integridad ----
            $table->string('hash', 64)->nullable()
                ->comment('Hash SHA256 del registro para verificar integridad');

            // ---- Timestamp (solo created_at, es inmutable) ----
            $table->timestamp('created_at')->useCurrent()
                ->comment('Fecha/hora de la acción (INMUTABLE)');

            // ---- Índices para búsqueda rápida ----
            $table->index('tenant_id', 'idx_audit_tenant');
            $table->index('user_id', 'idx_audit_user');
            $table->index('action', 'idx_audit_action');
            $table->index('model_type', 'idx_audit_model_type');
            $table->index('created_at', 'idx_audit_date');
            $table->index(['tenant_id', 'created_at'], 'idx_audit_tenant_date');
            $table->index(['model_type', 'model_id'], 'idx_audit_model');
            $table->index(['tenant_id', 'action'], 'idx_audit_tenant_action');

            // ---- Foreign Key ----
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};


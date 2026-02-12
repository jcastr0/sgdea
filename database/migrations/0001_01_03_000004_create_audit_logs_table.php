<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Tabla Audit Logs
 *
 * Registro de auditoría de todas las acciones del sistema.
 * Permite rastrear quién hizo qué, cuándo y desde dónde.
 * Los registros son inmutables (no se pueden modificar ni eliminar).
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
                ->comment('FK al tenant donde ocurrió la acción')
                ->constrained('tenants')
                ->onDelete('cascade');

            $table->foreignId('user_id')->nullable()
                ->comment('FK al usuario que realizó la acción (null si fue sistema)')
                ->constrained('users')
                ->onDelete('set null');

            // ---- Información de la acción ----
            $table->string('action', 50)
                ->comment('Acción realizada: create, update, delete, login, logout, approve, reject, import, export');

            $table->string('entity_type', 50)
                ->comment('Tipo de entidad afectada: factura, tercero, usuario, importacion, etc');

            $table->unsignedBigInteger('entity_id')->nullable()
                ->comment('ID de la entidad afectada');

            // ---- Valores para rastrear cambios ----
            $table->json('old_values')->nullable()
                ->comment('Valores anteriores al cambio (JSON)');

            $table->json('new_values')->nullable()
                ->comment('Valores nuevos después del cambio (JSON)');

            // ---- Información de seguridad ----
            $table->string('ip_address', 45)->nullable()
                ->comment('Dirección IP desde donde se realizó la acción');

            $table->text('user_agent')->nullable()
                ->comment('User agent del navegador');

            // ---- Integridad ----
            $table->string('hash', 64)->nullable()
                ->comment('Hash SHA256 del registro para verificar integridad');

            // ---- Descripción legible ----
            $table->text('description')->nullable()
                ->comment('Descripción legible de la acción realizada');

            // ---- Metadata adicional ----
            $table->json('metadata')->nullable()
                ->comment('Datos adicionales relevantes (JSON)');

            // ---- Timestamp (solo created_at, es inmutable) ----
            $table->timestamp('created_at')->useCurrent()
                ->comment('Fecha/hora de la acción (inmutable)');

            // ---- Índices para búsqueda rápida ----
            $table->index('tenant_id', 'idx_audit_tenant');
            $table->index('user_id', 'idx_audit_user');
            $table->index('action', 'idx_audit_action');
            $table->index('entity_type', 'idx_audit_entity_type');
            $table->index('created_at', 'idx_audit_date');
            $table->index(['tenant_id', 'created_at'], 'idx_audit_tenant_date');
            $table->index(['entity_type', 'entity_id'], 'idx_audit_entity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};


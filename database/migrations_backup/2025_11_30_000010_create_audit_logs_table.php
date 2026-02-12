<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Información de la acción
            $table->string('action'); // create, update, delete, approve, reject, login, logout, etc
            $table->string('entity_type'); // factura, tercero, usuario, importacion, etc
            $table->unsignedBigInteger('entity_id')->nullable();

            // Valores antes y después (para rastrear cambios)
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            // Información de seguridad
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            // Hash para verificar integridad (inalterable)
            $table->string('hash')->nullable();

            // Descripción legible
            $table->text('description')->nullable();

            // Timestamp (inmutable)
            $table->timestamp('created_at')->useCurrent();

            // Índices para búsqueda rápida
            $table->index('tenant_id');
            $table->index('user_id');
            $table->index('action');
            $table->index('entity_type');
            $table->index('created_at');
            $table->index(['tenant_id', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
        });

        // En MySQL la inmutabilidad se maneja a nivel aplicación
        // No se permiten updates ni deletes desde el modelo AuditLog
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};


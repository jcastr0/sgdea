<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Tablas de Setup/Configuración Inicial
 *
 * Tablas para el asistente de configuración inicial del sistema.
 * Rastrean el progreso del setup y los checkpoints completados.
 */
return new class extends Migration
{
    public function up(): void
    {
        // =========================================
        // TABLA: setup_checkpoints
        // Checkpoints del asistente de configuración
        // =========================================
        Schema::create('setup_checkpoints', function (Blueprint $table) {
            $table->id()
                ->comment('ID único del checkpoint');

            $table->string('key')->unique()
                ->comment('Identificador único del checkpoint (ej: database_configured, admin_created)');

            $table->string('name')
                ->comment('Nombre legible del checkpoint');

            $table->text('description')->nullable()
                ->comment('Descripción de lo que hace este checkpoint');

            $table->integer('order')->default(0)
                ->comment('Orden de ejecución del checkpoint');

            $table->boolean('is_required')->default(true)
                ->comment('TRUE si es obligatorio completarlo');

            $table->boolean('is_completed')->default(false)
                ->comment('TRUE si ya se completó');

            $table->timestamp('completed_at')->nullable()
                ->comment('Fecha/hora cuando se completó');

            $table->json('metadata')->nullable()
                ->comment('Datos adicionales del checkpoint (JSON)');

            $table->timestamps();

            // ---- Índices ----
            $table->index('order', 'idx_setup_checkpoints_order');
            $table->index('is_completed', 'idx_setup_checkpoints_completed');
        });

        // =========================================
        // TABLA: setup_progress
        // Progreso general del setup
        // =========================================
        Schema::create('setup_progress', function (Blueprint $table) {
            $table->id()
                ->comment('ID único del progreso');

            $table->string('current_step')
                ->comment('Paso actual del setup');

            $table->integer('step_number')->default(1)
                ->comment('Número del paso actual');

            $table->integer('total_steps')->default(5)
                ->comment('Total de pasos del setup');

            $table->boolean('is_completed')->default(false)
                ->comment('TRUE si el setup está completo');

            $table->timestamp('started_at')->nullable()
                ->comment('Fecha/hora de inicio del setup');

            $table->timestamp('completed_at')->nullable()
                ->comment('Fecha/hora de finalización');

            $table->json('data')->nullable()
                ->comment('Datos recopilados durante el setup (JSON)');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setup_progress');
        Schema::dropIfExists('setup_checkpoints');
    }
};


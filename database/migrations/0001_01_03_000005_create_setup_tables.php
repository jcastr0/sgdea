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

            $table->string('step_key')->unique()
                ->comment('Identificador único del checkpoint (ej: setup_step_superadmin_created)');

            $table->string('step_name')
                ->comment('Nombre legible del checkpoint');

            $table->integer('step_order')->default(0)
                ->comment('Orden de ejecución del checkpoint');

            $table->string('phase')->default('FASE_1')
                ->comment('Fase del setup (FASE_1, FASE_2, etc)');

            $table->string('component')->nullable()
                ->comment('Componente o descripción de lo que hace');

            $table->string('status')->default('pending')
                ->comment('Estado: pending, completed, skipped, error');

            $table->boolean('optional')->default(false)
                ->comment('TRUE si es opcional');

            $table->timestamp('completion_date')->nullable()
                ->comment('Fecha/hora cuando se completó');

            $table->text('error_message')->nullable()
                ->comment('Mensaje de error si falló');

            $table->timestamps();

            // ---- Índices ----
            $table->index('step_order', 'idx_setup_checkpoints_order');
            $table->index('status', 'idx_setup_checkpoints_status');
        });

        // =========================================
        // TABLA: setup_progress
        // Progreso general del setup
        // =========================================
        Schema::create('setup_progress', function (Blueprint $table) {
            $table->id()
                ->comment('ID único del progreso');

            $table->integer('current_step')->default(1)
                ->comment('Número del paso actual');

            $table->integer('total_steps')->default(7)
                ->comment('Total de pasos del setup');

            $table->integer('percentage')->default(0)
                ->comment('Porcentaje de progreso (0-100)');

            $table->string('last_completed_phase')->nullable()
                ->comment('Última fase completada');

            $table->json('progress_data')->nullable()
                ->comment('Datos adicionales del progreso (JSON)');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setup_progress');
        Schema::dropIfExists('setup_checkpoints');
    }
};


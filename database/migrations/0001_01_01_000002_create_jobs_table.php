<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Tablas de Jobs/Colas
 *
 * Crea las tablas necesarias para el sistema de colas de Laravel.
 * Incluye jobs pendientes, jobs fallidos y batches.
 */
return new class extends Migration
{
    public function up(): void
    {
        // =========================================
        // TABLA: jobs
        // Jobs pendientes en la cola
        // =========================================
        Schema::create('jobs', function (Blueprint $table) {
            $table->id()
                ->comment('ID único del job');

            $table->string('queue')
                ->index()
                ->comment('Nombre de la cola (default, high, low, etc)');

            $table->longText('payload')
                ->comment('Datos serializados del job');

            $table->unsignedTinyInteger('attempts')
                ->comment('Número de intentos de ejecución');

            $table->unsignedInteger('reserved_at')->nullable()
                ->comment('Timestamp cuando fue reservado por un worker');

            $table->unsignedInteger('available_at')
                ->comment('Timestamp cuando estará disponible para procesar');

            $table->unsignedInteger('created_at')
                ->comment('Timestamp de creación');
        });

        // =========================================
        // TABLA: job_batches
        // Batches de jobs agrupados
        // =========================================
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary()
                ->comment('UUID único del batch');

            $table->string('name')
                ->comment('Nombre descriptivo del batch');

            $table->integer('total_jobs')
                ->comment('Total de jobs en el batch');

            $table->integer('pending_jobs')
                ->comment('Jobs pendientes de procesar');

            $table->integer('failed_jobs')
                ->comment('Jobs que fallaron');

            $table->longText('failed_job_ids')
                ->comment('IDs de jobs fallidos (JSON)');

            $table->mediumText('options')->nullable()
                ->comment('Opciones del batch (JSON)');

            $table->integer('cancelled_at')->nullable()
                ->comment('Timestamp de cancelación');

            $table->integer('created_at')
                ->comment('Timestamp de creación');

            $table->integer('finished_at')->nullable()
                ->comment('Timestamp de finalización');
        });

        // =========================================
        // TABLA: failed_jobs
        // Jobs que fallaron después de todos los reintentos
        // =========================================
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id()
                ->comment('ID único del registro');

            $table->string('uuid')->unique()
                ->comment('UUID único del job fallido');

            $table->text('connection')
                ->comment('Conexión de cola utilizada');

            $table->text('queue')
                ->comment('Nombre de la cola');

            $table->longText('payload')
                ->comment('Datos serializados del job');

            $table->longText('exception')
                ->comment('Mensaje y trace del error');

            $table->timestamp('failed_at')->useCurrent()
                ->comment('Fecha/hora del fallo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
    }
};


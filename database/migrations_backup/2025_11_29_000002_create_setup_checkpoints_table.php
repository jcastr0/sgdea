<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('setup_checkpoints', function (Blueprint $table) {
            $table->id();
            $table->string('step_key')->unique(); // Clave única del paso (ej: setup_step_superadmin_created)
            $table->string('step_name'); // Nombre visible (ej: "Crear Superadmin")
            $table->integer('step_order'); // Orden de ejecución (1, 2, 3...)
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('phase'); // Fase del setup (FASE_1, FASE_2, etc)
            $table->string('component'); // Componente asociado (superadmin, database, tenant, etc)
            $table->timestamp('completion_date')->nullable(); // Fecha/hora de completitud
            $table->text('error_message')->nullable(); // Mensaje de error si falló
            $table->timestamps();

            $table->index('status');
            $table->index('phase');
            $table->index('step_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setup_checkpoints');
    }
};


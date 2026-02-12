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
        Schema::create('setup_progress', function (Blueprint $table) {
            $table->id();
            $table->integer('current_step')->default(1); // Paso actual
            $table->integer('total_steps')->default(6); // Total de pasos en FASE_1
            $table->integer('percentage')->default(0); // Porcentaje completado (0-100)
            $table->string('last_completed_phase')->nullable(); // Última fase completada
            $table->json('progress_data')->nullable(); // Datos adicionales en formato JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setup_progress');
    }
};


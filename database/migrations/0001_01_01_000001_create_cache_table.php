<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Tablas de Cache
 *
 * Crea las tablas necesarias para el sistema de caché de Laravel.
 * Incluye caché general y locks para operaciones atómicas.
 */
return new class extends Migration
{
    public function up(): void
    {
        // =========================================
        // TABLA: cache
        // Almacena datos en caché del sistema
        // =========================================
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary()
                ->comment('Clave única del ítem en caché');

            $table->mediumText('value')
                ->comment('Valor serializado almacenado en caché');

            $table->integer('expiration')
                ->comment('Timestamp UNIX de expiración');
        });

        // =========================================
        // TABLA: cache_locks
        // Locks para operaciones atómicas de caché
        // =========================================
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary()
                ->comment('Clave del lock');

            $table->string('owner')
                ->comment('Identificador del propietario del lock');

            $table->integer('expiration')
                ->comment('Timestamp UNIX de expiración del lock');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
    }
};


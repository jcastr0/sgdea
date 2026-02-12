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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre de la empresa
            $table->string('slug')->unique(); // identificador único (ej: maritimos-arboleda)
            $table->string('domain')->unique(); // Dominio principal (ej: maritimosarboleda.com)
            $table->string('database_name')->nullable(); // Nombre de BD si usa multi-db
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('superadmin_id')->nullable(); // FK a system_users
            $table->string('logo_path')->nullable(); // Ruta del logo
            $table->string('favicon_path')->nullable(); // Ruta del favicon
            $table->timestamps();

            $table->index('domain');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};


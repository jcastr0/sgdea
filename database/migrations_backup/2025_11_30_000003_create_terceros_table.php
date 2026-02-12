<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terceros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('nit', 20);
            $table->string('nombre_razon_social');
            $table->string('direccion')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('notas')->nullable();
            $table->string('estado', 20)->default('activo');
            $table->timestamps();

            $table->index('tenant_id');
            $table->unique(['tenant_id', 'nit']);
            $table->index(['tenant_id', 'nombre_razon_social']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terceros');
    }
};


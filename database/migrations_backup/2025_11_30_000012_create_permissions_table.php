<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // 'factura.crear', 'usuario.aprobar', etc
            $table->string('slug')->unique(); // mismo que name, normalizado
            $table->text('description')->nullable();
            $table->string('resource'); // 'factura', 'usuario', 'auditoria', etc
            $table->timestamps();

            $table->index('resource');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};


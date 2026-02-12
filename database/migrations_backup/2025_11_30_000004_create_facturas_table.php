<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('tercero_id')->constrained('terceros')->onDelete('cascade');

            // Identificadores
            $table->string('cufe', 255);
            $table->string('numero_factura', 50);

            // Fechas
            $table->dateTime('fecha_factura');
            $table->date('fecha_vencimiento')->nullable();

            // Montos
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('iva', 15, 2)->default(0);
            $table->decimal('descuento', 15, 2)->default(0);
            $table->decimal('total_pagar', 15, 2)->default(0);

            // Información adicional
            $table->string('motonave', 255)->nullable();
            $table->string('trb', 255)->nullable();
            $table->text('servicio_descripcion')->nullable();

            // Archivo PDF
            $table->string('pdf_path', 255)->nullable();
            $table->string('hash_pdf', 255)->nullable();

            // Estado
            $table->string('estado', 20)->default('pendiente'); // pendiente, pagada, cancelada

            $table->timestamps();

            // Índices
            $table->index('tenant_id');
            $table->index('tercero_id');
            $table->index('fecha_factura');
            $table->index('estado');
            $table->unique(['tenant_id', 'cufe']);
            $table->index(['tenant_id', 'numero_factura']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};


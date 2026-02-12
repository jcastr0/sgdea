<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Tabla Facturas
 *
 * Facturas electrónicas importadas al sistema.
 * Cada factura está asociada a un tercero (cliente) y puede tener un PDF adjunto.
 */
return new class extends Migration
{
    public function up(): void
    {
        // =========================================
        // TABLA: facturas
        // Facturas electrónicas por tenant
        // =========================================
        Schema::create('facturas', function (Blueprint $table) {
            $table->id()
                ->comment('ID único de la factura');

            // ---- Relaciones ----
            $table->foreignId('tenant_id')
                ->comment('FK al tenant dueño de la factura')
                ->constrained('tenants')
                ->onDelete('cascade');

            $table->foreignId('tercero_id')
                ->comment('FK al tercero (cliente) de la factura')
                ->constrained('terceros')
                ->onDelete('cascade');

            // ---- Identificadores únicos ----
            $table->string('cufe', 255)
                ->comment('Código Único de Factura Electrónica (DIAN Colombia)');

            $table->string('numero_factura', 50)
                ->comment('Número de la factura (ej: TRMC231)');

            $table->string('prefijo', 10)->nullable()
                ->comment('Prefijo de la factura (ej: TRMC)');

            // ---- Tipo de documento ----
            $table->enum('tipo_documento', ['FACTURA DE VENTA', 'NOTA CREDITO', 'NOTA DEBITO'])
                ->default('FACTURA DE VENTA')
                ->comment('Tipo de documento fiscal');

            // ---- Fechas ----
            $table->dateTime('fecha_factura')
                ->comment('Fecha de emisión de la factura');

            $table->date('fecha_vencimiento')->nullable()
                ->comment('Fecha límite de pago');

            // ---- Montos ----
            $table->decimal('subtotal', 18, 2)->default(0)
                ->comment('Valor antes de impuestos');

            $table->decimal('iva', 18, 2)->default(0)
                ->comment('Valor del IVA');

            $table->decimal('descuento', 18, 2)->default(0)
                ->comment('Valor de descuentos aplicados');

            $table->decimal('total_pagar', 18, 2)->default(0)
                ->comment('Valor total a pagar (subtotal + iva - descuento)');

            // ---- Moneda ----
            $table->string('moneda', 3)->default('COP')
                ->comment('Código de moneda ISO 4217 (COP, USD, EUR)');

            // ---- Información adicional (extraída del PDF) ----
            $table->string('motonave', 255)->nullable()
                ->comment('Nombre de la motonave (para facturas marítimas)');

            $table->string('trb', 50)->nullable()
                ->comment('TRB - Tonelaje de Registro Bruto');

            $table->string('locacion', 255)->nullable()
                ->comment('Ubicación/puerto donde se prestó el servicio');

            $table->text('servicio_descripcion')->nullable()
                ->comment('Descripción del servicio facturado');

            // ---- Forma de pago ----
            $table->string('forma_pago', 100)->nullable()
                ->comment('Forma de pago: Contado, Crédito, etc');

            // ---- Estado comercial ----
            $table->enum('estado', ['pendiente', 'aceptado', 'rechazado', 'pagado', 'anulado'])
                ->default('pendiente')
                ->comment('Estado comercial de la factura');

            $table->text('motivo_rechazo')->nullable()
                ->comment('Motivo si la factura fue rechazada');

            // ---- Archivo PDF ----
            $table->string('pdf_path', 255)->nullable()
                ->comment('Ruta al archivo PDF de la factura');

            $table->string('pdf_original_name', 255)->nullable()
                ->comment('Nombre original del archivo PDF subido');

            $table->string('hash_pdf', 64)->nullable()
                ->comment('Hash SHA256 del PDF para verificar integridad');

            $table->integer('pdf_paginas')->nullable()
                ->comment('Número de páginas del PDF');

            // ---- Importación ----
            $table->unsignedBigInteger('import_log_id')->nullable()
                ->comment('FK al log de importación que creó este registro');

            $table->timestamps();

            // ---- Índices ----
            $table->unique(['tenant_id', 'cufe'], 'uniq_factura_cufe');
            $table->index(['tenant_id', 'numero_factura'], 'idx_factura_numero');
            $table->index(['tenant_id', 'tercero_id'], 'idx_factura_tercero');
            $table->index(['tenant_id', 'fecha_factura'], 'idx_factura_fecha');
            $table->index(['tenant_id', 'estado'], 'idx_factura_estado');
            $table->index('import_log_id', 'idx_factura_import');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Tabla Terceros (Clientes/Proveedores)
 *
 * Terceros son las empresas o personas con las que se tienen
 * relaciones comerciales (clientes que reciben facturas).
 */
return new class extends Migration
{
    public function up(): void
    {
        // =========================================
        // TABLA: terceros
        // Clientes y proveedores por tenant
        // =========================================
        Schema::create('terceros', function (Blueprint $table) {
            $table->id()
                ->comment('ID único del tercero');

            $table->foreignId('tenant_id')
                ->comment('FK al tenant dueño del tercero')
                ->constrained('tenants')
                ->onDelete('cascade');

            // ---- Identificación ----
            $table->string('nit', 20)
                ->comment('NIT o documento de identificación tributaria');

            $table->string('tipo_documento', 20)->default('NIT')
                ->comment('Tipo de documento: NIT, CC, CE, PASAPORTE');

            $table->string('digito_verificacion', 1)->nullable()
                ->comment('Dígito de verificación del NIT');

            // ---- Información básica ----
            $table->string('nombre_razon_social')
                ->comment('Nombre completo o razón social');

            $table->string('nombre_comercial')->nullable()
                ->comment('Nombre comercial si es diferente a razón social');

            // ---- Contacto ----
            $table->string('direccion')->nullable()
                ->comment('Dirección física principal');

            $table->string('ciudad', 100)->nullable()
                ->comment('Ciudad de ubicación');

            $table->string('departamento', 100)->nullable()
                ->comment('Departamento/Estado/Provincia');

            $table->string('pais', 100)->default('Colombia')
                ->comment('País de ubicación');

            $table->string('codigo_postal', 20)->nullable()
                ->comment('Código postal');

            $table->string('telefono', 30)->nullable()
                ->comment('Teléfono principal de contacto');

            $table->string('email')->nullable()
                ->comment('Email de contacto principal');

            // ---- Información adicional ----
            $table->string('contacto_nombre')->nullable()
                ->comment('Nombre del contacto principal');

            $table->string('contacto_cargo')->nullable()
                ->comment('Cargo del contacto principal');

            $table->text('notas')->nullable()
                ->comment('Notas internas sobre el tercero');

            // ---- Estado ----
            $table->enum('estado', ['activo', 'inactivo', 'suspendido'])->default('activo')
                ->comment('activo=operativo, inactivo=no usar, suspendido=problemas de pago');

            $table->timestamps();

            // ---- Índices ----
            $table->unique(['tenant_id', 'nit'], 'uniq_tercero_tenant_nit');
            $table->index(['tenant_id', 'nombre_razon_social'], 'idx_tercero_nombre');
            $table->index(['tenant_id', 'estado'], 'idx_tercero_estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terceros');
    }
};


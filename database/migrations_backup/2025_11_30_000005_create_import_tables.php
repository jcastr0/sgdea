<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla: Configuración de importación por tenant
        Schema::create('import_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->json('excel_column_mapping')->nullable(); // Mapeo de columnas
            $table->string('pdf_naming_pattern')->nullable(); // Patrón de nombres PDF
            $table->timestamps();

            $table->unique('tenant_id');
            $table->index('tenant_id');
        });

        // Tabla: Log de importaciones
        Schema::create('import_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('import_type'); // excel, pdf
            $table->string('file_name');
            $table->string('status')->default('processing'); // processing, completed, failed
            $table->integer('total_records')->default(0);
            $table->integer('successful')->default(0);
            $table->integer('failed')->default(0);
            $table->json('error_details')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('status');
            $table->index('import_type');
        });

        // Tabla: Detalle de cada registro importado
        Schema::create('import_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_log_id')->constrained('import_logs')->onDelete('cascade');
            $table->foreignId('factura_id')->nullable()->constrained('facturas')->onDelete('set null');
            $table->foreignId('tercero_id')->nullable()->constrained('terceros')->onDelete('set null');
            $table->string('cufe')->nullable();
            $table->string('numero_factura');
            $table->string('nit');
            $table->string('status'); // new, duplicate, error
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('import_log_id');
            $table->index('factura_id');
            $table->index('tercero_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_records');
        Schema::dropIfExists('import_logs');
        Schema::dropIfExists('import_configurations');
    }
};


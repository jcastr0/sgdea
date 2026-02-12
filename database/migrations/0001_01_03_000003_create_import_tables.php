<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Tablas de Importación
 *
 * Sistema de importación masiva de datos desde Excel y PDF.
 * Incluye configuración, logs y registros detallados.
 */
return new class extends Migration
{
    public function up(): void
    {
        // =========================================
        // TABLA: import_configurations
        // Configuración de importación por tenant
        // =========================================
        Schema::create('import_configurations', function (Blueprint $table) {
            $table->id()
                ->comment('ID único de la configuración');

            $table->foreignId('tenant_id')
                ->comment('FK al tenant dueño de la configuración')
                ->constrained('tenants')
                ->onDelete('cascade');

            // ---- Mapeo de columnas Excel ----
            $table->json('excel_column_mapping')->nullable()
                ->comment('Mapeo de columnas Excel a campos BD (JSON)');

            // ---- Configuración PDF ----
            $table->string('pdf_naming_pattern')->nullable()
                ->comment('Patrón para identificar PDFs (regex o formato)');

            // ---- Opciones de importación ----
            $table->enum('default_mode', ['create', 'update', 'skip'])->default('create')
                ->comment('Modo por defecto: create=solo nuevos, update=actualizar, skip=omitir duplicados');

            $table->boolean('auto_create_terceros')->default(true)
                ->comment('Crear terceros automáticamente si no existen');

            $table->boolean('validate_duplicates')->default(true)
                ->comment('Validar duplicados por CUFE antes de importar');

            $table->string('date_format', 20)->default('d/m/Y')
                ->comment('Formato de fecha esperado en archivos');

            $table->char('decimal_separator', 1)->default(',')
                ->comment('Separador decimal: , o .');

            $table->timestamps();

            // ---- Constraints ----
            $table->unique('tenant_id', 'uniq_import_config_tenant');
        });

        // =========================================
        // TABLA: import_logs
        // Log de cada proceso de importación
        // =========================================
        Schema::create('import_logs', function (Blueprint $table) {
            $table->id()
                ->comment('ID único del log');

            $table->foreignId('tenant_id')
                ->comment('FK al tenant')
                ->constrained('tenants')
                ->onDelete('cascade');

            $table->foreignId('user_id')->nullable()
                ->comment('FK al usuario que ejecutó la importación')
                ->constrained('users')
                ->onDelete('set null');

            // ---- Información del archivo ----
            $table->enum('import_type', ['excel', 'pdf'])
                ->comment('Tipo de importación: excel o pdf');

            $table->string('file_name')
                ->comment('Nombre del archivo importado');

            $table->string('file_path')->nullable()
                ->comment('Ruta donde se guardó el archivo');

            $table->bigInteger('file_size')->nullable()
                ->comment('Tamaño del archivo en bytes');

            // ---- Estado del proceso ----
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])
                ->default('pending')
                ->comment('Estado: pending, processing, completed, failed, cancelled');

            // ---- Estadísticas ----
            $table->integer('total_records')->default(0)
                ->comment('Total de registros encontrados');

            $table->integer('successful')->default(0)
                ->comment('Registros importados exitosamente');

            $table->integer('duplicates')->default(0)
                ->comment('Registros duplicados omitidos');

            $table->integer('failed')->default(0)
                ->comment('Registros que fallaron');

            // ---- Detalles de errores ----
            $table->json('error_details')->nullable()
                ->comment('Detalle de errores en JSON');

            // ---- Tiempos ----
            $table->timestamp('started_at')->nullable()
                ->comment('Fecha/hora de inicio del proceso');

            $table->timestamp('completed_at')->nullable()
                ->comment('Fecha/hora de finalización');

            $table->timestamps();

            // ---- Índices ----
            $table->index(['tenant_id', 'status'], 'idx_import_logs_status');
            $table->index(['tenant_id', 'import_type'], 'idx_import_logs_type');
            $table->index(['tenant_id', 'created_at'], 'idx_import_logs_date');
        });

        // =========================================
        // TABLA: import_records
        // Detalle de cada registro importado
        // =========================================
        Schema::create('import_records', function (Blueprint $table) {
            $table->id()
                ->comment('ID único del registro');

            $table->foreignId('import_log_id')
                ->comment('FK al log de importación')
                ->constrained('import_logs')
                ->onDelete('cascade');

            // ---- Referencias a entidades creadas ----
            $table->foreignId('factura_id')->nullable()
                ->comment('FK a la factura creada (si aplica)')
                ->constrained('facturas')
                ->onDelete('set null');

            $table->foreignId('tercero_id')->nullable()
                ->comment('FK al tercero creado/encontrado')
                ->constrained('terceros')
                ->onDelete('set null');

            // ---- Datos del registro original ----
            $table->integer('row_number')->nullable()
                ->comment('Número de fila en el archivo Excel');

            $table->string('cufe', 255)->nullable()
                ->comment('CUFE del registro');

            $table->string('numero_factura', 50)
                ->comment('Número de factura del registro');

            $table->string('nit', 20)
                ->comment('NIT del tercero');

            // ---- Estado del registro ----
            $table->enum('status', ['new', 'updated', 'duplicate', 'error', 'skipped'])
                ->comment('new=creado, updated=actualizado, duplicate=ya existía, error=falló, skipped=omitido');

            $table->text('error_message')->nullable()
                ->comment('Mensaje de error si falló');

            // ---- Datos originales para auditoría ----
            $table->json('original_data')->nullable()
                ->comment('Datos originales del registro (JSON)');

            $table->timestamps();

            // ---- Índices ----
            $table->index('import_log_id', 'idx_import_records_log');
            $table->index('status', 'idx_import_records_status');
            $table->index('factura_id', 'idx_import_records_factura');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_records');
        Schema::dropIfExists('import_logs');
        Schema::dropIfExists('import_configurations');
    }
};


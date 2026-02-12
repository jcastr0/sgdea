<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar columnas PDF a tabla facturas (si no existen)
        if (Schema::hasTable('facturas') && !Schema::hasColumn('facturas', 'pdf_path')) {
            Schema::table('facturas', function (Blueprint $table) {
                $table->string('pdf_path')->nullable()->after('trb');
                $table->string('hash_pdf')->nullable()->after('pdf_path');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('facturas')) {
            Schema::table('facturas', function (Blueprint $table) {
                $table->dropColumn(['pdf_path', 'hash_pdf']);
            });
        }
    }
};


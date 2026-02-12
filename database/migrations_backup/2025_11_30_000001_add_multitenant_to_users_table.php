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
        // Actualizar tabla users con campos multi-tenant
        Schema::table('users', function (Blueprint $table) {
            // Si no existen, agregarlas
            if (!Schema::hasColumn('users', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'pending_approval', 'blocked', 'inactive'])->default('pending_approval');
            }
            if (!Schema::hasColumn('users', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('users', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Dropear solo si existen
            if (Schema::hasColumn('users', 'tenant_id')) {
                $table->dropForeignIdFor('tenants');
            }
            if (Schema::hasColumn('users', 'approved_by')) {
                $table->dropForeignIdFor('users');
            }
            $table->dropColumn([
                'tenant_id',
                'status',
                'approved_by',
                'approved_at',
                'last_login_at',
                'last_login_ip',
            ]);
        });
    }
};


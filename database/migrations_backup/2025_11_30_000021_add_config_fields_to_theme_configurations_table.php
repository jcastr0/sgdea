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
        Schema::table('theme_configurations', function (Blueprint $table) {
            if (!Schema::hasColumn('theme_configurations', 'color_accent')) {
                $table->string('color_accent', 7)->nullable()->default('#10b981')->after('color_secondary');
            }
            if (!Schema::hasColumn('theme_configurations', 'dark_mode_enabled')) {
                $table->boolean('dark_mode_enabled')->default(true)->after('show_company_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('theme_configurations', function (Blueprint $table) {
            $table->dropColumn(['color_accent', 'dark_mode_enabled']);
        });
    }
};


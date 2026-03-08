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
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('success_color')->default('#10B981')->after('text_color');
            $table->string('warning_color')->default('#F59E0B')->after('success_color');
            $table->string('error_color')->default('#EF4444')->after('warning_color');
            $table->string('info_color')->default('#3B82F6')->after('error_color');
            $table->string('surface_color')->default('#FFFFFF')->after('info_color');
            $table->string('border_color')->default('#E5E7EB')->after('surface_color');
            $table->string('muted_color')->default('#6B7280')->after('border_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'success_color',
                'warning_color',
                'error_color',
                'info_color',
                'surface_color',
                'border_color',
                'muted_color',
            ]);
        });
    }
};

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
        Schema::table('products', function (Blueprint $table) {
            $table->json('content')->nullable()->after('image');
            $table->json('draft_content')->nullable()->after('content');
            $table->boolean('has_unpublished_changes')->default(false)->after('draft_content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['content', 'draft_content', 'has_unpublished_changes']);
        });
    }
};

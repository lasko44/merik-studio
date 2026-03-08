<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->json('draft_content')->nullable()->after('sidebar_content');
            $table->json('draft_sidebar_content')->nullable()->after('draft_content');
            $table->boolean('has_unpublished_changes')->default(false)->after('draft_sidebar_content');
        });

        // Initialize draft content with current content for existing pages
        DB::table('pages')->update([
            'draft_content' => DB::raw('content'),
            'draft_sidebar_content' => DB::raw('sidebar_content'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['draft_content', 'draft_sidebar_content', 'has_unpublished_changes']);
        });
    }
};

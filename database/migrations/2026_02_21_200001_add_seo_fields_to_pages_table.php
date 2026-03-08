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
        Schema::table('pages', function (Blueprint $table) {
            // Open Graph
            $table->string('og_title')->nullable()->after('meta_keywords');
            $table->text('og_description')->nullable()->after('og_title');
            $table->string('og_image')->nullable()->after('og_description');

            // Twitter Cards
            $table->string('twitter_card_type')->default('summary_large_image')->after('og_image');

            // Canonical & Indexing
            $table->string('canonical_url')->nullable()->after('twitter_card_type');
            $table->boolean('no_index')->default(false)->after('canonical_url');
            $table->boolean('no_follow')->default(false)->after('no_index');

            // Structured Data
            $table->string('schema_type')->default('WebPage')->after('no_follow');
            $table->json('faqs')->nullable()->after('schema_type');
            $table->json('speakable_selectors')->nullable()->after('faqs');

            // Sitemap
            $table->decimal('priority', 2, 1)->default(0.5)->after('speakable_selectors');
            $table->string('change_frequency')->default('weekly')->after('priority');

            // Index for sitemap generation
            $table->index(['is_published', 'no_index', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropIndex(['is_published', 'no_index', 'priority']);

            $table->dropColumn([
                'og_title',
                'og_description',
                'og_image',
                'twitter_card_type',
                'canonical_url',
                'no_index',
                'no_follow',
                'schema_type',
                'faqs',
                'speakable_selectors',
                'priority',
                'change_frequency',
            ]);
        });
    }
};

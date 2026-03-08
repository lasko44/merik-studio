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
            // Open Graph Defaults
            $table->string('og_default_image')->nullable()->after('default_meta_keywords');

            // Twitter
            $table->string('twitter_handle')->nullable()->after('og_default_image');
            $table->string('twitter_card_type')->default('summary_large_image')->after('twitter_handle');

            // Organization Schema
            $table->string('organization_name')->nullable()->after('twitter_card_type');
            $table->string('organization_logo')->nullable()->after('organization_name');
            $table->string('organization_type')->default('Organization')->after('organization_logo');
            $table->json('same_as')->nullable()->after('organization_type');

            // LLMs.txt (GEO)
            $table->text('llms_txt_content')->nullable()->after('same_as');
            $table->integer('llms_crawl_delay')->nullable()->after('llms_txt_content');
            $table->boolean('llms_allow_training')->default(true)->after('llms_crawl_delay');

            // Robots.txt
            $table->text('robots_txt_content')->nullable()->after('llms_allow_training');

            // Feature Flags
            $table->boolean('sitemap_enabled')->default(true)->after('robots_txt_content');
            $table->boolean('geo_enabled')->default(true)->after('sitemap_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'og_default_image',
                'twitter_handle',
                'twitter_card_type',
                'organization_name',
                'organization_logo',
                'organization_type',
                'same_as',
                'llms_txt_content',
                'llms_crawl_delay',
                'llms_allow_training',
                'robots_txt_content',
                'sitemap_enabled',
                'geo_enabled',
            ]);
        });
    }
};

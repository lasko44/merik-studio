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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();

            // Branding
            $table->string('site_name')->default('My Site');
            $table->string('tagline')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();

            // Theme selection
            $table->string('theme')->default('default');

            // Theme colors (customizable per theme)
            $table->string('primary_color')->default('#3B82F6');
            $table->string('secondary_color')->default('#10B981');
            $table->string('accent_color')->default('#F59E0B');
            $table->string('background_color')->default('#FFFFFF');
            $table->string('text_color')->default('#1F2937');

            // Typography
            $table->string('heading_font')->default('Inter');
            $table->string('body_font')->default('Inter');

            // Social links
            $table->json('social_links')->nullable();

            // Contact info
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('contact_address')->nullable();

            // SEO defaults
            $table->text('default_meta_description')->nullable();
            $table->string('default_meta_keywords')->nullable();

            // Custom code injection
            $table->text('head_scripts')->nullable();
            $table->text('body_scripts')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};

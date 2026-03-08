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
            $table->boolean('blog_show_in_nav')->default(false)->after('stripe_test_mode');
            $table->string('blog_nav_label')->default('Blog')->after('blog_show_in_nav');
            $table->boolean('blog_enable_search')->default(true)->after('blog_nav_label');
            $table->boolean('blog_enable_category_filter')->default(true)->after('blog_enable_search');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'blog_show_in_nav',
                'blog_nav_label',
                'blog_enable_search',
                'blog_enable_category_filter',
            ]);
        });
    }
};

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
            $table->boolean('products_show_in_nav')->default(false)->after('blog_nav_order');
            $table->string('products_nav_label')->default('Products')->after('products_show_in_nav');
            $table->integer('products_nav_order')->default(999)->after('products_nav_label');
            $table->boolean('products_enable_search')->default(true)->after('products_nav_order');
            $table->boolean('products_enable_category_filter')->default(true)->after('products_enable_search');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'products_show_in_nav',
                'products_nav_label',
                'products_nav_order',
                'products_enable_search',
                'products_enable_category_filter',
            ]);
        });
    }
};

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
            // Stripe API Keys
            $table->string('stripe_publishable_key')->nullable()->after('geo_enabled');
            $table->text('stripe_secret_key')->nullable()->after('stripe_publishable_key');
            $table->text('stripe_webhook_secret')->nullable()->after('stripe_secret_key');
            $table->boolean('stripe_test_mode')->default(true)->after('stripe_webhook_secret');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_publishable_key',
                'stripe_secret_key',
                'stripe_webhook_secret',
                'stripe_test_mode',
            ]);
        });
    }
};

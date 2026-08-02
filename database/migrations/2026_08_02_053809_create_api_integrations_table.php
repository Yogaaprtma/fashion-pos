<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_integrations', function (Blueprint $table) {
            $table->id();
            $table->string('channel_name'); // e.g. Shopee, Tokopedia, WooCommerce, TikTok Shop
            $table->string('api_key')->unique();
            $table->string('webhook_secret')->nullable();
            $table->enum('sync_direction', ['bidirectional', 'pos_to_online', 'online_to_pos'])->default('bidirectional');
            $table->boolean('auto_deduct_stock')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });

        Schema::create('api_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_id')->constrained('api_integrations')->cascadeOnDelete();
            $table->string('event_type'); // e.g. stock_update, order_created
            $table->text('payload')->nullable();
            $table->enum('status', ['success', 'failed'])->default('success');
            $table->text('response_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_sync_logs');
        Schema::dropIfExists('api_integrations');
    }
};

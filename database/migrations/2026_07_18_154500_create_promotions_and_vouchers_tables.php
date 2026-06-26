<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('code', 50)->nullable()->unique(); // For coupon/voucher input
            $table->enum('type', ['discount_percent', 'discount_fixed', 'bogo', 'bundling']);
            $table->decimal('value', 15, 2)->nullable(); // e.g., 10 (10%) or 50000 (Rp 50.000)

            // Requirements
            $table->enum('min_requirement_type', ['none', 'min_spend', 'min_qty'])->default('none');
            $table->decimal('min_requirement_value', 15, 2)->default(0);

            // Validity
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('usage_limit')->nullable(); // Total times this promo can be used
            $table->integer('used_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();

            // Targeting
            $table->enum('target_type', ['all', 'category', 'product'])->default('all');

            $table->timestamps();
            $table->softDeletes();
        });

        // Mapping for specific products or categories
        Schema::create('promotion_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained('promotions')->cascadeOnDelete();
            $table->unsignedBigInteger('target_id'); // can be category_id or product_id
            $table->timestamps();
        });

        // Add promotion info to transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('promotion_id')->nullable()->after('customer_id')->constrained('promotions')->nullOnDelete();
            $table->decimal('promotion_discount', 15, 2)->default(0)->after('point_discount');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('promotion_id');
            $table->dropColumn('promotion_discount');
        });
        Schema::dropIfExists('promotion_targets');
        Schema::dropIfExists('promotions');
    }
};

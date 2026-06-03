<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_point_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->integer('amount'); // positive for earn, negative for redeem
            $table->enum('type', ['earn', 'redeem', 'adjustment']);
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('points_earned')->default(0)->after('customer_id');
            $table->integer('points_used')->default(0)->after('points_earned');
            $table->decimal('point_discount', 15, 2)->default(0)->after('discount_percent');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['points_earned', 'points_used', 'point_discount']);
        });
        
        Schema::dropIfExists('customer_point_histories');
    }
};

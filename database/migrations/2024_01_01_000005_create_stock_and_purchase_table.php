<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->string('return_number', 30)->unique();
            $table->decimal('total_refund', 15, 2);
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('requested_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->foreign('transaction_id')->references('id')->on('transactions');
            $table->foreign('requested_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('return_id');
            $table->unsignedBigInteger('transaction_item_id');
            $table->unsignedBigInteger('product_variant_id');
            $table->integer('quantity');
            $table->decimal('refund_amount', 15, 2);
            $table->timestamps();
            $table->foreign('return_id')->references('id')->on('returns')->onDelete('cascade');
            $table->foreign('transaction_item_id')->references('id')->on('transaction_items');
            $table->foreign('product_variant_id')->references('id')->on('product_variants');
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_variant_id');
            $table->enum('type', ['in', 'out', 'adjustment', 'return', 'opname']);
            $table->integer('quantity');
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->string('reference_type', 50)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->foreign('product_variant_id')->references('id')->on('product_variants');
            $table->foreign('user_id')->references('id')->on('users');
            $table->index('product_variant_id');
            $table->index('created_at');
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number', 30)->unique();
            $table->unsignedBigInteger('supplier_id');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->enum('status', ['draft', 'sent', 'partial', 'received', 'cancelled'])->default('draft');
            $table->unsignedBigInteger('ordered_by');
            $table->text('notes')->nullable();
            $table->date('expected_date')->nullable();
            $table->timestamps();
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('ordered_by')->references('id')->on('users');
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedBigInteger('product_variant_id');
            $table->integer('quantity_ordered');
            $table->integer('quantity_received')->default(0);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
            $table->foreign('product_variant_id')->references('id')->on('product_variants');
        });

        Schema::create('goods_received_notes', function (Blueprint $table) {
            $table->id();
            $table->string('grn_number', 30)->unique();
            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedBigInteger('received_by');
            $table->text('notes')->nullable();
            $table->timestamp('received_at');
            $table->timestamps();
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders');
            $table->foreign('received_by')->references('id')->on('users');
        });

        Schema::create('grn_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grn_id');
            $table->unsignedBigInteger('purchase_order_item_id');
            $table->unsignedBigInteger('product_variant_id');
            $table->integer('quantity_received');
            $table->text('condition_notes')->nullable();
            $table->timestamps();
            $table->foreign('grn_id')->references('id')->on('goods_received_notes')->onDelete('cascade');
            $table->foreign('purchase_order_item_id')->references('id')->on('purchase_order_items');
            $table->foreign('product_variant_id')->references('id')->on('product_variants');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grn_items');
        Schema::dropIfExists('goods_received_notes');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('return_items');
        Schema::dropIfExists('returns');
    }
};

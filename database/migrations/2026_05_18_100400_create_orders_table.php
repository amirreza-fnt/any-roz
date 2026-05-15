<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('order_number', 64)->unique();
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->decimal('shipping_fee', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('shipping_cost', 14, 2)->default(0);
            $table->decimal('insurance_cost', 14, 2)->default(0);
            $table->decimal('final_amount', 14, 2)->default(0);
            $table->decimal('total_weight', 10, 2)->default(0);
            $table->string('payment_status', 32)->default('pending');
            $table->string('payment_method', 64)->nullable();
            $table->string('payment_transaction_id', 128)->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->string('shipping_status', 32)->default('pending_review');
            $table->string('shipping_method', 64)->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('shipping_city', 120)->nullable();
            $table->string('shipping_state', 120)->nullable();
            $table->string('shipping_postal_code', 20)->nullable();
            $table->string('shipping_recipient_name', 200)->nullable();
            $table->string('shipping_phone', 20)->nullable();
            $table->string('shipping_tracking_code', 100)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('sent_to_supply')->default(false);
            $table->timestamp('sent_to_supply_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_name', 255);
            $table->string('product_code', 64)->nullable();
            $table->string('product_image', 500)->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedBigInteger('unit_price')->default(0);
            $table->unsignedTinyInteger('discount_percent')->default(0);
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('final_price')->default(0);
            $table->json('product_options')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_products');
        Schema::dropIfExists('orders');
    }
};

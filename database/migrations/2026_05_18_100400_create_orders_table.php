<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders')) {
            return;
        }

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('order_number', 100)->nullable()->index();
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->decimal('shipping_fee', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('shipping_cost', 14, 2)->default(0);
            $table->decimal('insurance_cost', 14, 2)->default(0);
            $table->decimal('final_amount', 14, 2)->default(0);
            $table->decimal('total_weight', 10, 3)->default(0);
            $table->string('payment_status', 32)->default('pending')->index();
            $table->string('payment_method', 64)->nullable();
            $table->string('payment_transaction_id')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->string('shipping_status', 32)->default('pending_review')->index();
            $table->string('shipping_method', 255)->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('shipping_city', 120)->nullable();
            $table->string('shipping_state', 120)->nullable();
            $table->string('shipping_postal_code', 20)->nullable();
            $table->string('shipping_recipient_name', 120)->nullable();
            $table->string('shipping_phone', 32)->nullable();
            $table->string('shipping_tracking_code')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('sent_to_supply')->default(false);
            $table->timestamp('sent_to_supply_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
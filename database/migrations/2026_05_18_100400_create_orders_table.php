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
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('order_number', 50)->unique();
            $table->decimal('total_amount', 12, 2);
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->decimal('insurance_cost', 12, 2)->default(0);
            $table->decimal('final_amount', 12, 2);
            $table->decimal('total_weight', 8, 2)->default(0);
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_transaction_id', 255)->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->enum('shipping_status', [
                'pending_review',
                'packaging',
                'shipping',
                'completed',
                'cancelled',
            ])->default('pending_review');
            $table->string('shipping_method', 120)->nullable();
            $table->text('shipping_address');
            $table->string('shipping_city', 100);
            $table->string('shipping_state', 100);
            $table->string('shipping_postal_code', 20);
            $table->string('shipping_recipient_name', 255);
            $table->string('shipping_phone', 20);
            $table->string('shipping_tracking_code', 100)->nullable();
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

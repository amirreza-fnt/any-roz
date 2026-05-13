<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->string('product_name');
            $table->string('product_code', 100)->nullable();
            $table->string('product_image', 255)->nullable();
            $table->unsignedInteger('quantity');
            $table->string('unit_price', 255);
            $table->unsignedBigInteger('discount_percent');
            $table->unsignedBigInteger('discount_amount');
            $table->unsignedBigInteger('final_price');
            $table->json('product_options')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};

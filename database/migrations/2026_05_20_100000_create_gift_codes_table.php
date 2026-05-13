<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gift_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->enum('value_type', ['fixed', 'percent'])->default('fixed');
            $table->unsignedBigInteger('amount')->nullable()->comment('Toman when fixed');
            $table->unsignedTinyInteger('percent')->nullable()->comment('1-100 when percent');
            $table->unsignedBigInteger('max_amount')->nullable()->comment('Cap in toman when percent');
            $table->unsignedBigInteger('min_order_amount')->default(0);
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->unsignedInteger('per_user_limit')->default(1);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->enum('applies_to', ['all', 'categories', 'products'])->default('all');
            $table->json('category_ids')->nullable();
            $table->json('product_ids')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_codes');
    }
};

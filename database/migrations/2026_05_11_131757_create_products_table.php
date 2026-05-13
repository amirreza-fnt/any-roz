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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('weight_id')->constrained('type_of_weights')->cascadeOnDelete();
            $table->integer('price');
            $table->integer('price_buy');
            $table->integer('price_discounted');
            $table->integer('stock');
            $table->enum('status',['active','inactive'])->default('active');
            $table->enum('suggested',['active','inactive'])->default('inactive');
            $table->text('mini_description');
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

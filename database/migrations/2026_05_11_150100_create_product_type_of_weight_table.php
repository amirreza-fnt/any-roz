<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_type_of_weight', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('type_of_weight_id')->constrained('type_of_weights')->cascadeOnDelete();
            $table->unsignedInteger('stock')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'type_of_weight_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_type_of_weight');
    }
};

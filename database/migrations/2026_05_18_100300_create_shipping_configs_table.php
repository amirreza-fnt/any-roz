<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('base_shipping_cost')->default(35000);
            $table->integer('base_insurance_cost')->default(5000);
            $table->integer('base_packaging_cost')->default(0);
            $table->integer('package_weight_limit')->default(12);
            $table->integer('extra_weight_cost')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(1);
            $table->text('description')->nullable();
            $table->json('additional_settings')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_configs');
    }
};

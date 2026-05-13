<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->unsignedInteger('province_id');
            $table->string('province_name')->nullable();
            $table->unsignedInteger('city_id');
            $table->string('city_name')->nullable();
            $table->text('full_address');
            $table->string('postal_code', 10);
            $table->string('mobile', 11);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('province_id')->references('id')->on('provinces')->restrictOnDelete();
            $table->foreign('city_id')->references('id')->on('cities')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};

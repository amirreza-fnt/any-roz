<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 120);
            $table->string('last_name', 120);
            $table->string('phone', 20)->unique();
            $table->string('email', 255)->nullable();
            $table->string('national_id', 20)->nullable();
            $table->string('father_name', 120)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('position', 255)->nullable()->comment('سمت');
            $table->string('password');
            $table->json('permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_super')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};

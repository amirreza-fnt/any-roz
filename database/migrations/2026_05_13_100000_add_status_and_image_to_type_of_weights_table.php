<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('type_of_weights', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive'])->default('active')->after('title');
            $table->string('image')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('type_of_weights', function (Blueprint $table) {
            $table->dropColumn(['status', 'image']);
        });
    }
};

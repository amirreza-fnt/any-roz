<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Removes columns that are not part of the original type_of_weights schema
 * (id, title, weight nullable, timestamps) if they were added in older branches.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('type_of_weights', 'image')) {
            Schema::table('type_of_weights', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }

        if (Schema::hasColumn('type_of_weights', 'status')) {
            Schema::table('type_of_weights', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }

    public function down(): void
    {
        // عمداً خالی: ستون‌های حذف‌شده بخشی از اسکیمای اصلی نبودند.
    }
};

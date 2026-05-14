<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_histories', function (Blueprint $table) {
            if (! Schema::hasColumn('order_histories', 'admin_id')) {
                $table->foreignId('admin_id')->nullable()->after('user_id')->constrained('admins')->nullOnDelete();
            }
        });

        if (Schema::hasTable('marketing_sales')) {
            try {
                Schema::table('marketing_sales', function (Blueprint $table) {
                    $table->dropForeign(['reviewed_by']);
                });
            } catch (\Throwable $e) {
                // ignore if constraint missing
            }
        }
    }

    public function down(): void
    {
        Schema::table('order_histories', function (Blueprint $table) {
            if (Schema::hasColumn('order_histories', 'admin_id')) {
                $table->dropForeign(['admin_id']);
                $table->dropColumn('admin_id');
            }
        });
    }
};

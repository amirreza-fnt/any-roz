<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketing_sales', function (Blueprint $table) {
            if (! Schema::hasColumn('marketing_sales', 'sale_type')) {
                $table->string('sale_type', 20)->default('marketer')->after('marketer_id')->index();
            }

            try {
                $table->dropForeign(['buyer_id']);
            } catch (\Throwable $e) {
                // ignore if the constraint does not exist
            }

            Schema::table('marketing_sales', function (Blueprint $table) {
                $table->unsignedBigInteger('buyer_id')->nullable()->change();
            });

            try {
                $table->foreign('buyer_id')->references('id')->on('marketing_buyers')->nullOnDelete();
            } catch (\Throwable $e) {
                // ignore if re-adding fails
            }
        });
    }

    public function down(): void
    {
        Schema::table('marketing_sales', function (Blueprint $table) {
            try {
                $table->dropForeign(['buyer_id']);
            } catch (\Throwable $e) {
                // ignore
            }

            Schema::table('marketing_sales', function (Blueprint $table) {
                $table->unsignedBigInteger('buyer_id')->nullable(false)->change();
            });

            try {
                $table->foreign('buyer_id')->references('id')->on('marketing_buyers')->cascadeOnDelete();
            } catch (\Throwable $e) {
                // ignore
            }

            if (Schema::hasColumn('marketing_sales', 'sale_type')) {
                $table->dropColumn('sale_type');
            }
        });
    }
};
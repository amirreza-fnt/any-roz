<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (! Schema::hasColumn('admins', 'is_colleague')) {
                $table->boolean('is_colleague')->default(false)->after('is_super')->comment('همکار خرید کلی');
            }
            if (! Schema::hasColumn('admins', 'store_name')) {
                $table->string('store_name', 255)->nullable()->after('position')->comment('نام فروشگاه / محل همکار');
            }
            if (! Schema::hasColumn('admins', 'address')) {
                $table->text('address')->nullable()->after('store_name');
            }
            if (! Schema::hasColumn('admins', 'postal_code')) {
                $table->string('postal_code', 20)->nullable()->after('address');
            }
            if (! Schema::hasColumn('admins', 'province_id')) {
                $table->unsignedInteger('province_id')->nullable()->after('postal_code');
                $table->foreign('province_id')->references('id')->on('provinces')->nullOnDelete();
            }
            if (! Schema::hasColumn('admins', 'city_id')) {
                $table->unsignedInteger('city_id')->nullable()->after('province_id');
                $table->foreign('city_id')->references('id')->on('cities')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropForeign(['province_id']);
            $table->dropForeign(['city_id']);
            $table->dropColumn([
                'is_colleague',
                'store_name',
                'address',
                'postal_code',
                'province_id',
                'city_id',
            ]);
        });
    }
};

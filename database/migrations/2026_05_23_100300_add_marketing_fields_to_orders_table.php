<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('source', 32)->default('site')->after('user_id');
            $table->unsignedBigInteger('marketer_id')->nullable()->after('source')->index();
            $table->unsignedBigInteger('marketing_sale_id')->nullable()->after('marketer_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['source', 'marketer_id', 'marketing_sale_id']);
        });
    }
};

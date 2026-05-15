<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flowchart_nodes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->string('title', 255);
            $table->unsignedInteger('sort_order')->default(0);

            $table->decimal('profit_percentage', 5, 2)->default(0)
                  ->comment('درصد سود این نود');

            $table->string('profit_source', 32)->default('none')
                  ->comment('none|all_sales|own_sales|marketing_sales|site_sales');

            $table->unsignedBigInteger('admin_id')->nullable()
                  ->comment('اتصال به ادمین (اختیاری)');

            $table->string('color', 7)->nullable()
                  ->comment('رنگ نود مثلاً #3b82f6');

            $table->timestamps();

            $table->foreign('parent_id')
                  ->references('id')->on('flowchart_nodes')
                  ->nullOnDelete();

            $table->foreign('admin_id')
                  ->references('id')->on('admins')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flowchart_nodes');
    }
};

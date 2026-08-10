<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('marketer_id')->index();
            $table->foreignId('buyer_id')->constrained('marketing_buyers')->cascadeOnDelete();
            $table->enum('status', ['pending_accounting', 'approved', 'rejected'])->default('pending_accounting')->index();
            $table->string('buyer_first_name', 120);
            $table->string('buyer_last_name', 120);
            $table->string('buyer_phone', 32);
            $table->string('buyer_store_name', 255)->nullable();
            $table->string('payment_method', 255)->nullable();
            $table->text('notes')->nullable();
            $table->date('sale_date');
            $table->text('accountant_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_sales');
    }
};

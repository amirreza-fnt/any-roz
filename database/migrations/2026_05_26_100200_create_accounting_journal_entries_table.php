<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_journal_entries', function (Blueprint $table) {
            $table->id();
            $table->date('document_date');
            $table->string('document_no', 64)->nullable();
            $table->string('title', 255);
            $table->string('kind', 32);
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_journal_entries');
    }
};

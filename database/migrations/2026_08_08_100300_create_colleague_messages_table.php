<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colleague_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('colleague_id')->constrained('admins')->cascadeOnDelete();
            $table->enum('sender_type', ['colleague', 'support']);
            $table->foreignId('sender_id')->constrained('admins')->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['colleague_id', 'id']);
            $table->index(['sender_type', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colleague_messages');
    }
};
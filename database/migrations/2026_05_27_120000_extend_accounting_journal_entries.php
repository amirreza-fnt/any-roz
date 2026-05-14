<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('accounting_journal_entries')) {
            return;
        }

        Schema::table('accounting_journal_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('accounting_journal_entries', 'subsidiary_code')) {
                $table->string('subsidiary_code', 64)->nullable()->after('kind')->comment('کد معین / تفصیل');
            }
            if (! Schema::hasColumn('accounting_journal_entries', 'counterparty')) {
                $table->string('counterparty', 255)->nullable()->after('title')->comment('طرف حساب / شخص');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('accounting_journal_entries')) {
            return;
        }

        Schema::table('accounting_journal_entries', function (Blueprint $table) {
            if (Schema::hasColumn('accounting_journal_entries', 'subsidiary_code')) {
                $table->dropColumn('subsidiary_code');
            }
            if (Schema::hasColumn('accounting_journal_entries', 'counterparty')) {
                $table->dropColumn('counterparty');
            }
        });
    }
};

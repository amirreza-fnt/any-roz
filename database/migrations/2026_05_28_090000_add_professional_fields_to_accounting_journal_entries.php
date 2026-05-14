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
            if (! Schema::hasColumn('accounting_journal_entries', 'category')) {
                $table->string('category', 64)->nullable()->after('counterparty')->comment('دستهٔ هزینه/درآمد');
            }
            if (! Schema::hasColumn('accounting_journal_entries', 'journal_payment_method')) {
                $table->string('journal_payment_method', 64)->nullable()->after('category')->comment('روش تسویه');
            }
            if (! Schema::hasColumn('accounting_journal_entries', 'external_reference')) {
                $table->string('external_reference', 128)->nullable()->after('journal_payment_method')->comment('شماره پیگیری بانکی / مرجع خارجی');
            }
            if (! Schema::hasColumn('accounting_journal_entries', 'cost_center')) {
                $table->string('cost_center', 64)->nullable()->after('external_reference')->comment('مرکز هزینه');
            }
            if (! Schema::hasColumn('accounting_journal_entries', 'vat_rate')) {
                $table->decimal('vat_rate', 6, 2)->nullable()->after('cost_center')->comment('درصد مالیات');
            }
            if (! Schema::hasColumn('accounting_journal_entries', 'vat_amount')) {
                $table->decimal('vat_amount', 14, 2)->nullable()->after('vat_rate')->comment('مبلغ مالیات');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('accounting_journal_entries')) {
            return;
        }

        Schema::table('accounting_journal_entries', function (Blueprint $table) {
            foreach (['category', 'journal_payment_method', 'external_reference', 'cost_center', 'vat_rate', 'vat_amount'] as $col) {
                if (Schema::hasColumn('accounting_journal_entries', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

<?php

namespace App\Http\Controllers\Admin\Accounting;

use App\Http\Controllers\Controller;
use App\Models\AccountingJournalEntry;
use Illuminate\Support\Facades\Schema;

/**
 * داشبورد «حسابداری مجزا» — نقطهٔ ورود سامانهٔ ثبت مالی مستقل از فاکتورهای سایت.
 */
class AccountingStandaloneController extends Controller
{
    public function index()
    {
        if (! Schema::hasTable('accounting_journal_entries')) {
            return view('backend.accounting.standalone.index', [
                'sumIncome' => 0.0,
                'sumExpense' => 0.0,
                'sumAdjustment' => 0.0,
                'recent' => collect(),
                'tableAvailable' => false,
            ]);
        }

        $sumIncome = (float) AccountingJournalEntry::query()->where('kind', AccountingJournalEntry::KIND_INCOME)->sum('amount');
        $sumExpense = (float) AccountingJournalEntry::query()->where('kind', AccountingJournalEntry::KIND_EXPENSE)->sum('amount');
        $sumAdjustment = (float) AccountingJournalEntry::query()->where('kind', AccountingJournalEntry::KIND_ADJUSTMENT)->sum('amount');

        $recent = AccountingJournalEntry::query()
            ->with('admin')
            ->orderByDesc('document_date')
            ->orderByDesc('id')
            ->limit(18)
            ->get();

        return view('backend.accounting.standalone.index', [
            'sumIncome' => $sumIncome,
            'sumExpense' => $sumExpense,
            'sumAdjustment' => $sumAdjustment,
            'recent' => $recent,
            'tableAvailable' => true,
        ]);
    }
}

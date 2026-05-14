<?php

namespace App\Http\Controllers\Admin\Accounting;

use App\Http\Controllers\Controller;
use App\Models\AccountingJournalEntry;
use App\Support\JalaliCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AccountingJournalEntryController extends Controller
{
    public function index()
    {
        $entries = AccountingJournalEntry::query()
            ->with('admin')
            ->orderByDesc('document_date')
            ->orderByDesc('id')
            ->paginate(25);

        $sumIncome = (float) AccountingJournalEntry::query()->where('kind', AccountingJournalEntry::KIND_INCOME)->sum('amount');
        $sumExpense = (float) AccountingJournalEntry::query()->where('kind', AccountingJournalEntry::KIND_EXPENSE)->sum('amount');
        $sumAdjustment = (float) AccountingJournalEntry::query()->where('kind', AccountingJournalEntry::KIND_ADJUSTMENT)->sum('amount');

        return view('backend.accounting.journal.index', compact('entries', 'sumIncome', 'sumExpense', 'sumAdjustment'));
    }

    public function create()
    {
        return view('backend.accounting.journal.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['admin_id'] = auth('admin')->id();

        AccountingJournalEntry::create($data);

        message('success', 'سند حسابداری مجزا ثبت شد.');

        return redirect()->route('admin.accounting.journal.index');
    }

    public function edit(AccountingJournalEntry $journal)
    {
        return view('backend.accounting.journal.edit', ['entry' => $journal]);
    }

    public function update(Request $request, AccountingJournalEntry $journal)
    {
        $journal->update($this->validated($request));

        message('success', 'سند به‌روزرسانی شد.');

        return redirect()->route('admin.accounting.journal.index');
    }

    public function destroy(AccountingJournalEntry $journal)
    {
        $journal->delete();
        message('success', 'سند حذف شد.');

        return redirect()->route('admin.accounting.journal.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $base = $request->validate([
            'document_date_shamsi' => ['required', 'string', 'max:32'],
            'document_no' => ['nullable', 'string', 'max:64'],
            'title' => ['required', 'string', 'max:255'],
            'kind' => ['required', 'string', Rule::in(AccountingJournalEntry::kinds())],
            'subsidiary_code' => ['nullable', 'string', 'max:64'],
            'counterparty' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:64'],
            'journal_payment_method' => ['nullable', 'string', 'max:64'],
            'external_reference' => ['nullable', 'string', 'max:128'],
            'cost_center' => ['nullable', 'string', 'max:64'],
            'vat_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'vat_amount' => ['nullable', 'numeric', 'min:0'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:8000'],
        ]);

        try {
            $documentDate = JalaliCalendar::parseShamsiDateStartOfDay(trim($base['document_date_shamsi']));
        } catch (\InvalidArgumentException $e) {
            throw ValidationException::withMessages([
                'document_date_shamsi' => $e->getMessage(),
            ]);
        }

        $out = [
            'document_date' => $documentDate->toDateString(),
            'document_no' => $base['document_no'] ?: null,
            'title' => $base['title'],
            'kind' => $base['kind'],
            'amount' => $base['amount'],
            'description' => $base['description'] ?? null,
        ];

        if (Schema::hasColumn('accounting_journal_entries', 'subsidiary_code')) {
            $out['subsidiary_code'] = $base['subsidiary_code'] ?: null;
        }
        if (Schema::hasColumn('accounting_journal_entries', 'counterparty')) {
            $out['counterparty'] = $base['counterparty'] ?: null;
        }
        if (Schema::hasColumn('accounting_journal_entries', 'category')) {
            $out['category'] = $base['category'] ?: null;
        }
        if (Schema::hasColumn('accounting_journal_entries', 'journal_payment_method')) {
            $out['journal_payment_method'] = $base['journal_payment_method'] ?: null;
        }
        if (Schema::hasColumn('accounting_journal_entries', 'external_reference')) {
            $out['external_reference'] = $base['external_reference'] ?: null;
        }
        if (Schema::hasColumn('accounting_journal_entries', 'cost_center')) {
            $out['cost_center'] = $base['cost_center'] ?: null;
        }
        if (Schema::hasColumn('accounting_journal_entries', 'vat_rate')) {
            $out['vat_rate'] = $base['vat_rate'] !== null && $base['vat_rate'] !== '' ? $base['vat_rate'] : null;
        }
        if (Schema::hasColumn('accounting_journal_entries', 'vat_amount')) {
            $out['vat_amount'] = $base['vat_amount'] !== null && $base['vat_amount'] !== '' ? $base['vat_amount'] : null;
        }

        return $out;
    }
}

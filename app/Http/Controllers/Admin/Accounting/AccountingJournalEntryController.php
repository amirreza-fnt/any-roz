<?php

namespace App\Http\Controllers\Admin\Accounting;

use App\Http\Controllers\Controller;
use App\Models\AccountingJournalEntry;
use App\Support\JalaliCalendar;
use Illuminate\Http\Request;
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

        message('success', 'سند در دفتر حسابداری مجزا ثبت شد.');

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

        return [
            'document_date' => $documentDate->toDateString(),
            'document_no' => $base['document_no'] ?: null,
            'title' => $base['title'],
            'kind' => $base['kind'],
            'amount' => $base['amount'],
            'description' => $base['description'] ?? null,
        ];
    }
}

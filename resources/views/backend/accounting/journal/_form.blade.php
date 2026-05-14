@php
    use App\Support\JalaliCalendar;
    $entry = $entry ?? null;
    $isEdit = (bool) $entry;
    $docShamsi = old('document_date_shamsi', $entry ? JalaliCalendar::formatShamsiDate($entry->document_date) : JalaliCalendar::formatShamsiDate(now()));
@endphp

<div class="form-row">
    <div class="form-group col-md-4">
        <label>تاریخ سند (شمسی) <span class="text-danger">*</span></label>
        <input type="text" name="document_date_shamsi" id="journal_doc_date" autocomplete="off" class="form-control text-left" dir="ltr" value="{{ $docShamsi }}" required>
        @error('document_date_shamsi')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-4">
        <label>نوع سند <span class="text-danger">*</span></label>
        <select name="kind" class="form-control" required>
            @foreach(\App\Models\AccountingJournalEntry::kinds() as $k)
                <option value="{{ $k }}" @selected(old('kind', $entry?->kind) === $k)>{{ \App\Models\AccountingJournalEntry::kindLabel($k) }}</option>
            @endforeach
        </select>
        @error('kind')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-4">
        <label>مبلغ (تومان) <span class="text-danger">*</span></label>
        <input type="number" name="amount" class="form-control text-left" dir="ltr" step="0.01" min="0.01" value="{{ old('amount', $entry?->amount) }}" required>
        @error('amount')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
</div>
<div class="form-group">
    <label>عنوان سند <span class="text-danger">*</span></label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $entry?->title) }}" maxlength="255" required>
    @error('title')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</div>
<div class="form-group">
    <label>شماره سند (اختیاری)</label>
    <input type="text" name="document_no" class="form-control text-left" dir="ltr" maxlength="64" value="{{ old('document_no', $entry?->document_no) }}">
    @error('document_no')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</div>
<div class="form-group mb-0">
    <label>توضیحات</label>
    <textarea name="description" class="form-control" rows="3" maxlength="8000">{{ old('description', $entry?->description) }}</textarea>
    @error('description')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
</div>

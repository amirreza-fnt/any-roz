@php
    $entry = $entry ?? null;
    $isEdit = (bool) $entry;
    $docShamsi = old('document_date_shamsi', $entry ? \App\Support\JalaliCalendar::formatShamsiDate($entry->document_date) : \App\Support\JalaliCalendar::formatShamsiDate(now()));
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
<div class="form-row">
    <div class="form-group col-md-6">
        <label>کد معین / تفصیل (اختیاری)</label>
        <input type="text" name="subsidiary_code" class="form-control text-left" dir="ltr" maxlength="64" value="{{ old('subsidiary_code', $entry?->subsidiary_code) }}" placeholder="مثال: ۵۰۱۰۲">
        @error('subsidiary_code')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-6">
        <label>طرف حساب / شخص (اختیاری)</label>
        <input type="text" name="counterparty" class="form-control" maxlength="255" value="{{ old('counterparty', $entry?->counterparty) }}">
        @error('counterparty')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
</div>
<div class="form-row">
    <div class="form-group col-md-4">
        <label>دستهٔ مالی (اختیاری)</label>
        <input type="text" name="category" class="form-control" maxlength="64" value="{{ old('category', $entry?->category) }}" placeholder="مثال: اداری، حمل، فروش">
        @error('category')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-4">
        <label>روش تسویه (اختیاری)</label>
        <input type="text" name="journal_payment_method" class="form-control" maxlength="64" value="{{ old('journal_payment_method', $entry?->journal_payment_method) }}" placeholder="نقد، کارت، چک، …">
        @error('journal_payment_method')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-4">
        <label>مرکز هزینه (اختیاری)</label>
        <input type="text" name="cost_center" class="form-control" maxlength="64" value="{{ old('cost_center', $entry?->cost_center) }}">
        @error('cost_center')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
</div>
<div class="form-row">
    <div class="form-group col-md-6">
        <label>شماره پیگیری / مرجع خارجی (اختیاری)</label>
        <input type="text" name="external_reference" class="form-control text-left" dir="ltr" maxlength="128" value="{{ old('external_reference', $entry?->external_reference) }}">
        @error('external_reference')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-3">
        <label>درصد مالیات (اختیاری)</label>
        <input type="number" name="vat_rate" class="form-control text-left" dir="ltr" step="0.01" min="0" max="100" value="{{ old('vat_rate', $entry?->vat_rate) }}">
        @error('vat_rate')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-3">
        <label>مبلغ مالیات (اختیاری)</label>
        <input type="number" name="vat_amount" class="form-control text-left" dir="ltr" step="0.01" min="0" value="{{ old('vat_amount', $entry?->vat_amount) }}">
        @error('vat_amount')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>
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

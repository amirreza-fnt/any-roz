@extends('backend.views.view')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/datepicker-jalali/bootstrap-datepicker.min.css') }}" type="text/css">
@endpush

@section('main')
<div class="main-content">
    <div class="container-fluid">
        <div class="page-header mb-3">
            <h4 class="mb-1">ویرایش سند #{{ $entry->id }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.accounting.journal.index') }}">دفتر مجزا</a></li>
                    <li class="breadcrumb-item active">ویرایش</li>
                </ol>
            </nav>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="post" action="{{ route('admin.accounting.journal.update', $entry) }}">
                    @csrf
                    @method('put')
                    @include('backend.accounting.journal._form', ['entry' => $entry])
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">به‌روزرسانی</button>
                        <a href="{{ route('admin.accounting.journal.index') }}" class="btn btn-light rounded-pill">بازگشت</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@php
    $shamsiYear = \App\Support\JalaliCalendar::currentJalaliYear();
@endphp
<script src="{{ asset('assets/back-end/vendors/datepicker-jalali/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/back-end/vendors/datepicker-jalali/bootstrap-datepicker.fa.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof jQuery === 'undefined' || !jQuery.fn.datepicker) return;
    var y0 = {{ (int) $shamsiYear }};
    jQuery('#journal_doc_date').datepicker({
        dateFormat: 'yy/mm/dd',
        showOtherMonths: true,
        selectOtherMonths: true,
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        yearRange: (y0 - 20) + ':' + (y0 + 1)
    });
});
</script>
@endpush

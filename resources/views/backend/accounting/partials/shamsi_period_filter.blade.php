@php
    use App\Support\JalaliCalendar;
    $jFromVal = request('j_date_from') !== null && request('j_date_from') !== ''
        ? (string) request('j_date_from')
        : JalaliCalendar::formatShamsiDate($from);
    $jToVal = request('j_date_to') !== null && request('j_date_to') !== ''
        ? (string) request('j_date_to')
        : JalaliCalendar::formatShamsiDate($to);
@endphp
<div class="col-md-3 mb-2 mb-md-0">
    <label class="small text-muted">از تاریخ (شمسی)</label>
    <input type="text" name="j_date_from" autocomplete="off" class="form-control acct-j-date text-left" dir="ltr" value="{{ $jFromVal }}" placeholder="۱۴۰۴/۰۱/۰۱">
</div>
<div class="col-md-3 mb-2 mb-md-0">
    <label class="small text-muted">تا تاریخ (شمسی)</label>
    <input type="text" name="j_date_to" autocomplete="off" class="form-control acct-j-date text-left" dir="ltr" value="{{ $jToVal }}" placeholder="۱۴۰۴/۱۲/۲۹">
</div>

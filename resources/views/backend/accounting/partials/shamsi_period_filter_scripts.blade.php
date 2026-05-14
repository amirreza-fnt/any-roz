@php
    $shamsiYear = \App\Support\JalaliCalendar::currentJalaliYear();
@endphp
<script src="{{ asset('assets/back-end/vendors/datepicker-jalali/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/back-end/vendors/datepicker-jalali/bootstrap-datepicker.fa.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof jQuery === 'undefined' || !jQuery.fn.datepicker) return;
    var y0 = {{ (int) $shamsiYear }};
    jQuery('.acct-j-date').datepicker({
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

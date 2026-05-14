@extends('backend.views.view')

@section('main')
<div class="main-content">
    <div class="container py-4">
        <div class="page-header mb-3">
            <h4>دانلود پشتیبان</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ admin_home_url() }}">خانه</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.technical-backup.index') }}">پشتیبان‌گیری فنی</a></li>
                    <li class="breadcrumb-item active">دانلود</li>
                </ol>
            </nav>
        </div>

        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
        @endif

        @if (is_array(session('tb_warnings')) && count(session('tb_warnings')))
            <div class="alert alert-warning border-0 shadow-sm">
                <ul class="mb-0 pr-3">
                    @foreach (session('tb_warnings') as $w)
                        <li>{{ $w }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div id="tb-status" class="alert alert-info border-0 shadow-sm mb-3">در حال آماده‌سازی دانلودها…</div>

        <p class="text-muted small mb-3">برای هر بخش یک فایل جدا (CSV سازگار با Excel) ذخیره می‌شود. اگر مرورگر اجازهٔ چند دانلود پشت‌سرهم را نداد، در نوار آدرس روی آیکن قفل یا دانلود کلیک کنید و مسدودیت را بردارید.</p>

        <a href="{{ route('admin.technical-backup.index') }}" class="btn btn-outline-secondary">بازگشت به پشتیبان‌گیری فنی</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function () {
    const urls = @json($downloadUrls);
    const names = @json($filenames);
    const status = document.getElementById('tb-status');
    if (!urls.length) {
        status.className = 'alert alert-warning border-0 shadow-sm';
        status.textContent = 'فایلی برای دانلود یافت نشد.';
        return;
    }
    try {
        for (let i = 0; i < urls.length; i++) {
            status.className = 'alert alert-info border-0 shadow-sm';
            status.textContent = 'در حال دریافت فایل ' + (i + 1) + ' از ' + urls.length + '…';
            const r = await fetch(urls[i], {
                credentials: 'same-origin',
                headers: { 'Accept': 'text/csv,*/*', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!r.ok) {
                throw new Error('خطای ' + r.status);
            }
            const blob = await r.blob();
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = names[i] || ('export-' + (i + 1) + '.csv');
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(a.href);
            await new Promise(function (res) { setTimeout(res, 550); });
        }
        status.className = 'alert alert-success border-0 shadow-sm';
        status.textContent = 'درخواست دانلود همهٔ فایل‌ها ارسال شد. پوشهٔ دانلود مرورگر را بررسی کنید.';
        if (typeof toastr !== 'undefined') {
            toastr.success('دانلودها تکمیل شد.');
        }
    } catch (e) {
        status.className = 'alert alert-danger border-0 shadow-sm';
        status.textContent = 'خطا در دانلود: ' + (e && e.message ? e.message : 'نامشخص');
        if (typeof toastr !== 'undefined') {
            toastr.error(status.textContent);
        }
    }
});
</script>
@endpush

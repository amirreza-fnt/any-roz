@extends('backend.views.view')

@php
    $phones = old('phones', $settings->phones ?? []);
    $emails = old('emails', $settings->emails ?? []);
    $addresses = old('addresses', $settings->addresses ?? []);
    $socials = old('socials', $settings->socials ?? []);
    if (! is_array($phones) || count($phones) === 0) { $phones = ['']; }
    if (! is_array($emails) || count($emails) === 0) { $emails = ['']; }
    if (! is_array($addresses) || count($addresses) === 0) { $addresses = [['label' => '', 'body' => '', 'map_url' => '']]; }
    if (! is_array($socials) || count($socials) === 0) { $socials = [['network' => 'website', 'url' => '']]; }
@endphp

@push('styles')
<style>
    .cs-card { border-radius: 14px; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 1.25rem; }
    .cs-card .hd { background: linear-gradient(120deg,#0ea5e9,#6366f1); color:#fff; padding:.75rem 1rem; font-weight:700; font-size:.9rem; display:flex; justify-content:space-between; align-items:center; }
    .cs-card .bd { padding: 1rem; background: #fafafa; }
    .rep-row { background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:.75rem; margin-bottom:.5rem; position:relative; }
    .rep-row .btn-remove { position:absolute; top:.5rem; left:.5rem; }
</style>
@endpush

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>مدیریت ارتباطات</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">خانه</a></li>
                    <li class="breadcrumb-item active">ارتباطات</li>
                </ol>
            </nav>
        </div>

        <p class="text-muted small mb-3">تمام اطلاعات این صفحه در یک رکورد مرکزی ذخیره می‌شود و برای نمایش در سایت کاربر استفاده خواهد شد.</p>

        <form method="post" action="{{ route('admin.contact-settings.update') }}" class="card border-0 shadow-sm">
            @csrf
            @method('PUT')

            <div class="card-body">
                <div class="form-group">
                    <label>عنوان بخش تماس (اختیاری)</label>
                    <input type="text" name="support_title" class="form-control" value="{{ old('support_title', $settings->support_title) }}" maxlength="255" placeholder="مثلاً با ما در تماس باشید">
                </div>

                <div class="cs-card">
                    <div class="hd"><span>شماره تلفن‌ها</span><button type="button" class="btn btn-sm btn-light add-phone">+ ردیف</button></div>
                    <div class="bd" id="phones-wrap">
                        @foreach ($phones as $i => $p)
                            <div class="rep-row">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove rm-row" tabindex="-1">حذف</button>
                                <label class="small text-muted d-block">شماره {{ $i + 1 }}</label>
                                <input type="text" name="phones[]" class="form-control" value="{{ $p }}" maxlength="64" placeholder="مثلاً 021-...">
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="cs-card">
                    <div class="hd"><span>ایمیل‌ها</span><button type="button" class="btn btn-sm btn-light add-email">+ ردیف</button></div>
                    <div class="bd" id="emails-wrap">
                        @foreach ($emails as $i => $e)
                            <div class="rep-row">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove rm-row">حذف</button>
                                <label class="small text-muted d-block">ایمیل {{ $i + 1 }}</label>
                                <input type="email" name="emails[]" class="form-control" dir="ltr" value="{{ $e }}" maxlength="255">
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="cs-card">
                    <div class="hd"><span>آدرس‌ها</span><button type="button" class="btn btn-sm btn-light add-adr">+ ردیف</button></div>
                    <div class="bd" id="adr-wrap">
                        @foreach ($addresses as $i => $a)
                            <div class="rep-row adr-block">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove rm-row">حذف</button>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label class="small text-muted">برچسب</label>
                                        <input type="text" name="addresses[{{ $i }}][label]" class="form-control" value="{{ $a['label'] ?? '' }}" maxlength="120" placeholder="دفتر مرکزی">
                                    </div>
                                    <div class="form-group col-md-8">
                                        <label class="small text-muted">لینک نقشه (اختیاری)</label>
                                        <input type="text" name="addresses[{{ $i }}][map_url]" class="form-control" dir="ltr" value="{{ $a['map_url'] ?? '' }}" maxlength="1024">
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="small text-muted">آدرس کامل</label>
                                    <textarea name="addresses[{{ $i }}][body]" class="form-control" rows="2" maxlength="4000">{{ $a['body'] ?? '' }}</textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="cs-card">
                    <div class="hd"><span>شبکه‌های اجتماعی</span><button type="button" class="btn btn-sm btn-light add-soc">+ ردیف</button></div>
                    <div class="bd" id="soc-wrap">
                        @foreach ($socials as $i => $s)
                            <div class="rep-row soc-block">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove rm-row">حذف</button>
                                <div class="form-row align-items-end">
                                    <div class="form-group col-md-4">
                                        <label class="small text-muted">پلتفرم</label>
                                        <select name="socials[{{ $i }}][network]" class="form-control">
                                            @foreach ($socialOptions as $key => $label)
                                                <option value="{{ $key }}" @selected(($s['network'] ?? '') === $key)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-8 mb-0">
                                        <label class="small text-muted">لینک</label>
                                        <input type="text" name="socials[{{ $i }}][url]" class="form-control" dir="ltr" value="{{ $s['url'] ?? '' }}" maxlength="1024" placeholder="https://...">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>فکس</label>
                        <input type="text" name="fax" class="form-control" value="{{ old('fax', $settings->fax) }}" maxlength="64">
                    </div>
                </div>
                <div class="form-group">
                    <label>ساعات کاری / پاسخگویی</label>
                    <textarea name="working_hours" class="form-control" rows="3" maxlength="8000">{{ old('working_hours', $settings->working_hours) }}</textarea>
                </div>
                <div class="form-group">
                    <label>یادداشت پاورقی (اختیاری)</label>
                    <textarea name="footer_note" class="form-control" rows="2" maxlength="8000">{{ old('footer_note', $settings->footer_note) }}</textarea>
                </div>
            </div>

            <div class="card-footer bg-white d-flex justify-content-between">
                <span class="text-muted small">ذخیره پس از ویرایش</span>
                <button type="submit" class="btn btn-primary px-4">ذخیرهٔ اطلاعات</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    let adrIdx = {{ count($addresses) }};
    let socIdx = {{ count($socials) }};
    const socOptions = @json($socialOptions);

    function optHtml(selected) {
        let h = '';
        for (const [k, v] of Object.entries(socOptions)) {
            h += '<option value="' + k + '"' + (k === selected ? ' selected' : '') + '>' + v + '</option>';
        }
        return h;
    }

    $('.add-phone').on('click', function () {
        $('#phones-wrap').append(
            '<div class="rep-row"><button type="button" class="btn btn-sm btn-outline-danger btn-remove rm-row">حذف</button>' +
            '<label class="small text-muted d-block">شماره جدید</label>' +
            '<input type="text" name="phones[]" class="form-control" maxlength="64"></div>'
        );
    });
    $('.add-email').on('click', function () {
        $('#emails-wrap').append(
            '<div class="rep-row"><button type="button" class="btn btn-sm btn-outline-danger btn-remove rm-row">حذف</button>' +
            '<label class="small text-muted d-block">ایمیل جدید</label>' +
            '<input type="email" name="emails[]" class="form-control" dir="ltr" maxlength="255"></div>'
        );
    });
    $('.add-adr').on('click', function () {
        const i = adrIdx++;
        $('#adr-wrap').append(
            '<div class="rep-row adr-block"><button type="button" class="btn btn-sm btn-outline-danger btn-remove rm-row">حذف</button>' +
            '<div class="form-row"><div class="form-group col-md-4"><label class="small text-muted">برچسب</label>' +
            '<input type="text" name="addresses[' + i + '][label]" class="form-control" maxlength="120"></div>' +
            '<div class="form-group col-md-8"><label class="small text-muted">لینک نقشه</label>' +
            '<input type="text" name="addresses[' + i + '][map_url]" class="form-control" dir="ltr" maxlength="1024"></div></div>' +
            '<div class="form-group mb-0"><label class="small text-muted">آدرس کامل</label>' +
            '<textarea name="addresses[' + i + '][body]" class="form-control" rows="2" maxlength="4000"></textarea></div></div>'
        );
    });
    $('.add-soc').on('click', function () {
        const i = socIdx++;
        $('#soc-wrap').append(
            '<div class="rep-row soc-block"><button type="button" class="btn btn-sm btn-outline-danger btn-remove rm-row">حذف</button>' +
            '<div class="form-row align-items-end"><div class="form-group col-md-4"><label class="small text-muted">پلتفرم</label>' +
            '<select name="socials[' + i + '][network]" class="form-control">' + optHtml('website') + '</select></div>' +
            '<div class="form-group col-md-8 mb-0"><label class="small text-muted">لینک</label>' +
            '<input type="text" name="socials[' + i + '][url]" class="form-control" dir="ltr" maxlength="1024"></div></div></div>'
        );
    });

    $(document).on('click', '.rm-row', function () {
        const row = $(this).closest('.rep-row');
        const container = row.parent();
        if (container.children('.rep-row').length <= 1) {
            return;
        }
        row.remove();
    });

    if (typeof feather !== 'undefined') feather.replace();
})();
</script>
@endpush

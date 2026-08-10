@php
    $b = $buyer;
@endphp

<div class="row">
    <div class="col-lg-6">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>نام <span class="text-danger">*</span></label>
                <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $b?->first_name) }}" required maxlength="120">
            </div>
            <div class="form-group col-md-6">
                <label>نام خانوادگی <span class="text-danger">*</span></label>
                <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $b?->last_name) }}" required maxlength="120">
            </div>
        </div>
        <div class="form-group">
            <label>شماره تماس <span class="text-danger">*</span></label>
            <input type="text" name="phone" class="form-control text-left" dir="ltr" value="{{ old('phone', $b?->phone) }}" required placeholder="09123456789" maxlength="32">
        </div>
        <div class="form-group">
            <label>نام مغازه</label>
            <input type="text" name="store_name" class="form-control" value="{{ old('store_name', $b?->store_name) }}" maxlength="255">
        </div>
        <div class="form-group">
            <label>آدرس</label>
            <textarea name="address" id="buyer-address" class="form-control" rows="3" maxlength="2000">{{ old('address', $b?->address) }}</textarea>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>استان</label>
                <select name="province_id" id="buyer-province" class="form-control">
                    <option value="">— انتخاب —</option>
                    @foreach ($provinces as $p)
                        <option value="{{ $p->id }}" @selected((int) old('province_id', $b?->province_id) === $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6">
                <label>شهر</label>
                <select name="city_id" id="buyer-city" class="form-control">
                    <option value="">— انتخاب —</option>
                    @foreach ($cities ?? [] as $c)
                        <option value="{{ $c->id }}" @selected((int) old('city_id', $b?->city_id) === $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>کد پستی</label>
            <input type="text" name="postal_code" class="form-control text-left" dir="ltr" value="{{ old('postal_code', $b?->postal_code) }}" maxlength="20">
        </div>
        <div class="form-group">
            <label>یادداشت داخلی</label>
            <textarea name="notes" class="form-control" rows="2" maxlength="5000">{{ old('notes', $b?->notes) }}</textarea>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="border rounded-lg p-3 mb-3 bg-light">
            <h6 class="font-weight-bold mb-2">موقعیت روی نقشه</h6>
            <p class="small text-muted mb-2">با کلیک روی نقشه یا جابه‌جایی نشانگر، مختصات ثبت می‌شود.</p>
            <div id="buyer-map" class="rounded border bg-white" style="height:320px;z-index:1"></div>
            <div class="form-row mt-2">
                <div class="form-group col-6 mb-0">
                    <label class="small text-muted">عرض جغرافیایی</label>
                    <input type="text" name="latitude" id="buyer-lat" class="form-control form-control-sm text-left" dir="ltr" readonly value="{{ old('latitude', $b?->latitude) }}">
                </div>
                <div class="form-group col-6 mb-0">
                    <label class="small text-muted">طول جغرافیایی</label>
                    <input type="text" name="longitude" id="buyer-lng" class="form-control form-control-sm text-left" dir="ltr" readonly value="{{ old('longitude', $b?->longitude) }}">
                </div>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm btn-block mt-2" id="btn-buyer-reverse-geo">پیشنهاد آدرس از نشان (تبدیل نقطه به آدرس)</button>
        </div>
    </div>
</div>

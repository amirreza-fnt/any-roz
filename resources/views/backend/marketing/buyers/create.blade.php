@extends('backend.views.view')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<style>#buyer-map { min-height: 320px; }</style>
@endpush

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>افزودن خریدار</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.marketing') }}">بازاریابی</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.marketing.buyers.index') }}">خریداران</a></li>
                    <li class="breadcrumb-item active">افزودن</li>
                </ol>
            </nav>
        </div>

        <form method="post" action="{{ route('admin.marketing.buyers.store') }}" class="card border-0 shadow-sm">
            @csrf
            <div class="card-body">
                @include('backend.marketing.buyers._form', ['buyer' => null, 'provinces' => $provinces, 'cities' => collect()])
            </div>
            <div class="card-footer bg-white d-flex justify-content-between">
                <a href="{{ route('admin.marketing.buyers.index') }}" class="btn btn-light">بازگشت</a>
                <button type="submit" class="btn btn-primary px-4">ذخیره</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function () {
    var map = L.map('buyer-map', { zoomControl: true }).setView([32.4279, 53.6880], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);
    var latIn = document.getElementById('buyer-lat');
    var lngIn = document.getElementById('buyer-lng');
    var lat = parseFloat(latIn.value) || 35.6892;
    var lng = parseFloat(lngIn.value) || 51.3890;
    var marker = L.marker([lat, lng], { draggable: true }).addTo(map);
    map.setView([lat, lng], latIn.value ? 14 : 6);
    function sync() {
        var ll = marker.getLatLng();
        latIn.value = ll.lat.toFixed(6);
        lngIn.value = ll.lng.toFixed(6);
    }
    marker.on('dragend', sync);
    map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        sync();
    });
    sync();

    var prov = document.getElementById('buyer-province');
    var city = document.getElementById('buyer-city');
    var citiesUrl = @json(route('admin.marketing.api.cities'));
    prov.addEventListener('change', function () {
        var pid = this.value;
        city.innerHTML = '<option value=\"\">— انتخاب —</option>';
        if (!pid) return;
        fetch(citiesUrl + '?province_id=' + encodeURIComponent(pid), { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (rows) {
                rows.forEach(function (c) {
                    var o = document.createElement('option');
                    o.value = c.id;
                    o.textContent = c.name;
                    city.appendChild(o);
                });
            });
    });
    if (typeof feather !== 'undefined') feather.replace();
})();
</script>
@endpush

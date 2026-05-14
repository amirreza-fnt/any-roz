<script>
(function () {
    var mapKey = @json(config('neshan.map_api_key'));
    var el = document.getElementById('buyer-map');
    if (!el) return;
    if (!mapKey) {
        el.innerHTML = '<div class="p-3 text-danger small">کلید نقشهٔ نشان تنظیم نشده است. متغیر <code>NESHAN_MAP_API_KEY</code> را در فایل .env قرار دهید.</div>';
        return;
    }

    var latIn = document.getElementById('buyer-lat');
    var lngIn = document.getElementById('buyer-lng');
    var lat = parseFloat(latIn.value) || 35.6892;
    var lng = parseFloat(lngIn.value) || 51.3890;

    var map = new L.Map('buyer-map', {
        key: mapKey,
        maptype: 'dreamy',
        center: [lat, lng],
        zoom: latIn.value ? 14 : 6
    });

    var marker = L.marker([lat, lng], { draggable: true }).addTo(map);

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

    var revUrl = @json(route('admin.marketing.api.reverse-geocode'));
    var revBtn = document.getElementById('btn-buyer-reverse-geo');
    var addrTa = document.querySelector('textarea[name="address"]');
    if (revBtn && addrTa) {
        revBtn.addEventListener('click', function () {
            revBtn.disabled = true;
            fetch(revUrl + '?lat=' + encodeURIComponent(latIn.value) + '&lng=' + encodeURIComponent(lngIn.value), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function (r) { return r.json(); })
                .then(function (j) {
                    revBtn.disabled = false;
                    if (j.ok && j.address) {
                        var t = (addrTa.value || '').trim();
                        addrTa.value = t ? (t + '\n' + j.address) : j.address;
                    } else if (j.message) {
                        alert(j.message);
                    } else {
                        alert('آدرسی از سرویس دریافت نشد.');
                    }
                })
                .catch(function () {
                    revBtn.disabled = false;
                    alert('خطا در اتصال به سرویس آدرس‌یابی نشان.');
                });
        });
    }

    var prov = document.getElementById('buyer-province');
    var city = document.getElementById('buyer-city');
    var citiesUrl = @json(route('admin.marketing.api.cities'));
    if (prov && city) {
        prov.addEventListener('change', function () {
            var pid = this.value;
            city.innerHTML = '<option value="">— انتخاب —</option>';
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
    }
    if (typeof feather !== 'undefined') feather.replace();
})();
</script>

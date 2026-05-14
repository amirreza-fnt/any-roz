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
                    alert('خطا در اتصال به سرویس آدرس‌یابی.');
                });
        });
    }

    var prov = document.getElementById('buyer-province');
    var city = document.getElementById('buyer-city');
    var citiesUrl = @json(route('admin.marketing.api.cities'));
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
    if (typeof feather !== 'undefined') feather.replace();
})();
</script>

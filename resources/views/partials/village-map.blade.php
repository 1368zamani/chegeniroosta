@if(!empty($village->latitude) && !empty($village->longitude))
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #vVillageMap {
        width: 100%;
        height: 420px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,.06);
        z-index: 1;
    }
    .v-map-wrapper {
        margin: 30px 0;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,.06);
    }
    .v-map-actions {
        display: flex;
        gap: 10px;
        padding: 14px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        flex-wrap: wrap;
    }
    .v-map-btn {
        flex: 1;
        min-width: 160px;
        text-align: center;
        padding: 11px 16px;
        background: #1565c0;
        color: #fff !important;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: .9rem;
    }
    .v-map-btn:hover { background: #0d47a1; color: #fff !important; }
    .v-map-btn-secondary { background: #f1f5f9; color: #1565c0 !important; border: 1px solid #cbd5e1; }
    .leaflet-popup-content { font-family: 'Vazirmatn', Tahoma, sans-serif; direction: rtl; margin: 12px 16px !important; }
    @media (max-width: 640px) {
        #vVillageMap { height: 320px; }
        .v-map-btn { min-width: 100%; }
    }
</style>

<div class="v-map-wrapper">
    <div id="vVillageMap"
         data-lat="{{ $village->latitude }}"
         data-lng="{{ $village->longitude }}"
         data-name="{{ $village->name }}"
         data-address="{{ $village->address ?? '' }}"></div>

    <div class="v-map-actions">
        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $village->latitude }},{{ $village->longitude }}"
           target="_blank" rel="noopener" class="v-map-btn">
            🧭 مسیریابی با گوگل مپ
        </a>
        <a href="https://www.openstreetmap.org/?mlat={{ $village->latitude }}&mlon={{ $village->longitude }}#map=15/{{ $village->latitude }}/{{ $village->longitude }}"
           target="_blank" rel="noopener" class="v-map-btn v-map-btn-secondary">
            🗺 مشاهده در OpenStreetMap
        </a>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function(){
    var el = document.getElementById('vVillageMap');
    if (!el || typeof L === 'undefined') return;
    var lat = parseFloat(el.dataset.lat);
    var lng = parseFloat(el.dataset.lng);
    if (!isFinite(lat) || !isFinite(lng)) return;

    var map = L.map('vVillageMap', {
        center: [lat, lng],
        zoom: 15,
        scrollWheelZoom: false
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    var icon = L.divIcon({
        className: '',
        html: '<svg width="40" height="40" viewBox="0 0 24 24" fill="#1565c0" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z" stroke="#fff" stroke-width="0.8"/></svg>',
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -38]
    });

    L.marker([lat, lng], { icon: icon })
        .addTo(map)
        .bindPopup('<b>' + el.dataset.name + '</b>' + (el.dataset.address ? '<br>' + el.dataset.address : ''))
        .openPopup();

    map.on('click', function(){ map.scrollWheelZoom.enable(); });
    map.on('mouseout', function(){ map.scrollWheelZoom.disable(); });
})();
</script>
@endif

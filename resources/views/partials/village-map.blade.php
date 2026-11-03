@php
    $__hasCoords = !empty($village->latitude) && !empty($village->longitude)
        && (float) $village->latitude !== 0.0 && (float) $village->longitude !== 0.0;
    $__mapsQuery = rawurlencode('روستای ' . $village->name . ' ' . ($village->county ?? 'چگنی'));
@endphp

@if($__hasCoords)
    <link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}">
    <style>
        /* ═══ نقشه‌ی موقعیت روستا ═══ */
        .v-map-wrap {
            margin: 20px 0;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 14px rgba(0, 0, 0, .07);
            border: 1px solid #e8eee9;
        }

        .v-map-canvas {
            position: relative;
            width: 100%;
            height: 300px;
            background: #eef2ee;
        }
        #vVillageMap {
            width: 100%;
            height: 100%;
            z-index: 1;
            opacity: 0;
            transition: opacity .5s ease;
        }
        #vVillageMap.is-ready { opacity: 1; }

        /* اسکلت بارگذاری */
        .v-map-skeleton {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: linear-gradient(135deg, #eef2ee, #e3ece5, #eef2ee);
            background-size: 200% 200%;
            animation: vMapShift 2.4s ease-in-out infinite;
            color: #4b6b52;
            font-size: 13px;
            z-index: 2;
            transition: opacity .35s ease;
        }
        .v-map-skeleton.is-hidden { opacity: 0; pointer-events: none; }

        @keyframes vMapShift {
            0%, 100% { background-position: 0% 50%; }
            50%      { background-position: 100% 50%; }
        }

        .v-map-spinner {
            width: 38px;
            height: 38px;
            border: 3px solid #c8e6c9;
            border-top-color: #2e7d32;
            border-radius: 50%;
            animation: vMapSpin .9s linear infinite;
        }
        @keyframes vMapSpin { to { transform: rotate(360deg); } }

        /* پیام خطا / جایگزین */
        .v-map-error {
            position: absolute;
            inset: 0;
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 24px;
            text-align: center;
            background: #f8faf8;
            color: #4b6b52;
            font-size: 13.5px;
            line-height: 1.9;
            z-index: 3;
        }
        .v-map-error.is-shown { display: flex; }

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
            min-width: 150px;
            text-align: center;
            padding: 9px 14px;
            background: #2e7d32;
            color: #fff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: .85rem;
            transition: background .2s ease;
        }
        .v-map-btn:hover { background: #1b5e20; color: #fff !important; }
        .v-map-btn-secondary {
            background: #f1f5f9;
            color: #2e7d32 !important;
            border: 1px solid #cbd5e1;
        }
        .v-map-btn-secondary:hover { background: #e8f5e9; }

        .leaflet-popup-content {
            font-family: 'Vazirmatn', Tahoma, sans-serif;
            direction: rtl;
            margin: 12px 16px !important;
        }

        /* ── تبلت: کوچک‌تر ── */
        @media (max-width: 820px) {
            .v-map-canvas { height: 240px; }
            .v-map-wrap { margin: 16px 0; }
        }

        /* ── موبایل: جمع‌وجور ── */
        @media (max-width: 560px) {
            .v-map-canvas { height: 190px; }
            .v-map-wrap { margin: 14px 0; border-radius: 10px; }
            .v-map-actions { padding: 8px; gap: 6px; }
            .v-map-btn { min-width: 100%; padding: 8px 12px; font-size: .8rem; }
            .v-map-skeleton { gap: 8px; font-size: 11.5px; }
            .v-map-spinner { width: 28px; height: 28px; border-width: 2.5px; }
            .v-map-error { padding: 14px; font-size: 12px; }
        }

        @media (prefers-reduced-motion: reduce) {
            .v-map-skeleton { animation: none; }
            .v-map-spinner { animation: none; }
        }
    </style>

    <div class="v-map-wrap">
        <div class="v-map-canvas">
            <div id="vVillageMap"
                 data-lat="{{ $village->latitude }}"
                 data-lng="{{ $village->longitude }}"
                 data-name="{{ $village->name }}"
                 data-address="{{ $village->address ?? '' }}"></div>

            {{-- اسکلت بارگذاری --}}
            <div class="v-map-skeleton" id="vMapSkeleton">
                <div class="v-map-spinner"></div>
                <div>در حال بارگذاری نقشه…</div>
                <div style="font-size:11.5px; opacity:.75;">اگر نقشه باز نشد، از دکمه‌های پایین استفاده کنید</div>
            </div>

            {{-- پیام جایگزین اگر کاشی‌ها بارگذاری نشدند --}}
            <div class="v-map-error" id="vMapError">
                <div style="font-size:30px;">🗺</div>
                <div><strong>نقشه در دسترس نیست</strong></div>
                <div style="font-size:12px; opacity:.8;">
                    اتصال به سرور نقشه برقرار نشد. می‌توانید موقعیت روستا را از طریق
                    دکمه‌های پایین مشاهده کنید.
                </div>
            </div>
        </div>

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

    <script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
    <script>
    (function () {
        'use strict';

        var el = document.getElementById('vVillageMap');
        if (!el) return;

        var skeleton = document.getElementById('vMapSkeleton');
        var errorBox = document.getElementById('vMapError');

        function showError() {
            if (skeleton) skeleton.classList.add('is-hidden');
            if (errorBox) errorBox.classList.add('is-shown');
        }

        function showMap() {
            if (skeleton) skeleton.classList.add('is-hidden');
            el.classList.add('is-ready');
        }

        if (typeof L === 'undefined') {
            showError();
            return;
        }

        var lat = parseFloat(el.dataset.lat);
        var lng = parseFloat(el.dataset.lng);

        if (!isFinite(lat) || !isFinite(lng)) {
            showError();
            return;
        }

        try {
            var map = L.map('vVillageMap', {
                center: [lat, lng],
                zoom: 15,
                scrollWheelZoom: false,
                zoomControl: true,
                attributionControl: true
            });

            var loadedAnyTile = false;

            /*
             * ═══ زنجیره‌ی سرورهای کاشی ═══
             *
             * نکته‌ی مهم: سرور رسمی OSM برای برخی شبکه‌ها (از جمله ایران)
             * پاسخ ۴۰۳ می‌دهد و هدر «x-blocked» برمی‌گرداند. به همین دلیل
             * CARTO اول امتحان می‌شود که سریع‌تر و بازتر است؛ اگر آن هم
             * شکست خورد، OSM به‌عنوان جایگزین امتحان می‌شود.
             */
            var PROVIDERS = [
                {
                    url: 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}.png',
                    options: {
                        maxZoom: 19,
                        subdomains: ['a', 'b', 'c'],
                        attribution: '© OpenStreetMap · © CARTO'
                    }
                },
                {
                    url: 'https://{s}.tile.openstreetmap.de/{z}/{x}/{y}.png',
                    options: {
                        maxZoom: 19,
                        subdomains: ['a', 'b', 'c'],
                        attribution: '© OpenStreetMap'
                    }
                },
                {
                    url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    options: {
                        maxZoom: 19,
                        subdomains: ['a', 'b', 'c'],
                        attribution: '© OpenStreetMap'
                    }
                }
            ];

            var providerIndex = 0;
            var currentLayer  = null;
            var tileErrors    = 0;
            var anyTileLoaded = false;

            /*
             * اگر ۲ کاشی از یک سرور خطا دادند، به سرور بعدی می‌رویم.
             * (حتی اگر بعضی کاشی‌ها قبلاً لود شده باشند — چون ممکن است
             *  فقط بخشی از نقشه ۴۰۳ بدهد و نقشه‌ی ناقص بدتر از جایگزین است)
             */
            function useProvider(index) {
                providerIndex = index;
                tileErrors = 0;

                var cfg = PROVIDERS[index];
                currentLayer = L.tileLayer(cfg.url, cfg.options);

                currentLayer.on('tileload', function () {
                    loadedAnyTile = true;
                    anyTileLoaded = true;
                    showMap();
                });

                currentLayer.on('tileerror', function () {
                    tileErrors++;

                    if (tileErrors >= 2) {
                        if (providerIndex + 1 < PROVIDERS.length) {
                            try { map.removeLayer(currentLayer); } catch (e) {}

                            // اگر سرور بعدی هم شکست خورد، خطای نهایی
                            useProvider(providerIndex + 1);
                            return;
                        }

                        if (tileErrors >= 4) {
                            showError();
                        }
                    }
                });

                currentLayer.addTo(map);
            }

            useProvider(0);

            /* اگر تا ۱۴ ثانیه هیچ کاشی‌ای نیامد، پیام جایگزین */
            setTimeout(function () {
                if (!anyTileLoaded) showError();
            }, 14000);

            /* تلاش دوباره‌ی خودکار پس از ۱۵ ثانیه (اگر همه شکست خوردند) */
            setTimeout(function () {
                if (anyTileLoaded) return;

                var errShown = errorBox && errorBox.classList.contains('is-shown');
                if (!errShown) return;

                try {
                    if (currentLayer) map.removeLayer(currentLayer);
                } catch (e) {}

                useProvider(0);
            }, 15000);

            /* آیکون سبز سفارشی */
            var icon = L.divIcon({
                className: '',
                html: '<svg width="40" height="40" viewBox="0 0 24 24" fill="#2e7d32" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z" stroke="#fff" stroke-width="0.8"/></svg>',
                iconSize: [40, 40],
                iconAnchor: [20, 40],
                popupAnchor: [0, -38]
            });

            L.marker([lat, lng], { icon: icon })
                .addTo(map)
                .bindPopup('<b>' + el.dataset.name + '</b>'
                    + (el.dataset.address ? '<br>' + el.dataset.address : ''))
                .openPopup();

            map.on('click', function () { map.scrollWheelZoom.enable(); });
            map.on('mouseout', function () { map.scrollWheelZoom.disable(); });

        } catch (e) {
            showError();
        }
    })();
    </script>

@else
    {{-- ═══ این روستا مختصات ندارد — کارت جایگزین با جست‌وجو در نقشه ═══ --}}
    <div class="v-map-fallback" style="margin:20px 0; background:#fff; border:1px solid #e8eee9;
                border-radius:12px; box-shadow:0 3px 14px rgba(0,0,0,.06); overflow:hidden;">
        <div style="padding:22px 18px; text-align:center;">
            <div style="font-size:32px; margin-bottom:8px;">🗺</div>
            <div style="font-weight:700; color:#1b5e20; font-size:14px; margin-bottom:5px;">
                موقعیت {{ $village->name }} روی نقشه
            </div>
            <div style="color:#607d8b; font-size:12.5px; line-height:1.9; margin-bottom:14px;">
                @if($village->address)
                    📍 {{ $village->address }}<br>
                @endif
                مختصات دقیق این روستا هنوز در سامانه ثبت نشده است.
                از دکمه‌های زیر می‌توانید موقعیت آن را روی نقشه ببینید.
            </div>
            <div style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
                <a href="https://www.google.com/maps/search/?api=1&query={{ $__mapsQuery }}"
                   target="_blank" rel="noopener"
                   style="padding:11px 20px; background:#2e7d32; color:#fff; text-decoration:none;
                          border-radius:8px; font-weight:600; font-size:13.5px;">
                    🔍 جست‌وجو در گوگل مپ
                </a>
                <a href="https://www.openstreetmap.org/search?query={{ $__mapsQuery }}"
                   target="_blank" rel="noopener"
                   style="padding:11px 20px; background:#f1f5f9; color:#2e7d32;
                          border:1px solid #cbd5e1; text-decoration:none;
                          border-radius:8px; font-weight:600; font-size:13.5px;">
                    🗺 جست‌وجو در OpenStreetMap
                </a>
            </div>
        </div>
    </div>
@endif

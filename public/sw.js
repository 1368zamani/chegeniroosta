/* ═══════════════════════════════════════════════════════════════
   Service Worker — سامانه اطلاع‌رسانی چگنی روستا
   ─────────────────────────────────────────────────────────────
   • صفحات  : شبکه اول (تازه‌ترین نسخه)، اگر آفلاین بود از کش
   • استاتیک: کش اول (سریع)، در پس‌زمینه به‌روزرسانی
   • پنل ادمین و API هرگز کش نمی‌شوند
   ═══════════════════════════════════════════════════════════════ */

const VERSION = 'chegeni-v1';
const PAGE_CACHE = VERSION + '-pages';
const ASSET_CACHE = VERSION + '-assets';

/* حداکثر تعداد صفحات کش‌شده (جلوگیری از رشد بی‌رویه) */
const MAX_PAGES = 40;

/* فایل‌های ضروری برای نصب اولیه */
const PRECACHE = [
    '/',
    '/favicon.png',
    '/css/persian-fonts.css',
    '/css/green-theme.css',
    '/css/mobile.css',
    '/css/site-animations.css',
    '/icons/icon-192.png'
];

/* مسیرهایی که هرگز نباید کش شوند */
function isPrivate(url) {
    const p = url.pathname;
    return p.startsWith('/admin')
        || p.startsWith('/api')
        || p.startsWith('/login')
        || p.startsWith('/logout')
        || p.startsWith('/storage/private');
}

/* فایل‌های استاتیک */
function isAsset(url) {
    return /\.(css|js|mjs|png|jpe?g|webp|gif|svg|ico|woff2?|ttf|eot|mp4|webm|pdf)$/i.test(url.pathname);
}

/* ─── نصب ─── */
self.addEventListener('install', function (event) {
    event.waitUntil(
        caches.open(ASSET_CACHE)
            .then(function (cache) {
                /* هر فایل جداگانه — یک خطا کل نصب را خراب نکند */
                return Promise.all(
                    PRECACHE.map(function (url) {
                        return cache.add(url).catch(function () { return null; });
                    })
                );
            })
            .then(function () { return self.skipWaiting(); })
    );
});

/* ─── فعال‌سازی: پاک کردن نسخه‌های قدیمی ─── */
self.addEventListener('activate', function (event) {
    event.waitUntil(
        caches.keys()
            .then(function (keys) {
                return Promise.all(
                    keys.map(function (key) {
                        if (key !== PAGE_CACHE && key !== ASSET_CACHE) {
                            return caches.delete(key);
                        }
                        return null;
                    })
                );
            })
            .then(function () { return self.clients.claim(); })
    );
});

/* ─── محدود کردن تعداد صفحات کش‌شده ─── */
function trimPageCache(cache) {
    cache.keys().then(function (keys) {
        if (keys.length <= MAX_PAGES) return;
        const remove = keys.slice(0, keys.length - MAX_PAGES);
        remove.forEach(function (req) { cache.delete(req); });
    });
}

/* ─── درخواست‌ها ─── */
self.addEventListener('fetch', function (event) {
    const request = event.request;

    if (request.method !== 'GET') return;

    let url;
    try { url = new URL(request.url); } catch (e) { return; }

    /* فقط دامنه‌ی خودمان */
    if (url.origin !== self.location.origin) return;

    /* پنل و API: مستقیم از شبکه، بدون کش */
    if (isPrivate(url)) return;

    /* ─── استاتیک: کش اول ─── */
    if (isAsset(url)) {
        event.respondWith(
            caches.match(request).then(function (cached) {
                if (cached) return cached;

                return fetch(request).then(function (response) {
                    if (response && response.status === 200) {
                        const copy = response.clone();
                        caches.open(ASSET_CACHE).then(function (cache) {
                            cache.put(request, copy);
                        });
                    }
                    return response;
                }).catch(function () {
                    return cached || Response.error();
                });
            })
        );
        return;
    }

    /* ─── صفحات: شبکه اول ─── */
    event.respondWith(
        fetch(request)
            .then(function (response) {
                if (response && response.status === 200) {
                    const copy = response.clone();
                    caches.open(PAGE_CACHE).then(function (cache) {
                        cache.put(request, copy);
                        trimPageCache(cache);
                    });
                }
                return response;
            })
            .catch(function () {
                /* آفلاین: از کش، وگرنه صفحه‌ی اصلی کش‌شده */
                return caches.match(request).then(function (cached) {
                    if (cached) return cached;
                    return caches.match('/').then(function (root) {
                        if (root) return root;
                        return new Response(
                            '<!DOCTYPE html><html lang="fa" dir="rtl"><head><meta charset="utf-8">'
                            + '<meta name="viewport" content="width=device-width,initial-scale=1">'
                            + '<title>آفلاین</title></head><body style="font-family:Tahoma,Arial;text-align:center;padding:60px 20px;direction:rtl">'
                            + '<div style="font-size:52px">📡</div>'
                            + '<h2 style="color:#2e7d32">اتصال اینترنت برقرار نیست</h2>'
                            + '<p style="color:#78909c">این صفحه قبلاً بازدید نشده و آفلاین در دسترس نیست.</p>'
                            + '</body></html>',
                            { headers: { 'Content-Type': 'text/html; charset=utf-8' } }
                        );
                    });
                });
            })
    );
});

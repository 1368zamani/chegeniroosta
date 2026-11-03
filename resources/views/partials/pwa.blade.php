{{--
    ═══════════════════════════════════════════════════════════════
    وب‌اپ (PWA) + بهینه‌سازی موبایل
    ─────────────────────────────────────────────────────────────
    یک بار داخل <head> هر صفحه‌ی عمومی اضافه می‌شود و همه‌ی
    سایت‌ها (پرتال مرکزی، بخش‌ها، روستاها) را وب‌اپ می‌کند:
      • نصب روی صفحه‌ی اصلی گوشی
      • کار آفلاین (کش هوشمند)
      • theme-color برای نوار مرورگر
      • استایل‌های ایمن موبایل
    ═══════════════════════════════════════════════════════════════
--}}

{{-- استایل موبایل (مشترک) --}}
<link rel="stylesheet" href="{{ asset('css/mobile.css') }}?v=1">

{{-- Manifest وب‌اپ --}}
<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">

{{-- رنگ نوار مرورگر + قابلیت نصب --}}
<meta name="theme-color" content="#2e7d32">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="چگنی روستا">
<meta name="format-detection" content="telephone=no">

{{-- آیکون‌ها --}}
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icons/icon-192.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/apple-touch-icon.png') }}">

{{-- ثبت Service Worker (بی‌صدا — اگر پشتیبانی نبود کاری نمی‌کند) --}}
<script>
(function () {
    if (!('serviceWorker' in navigator)) return;

    window.addEventListener('load', function () {
        navigator.serviceWorker
            .register('{{ asset('sw.js') }}', { scope: '/' })
            .catch(function () { /* بی‌صدا: عدم موفقیت نباید صفحه را خراب کند */ });
    });
})();
</script>

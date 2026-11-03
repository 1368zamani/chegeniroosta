<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>درخواست ثبت شد | {{ $village->name }}</title>
{{-- فونت وزیرمتن اکنون محلی از css/persian-fonts.css لود می‌شود (قبلاً از jsDelivr بود) --}}
@include('village.site._styles')

<style>
/* CARD-HOVER-2026 */
.info-item,
.info-card,
[class*="info-"] {
    transition: transform .25s ease, box-shadow .25s ease !important;
}
.info-item:hover,
.info-card:hover {
    transform: translateY(-3px) !important;
    box-shadow: 0 8px 24px rgba(13,46,79,0.15) !important;
}
.v-gallery-item:hover {
    transform: translateY(-3px) !important;
    box-shadow: 0 12px 30px rgba(13,46,79,0.18) !important;
}
/* tel: — لینک تماس */
a[href^="tel:"] {
    direction: ltr;
    display: inline-block;
    font-weight: 600;
}
</style>

<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
@include('partials.site-animations')
@include('partials.pwa')
</head>
<body>
<div class="wrap">
    <a href="{{ url('/village/' . $village->slug) }}" class="back">→ بازگشت به صفحه روستا</a>
    <div class="card" style="text-align:center">
        <h1 style="color:#16a34a">✅ درخواست شما ثبت شد</h1>
        <p class="subtitle">کد پیگیری خود را نزد خود نگه دارید.</p>

        <div class="track-code" id="code">{{ $request->tracking_code }}</div>

        <p style="color:#64748b; font-size:.9rem; margin-bottom:1rem">
            برای پیگیری وضعیت، این کد را در بخش «پیگیری درخواست» وارد کنید.
        </p>

        <button onclick="copyCode()" class="btn btn-secondary" style="margin-bottom:.75rem">📋 کپی کد پیگیری</button>

        <div class="actions">
            <a href="{{ route('village.site.request.track', $village->slug) }}" class="btn">🔍 پیگیری درخواست</a>
            <a href="{{ url('/village/' . $village->slug) }}" class="btn btn-secondary">🏠 صفحه روستا</a>
        </div>
    </div>
</div>
<script>
function copyCode() {
    const code = document.getElementById('code').innerText.trim();
    navigator.clipboard.writeText(code).then(() => {
        alert('کد پیگیری کپی شد: ' + code);
    }).catch(() => {
        alert('کد پیگیری: ' + code);
    });
}
</script>
</body>
</html>

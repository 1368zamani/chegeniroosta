<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>پیگیری درخواست | {{ $village->name }}</title>
<link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
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

</head>
<body>
<div class="wrap">
    <a href="{{ url('/village/' . $village->slug) }}" class="back">→ بازگشت به صفحه روستا</a>
    <div class="card">
        <h1>🔍 پیگیری درخواست</h1>
        <p class="subtitle">کد پیگیری خود را وارد کنید تا وضعیت درخواست را ببینید.</p>

        @if ($errors->any())
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('village.site.request.track.result', $village->slug) }}">
            @csrf
            <div class="field">
                <label>کد پیگیری <span class="req">*</span></label>
                <input type="text" name="tracking_code" value="{{ old('tracking_code') }}" required dir="ltr" placeholder="REQ-..." style="text-align:center; font-size:1.1rem; letter-spacing:1px">
            </div>
            <button type="submit">جستجو</button>
        </form>

        <div class="actions" style="margin-top:1rem">
            <a href="{{ route('village.site.request.create', $village->slug) }}" class="btn btn-secondary">✍️ ثبت درخواست جدید</a>
        </div>
    </div>
</div>
</body>
</html>

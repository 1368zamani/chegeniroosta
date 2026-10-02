<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ثبت درخواست | {{ $village->name }}</title>
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

<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
</head>
<body>
<div class="wrap">
    <a href="{{ url('/village/' . $village->slug) }}" class="back">→ بازگشت به صفحه روستا</a>
    <div class="card">
        <h1>📩 ثبت درخواست مردمی</h1>
        <p class="subtitle">درخواست خود را ثبت کنید و با کد پیگیری وضعیت آن را دنبال کنید.</p>

        @if ($errors->any())
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('village.site.request.store', $village->slug) }}" enctype="multipart/form-data">
            @csrf

            <div class="field">
                <label>نام و نام خانوادگی <span class="req">*</span></label>
                <input type="text" name="full_name" value="{{ old('full_name') }}" required maxlength="100">
            </div>

            <div class="field">
                <label>شماره تماس <span class="req">*</span></label>
                <input type="text" name="phone" value="{{ old('phone') }}" required maxlength="20" dir="ltr" placeholder="09xxxxxxxxx">
            </div>

            <div class="field">
                <label>موضوع درخواست <span class="req">*</span></label>
                <select name="subject" required>
                    <option value="">— انتخاب کنید —</option>
                    @foreach($subjects as $s)
                        <option value="{{ $s }}" @selected(old('subject') == $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>توضیحات <span class="req">*</span></label>
                <textarea name="description" required maxlength="2000" placeholder="شرح کامل درخواست...">{{ old('description') }}</textarea>
                <p class="hint">حداکثر ۲۰۰۰ کاراکتر</p>
            </div>

            <div class="field">
                <label>فایل پیوست (اختیاری)</label>
                <input type="file" name="attachment" accept="image/*,.pdf,.doc,.docx">
                <p class="hint">حداکثر ۵ مگابایت</p>
            </div>

            <button type="submit">✍️ ثبت درخواست</button>
        </form>

        <div class="actions" style="margin-top:1rem">
            <a href="{{ route('village.site.request.track', $village->slug) }}" class="btn btn-secondary">🔍 پیگیری درخواست قبلی</a>
        </div>
    </div>
</div>
</body>
</html>

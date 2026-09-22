<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>درخواست ثبت شد | {{ $village->name }}</title>
<link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
@include('village.site._styles')
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

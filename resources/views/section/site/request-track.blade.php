<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>پیگیری درخواست | بخشداری {{ $section->name }}</title>
<link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
@include('section.site._styles')
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
</head>
<body>
<div class="wrap">
    <a href="{{ url('/') }}" class="back">→ بازگشت به صفحه بخش</a>
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

        <form method="POST" action="{{ route('section.site.request.track.result') }}">
            @csrf
            <div class="field">
                <label>کد پیگیری <span class="req">*</span></label>
                <input type="text" name="tracking_code" value="{{ old('tracking_code') }}" required dir="ltr" placeholder="SEC-..." style="text-align:center; font-size:1.1rem; letter-spacing:1px">
            </div>
            <button type="submit">جستجو</button>
        </form>

        <div class="actions" style="margin-top:1rem">
            <a href="{{ route('section.site.request.create') }}" class="btn btn-secondary">✍️ ثبت درخواست جدید</a>
        </div>
    </div>
</div>
</body>
</html>

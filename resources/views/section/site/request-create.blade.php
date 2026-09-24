<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ثبت درخواست | بخشداری {{ $section->name }}</title>
<link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
@include('section.site._styles')
</head>
<body>
<div class="wrap">
    <a href="{{ url('/') }}" class="back">→ بازگشت به صفحه بخش</a>
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

        <form method="POST" action="{{ route('section.site.request.store') }}" enctype="multipart/form-data">
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
                <p class="hint">حداکثر ۲۵ مگابایت</p>
            </div>

            <button type="submit">✍️ ثبت درخواست</button>
        </form>

        <div class="actions" style="margin-top:1rem">
            <a href="{{ route('section.site.request.track') }}" class="btn btn-secondary">🔍 پیگیری درخواست قبلی</a>
        </div>
    </div>
</div>
</body>
</html>

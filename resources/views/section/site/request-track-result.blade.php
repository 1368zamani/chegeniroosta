<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>نتیجه پیگیری | بخشداری {{ $section->name }}</title>
{{-- فونت وزیرمتن اکنون محلی از css/persian-fonts.css لود می‌شود (قبلاً از jsDelivr بود) --}}
@include('section.site._styles')
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
@include('partials.pwa')
</head>
<body>
<div class="wrap">
    <a href="{{ url('/') }}" class="back">→ بازگشت به صفحه بخش</a>
    <div class="card">
        @if($found)
            <h1 style="color:#16a34a">✅ درخواست پیدا شد</h1>
            <p class="subtitle">وضعیت فعلی درخواست شما:</p>

            <div style="text-align:center; margin-bottom:1rem">
                <span class="status-badge status-{{ $found->status }}">{{ $found->status_label }}</span>
            </div>

            <div class="info-row"><div class="k">کد پیگیری:</div><div class="v" dir="ltr">{{ $found->tracking_code }}</div></div>
            <div class="info-row"><div class="k">نام:</div><div class="v">{{ $found->full_name }}</div></div>
            <div class="info-row"><div class="k">موضوع:</div><div class="v">{{ $found->subject }}</div></div>
            <div class="info-row"><div class="k">تاریخ ثبت:</div><div class="v">{{ jalali($found->created_at, 'Y/m/d H:i') }}</div></div>
            <div class="info-row"><div class="k">توضیحات:</div><div class="v">{{ $found->description }}</div></div>

            @if($found->admin_response)
                <div style="background:#f1f8e9; border-right:4px solid #2e7d32; padding:1rem; border-radius:8px; margin-top:1rem">
                    <strong style="color:#1b5e20">پاسخ بخشداری:</strong>
                    <p style="margin-top:.5rem">{{ $found->admin_response }}</p>
                </div>
            @endif
        @else
            <h1 style="color:#dc2626">❌ درخواستی با این کد پیدا نشد</h1>
            <p class="subtitle">لطفاً کد پیگیری را دوباره بررسی کنید.</p>
        @endif

        <div class="actions" style="margin-top:1.5rem">
            <a href="{{ route('section.site.request.track') }}" class="btn btn-secondary">🔍 جستجوی مجدد</a>
            <a href="{{ route('section.site.request.create') }}" class="btn">✍️ ثبت درخواست جدید</a>
        </div>
    </div>
</div>
</body>
</html>

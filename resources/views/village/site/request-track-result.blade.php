<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>نتیجه پیگیری | {{ $village->name }}</title>
<link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
@include('village.site._styles')
</head>
<body>
<div class="wrap">
    <a href="{{ url('/village/' . $village->slug) }}" class="back">→ بازگشت به صفحه روستا</a>
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
            <div class="info-row"><div class="k">تاریخ ثبت:</div><div class="v">{{ $found->created_at->format('Y/m/d H:i') }}</div></div>
            <div class="info-row"><div class="k">توضیحات:</div><div class="v">{{ $found->description }}</div></div>

            @if($found->admin_response)
                <div style="background:#f0f9ff; border-right:4px solid #1565c0; padding:1rem; border-radius:8px; margin-top:1rem">
                    <strong style="color:#0d47a1">پاسخ مدیر:</strong>
                    <p style="margin-top:.5rem">{{ $found->admin_response }}</p>
                </div>
            @endif
        @else
            <h1 style="color:#dc2626">❌ درخواستی با این کد پیدا نشد</h1>
            <p class="subtitle">لطفاً کد پیگیری را دوباره بررسی کنید.</p>
        @endif

        <div class="actions" style="margin-top:1.5rem">
            <a href="{{ route('village.site.request.track', $village->slug) }}" class="btn btn-secondary">🔍 جستجوی مجدد</a>
            <a href="{{ route('village.site.request.create', $village->slug) }}" class="btn">✍️ ثبت درخواست جدید</a>
        </div>
    </div>
</div>
</body>
</html>

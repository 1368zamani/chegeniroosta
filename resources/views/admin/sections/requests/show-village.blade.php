<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>درخواست {{ $request->tracking_code }}</title>
<style>
* { box-sizing: border-box; }
body { margin:0; font-family: Tahoma, Arial, sans-serif; background:#f5f7fa; color:#263238; }
.header { background: linear-gradient(135deg, #2e7d32, #388e3c); color: white; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
.header h1 { margin: 0; font-size: 20px; }
.header .nav a { color: white; text-decoration: none; padding: 8px 16px; background: rgba(255,255,255,0.15); border-radius: 8px; font-size: 13px; margin-left: 8px; }
.header .nav a:hover { background: rgba(255,255,255,0.25); }
.container { max-width: 900px; margin: auto; padding: 30px 20px; }
.card { background: white; border-radius: 12px; padding: 25px; margin-bottom: 18px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
.card h3 { color: #2e7d32; margin: 0 0 18px; padding-bottom: 12px; border-bottom: 2px solid #e8f5e9; font-size: 17px; }
.info-row { display: grid; grid-template-columns: 140px 1fr; gap: 8px 16px; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
.info-row:last-child { border-bottom: none; }
.info-row strong { color: #64748b; font-size: 13px; }
.desc { background: #f8fafc; padding: 14px; border-radius: 8px; line-height: 2; font-size: 14px; white-space: pre-line; margin-top: 10px; }
.badge { padding: 5px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; display: inline-block; }
.btn-cancel { padding: 12px 20px; background: #f1f5f9; color: #2e7d32; text-decoration: none; border-radius: 8px; display: inline-block; }
@media (max-width: 600px) { .info-row { grid-template-columns: 1fr; gap: 2px; } }
</style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
<link rel="stylesheet" href="/css/admin-master.css?v=2">
<link rel="stylesheet" href="{{ asset('css/admin-final-fix.css') }}?v=10">
<link rel="stylesheet" href="/css/admin-header-fix.css?v=1"></head>
<body>

<div class="header">
    <h1>🏘️ درخواست روستا — بخشداری {{ $section->name }}</h1>
    <div class="nav">
        <a href="{{ route('admin.sections.all-requests', $section->id) }}">← بازگشت به لیست</a>
    </div>
</div>

<div class="container">

    @php
        $badges = [
            'submitted'  => ['bg'=>'#e2e8f0','color'=>'#475569','label'=>'ثبت شده'],
            'reviewing'  => ['bg'=>'#fef3c7','color'=>'#92400e','label'=>'در حال بررسی'],
            'assigned'   => ['bg'=>'#fef3c7','color'=>'#92400e','label'=>'ارجاع شده'],
            'processing' => ['bg'=>'#fef3c7','color'=>'#92400e','label'=>'در حال پیگیری'],
            'resolved'   => ['bg'=>'#d1fae5','color'=>'#065f46','label'=>'پاسخ داده شده'],
            'rejected'   => ['bg'=>'#fee2e2','color'=>'#991b1b','label'=>'رد شده'],
        ];
        $b = $badges[$request->status] ?? ['bg'=>'#e2e8f0','color'=>'#475569','label'=>$request->status];
    @endphp

    <div class="card">
        <h3>📋 اطلاعات درخواست</h3>

        <div class="info-row">
            <strong>کد پیگیری:</strong>
            <span style="font-family:monospace;direction:ltr;color:#2e7d32;font-weight:700;font-size:15px;">{{ $request->tracking_code }}</span>
        </div>
        <div class="info-row">
            <strong>روستا:</strong>
            <span>{{ optional($request->village)->name ?? '—' }}</span>
        </div>
        <div class="info-row">
            <strong>نام درخواست‌کننده:</strong>
            <span>{{ $request->full_name }}</span>
        </div>
        <div class="info-row">
            <strong>شماره تماس:</strong>
            <span style="direction:ltr;"><a href="tel:{{ $request->phone }}" style="color:#2e7d32;">{{ $request->phone }}</a></span>
        </div>
        <div class="info-row">
            <strong>موضوع:</strong>
            <span>{{ $request->subject }}</span>
        </div>
        <div class="info-row">
            <strong>وضعیت:</strong>
            <span><span class="badge" style="background:{{ $b['bg'] }};color:{{ $b['color'] }};">{{ $b['label'] }}</span></span>
        </div>
        <div class="info-row">
            <strong>تاریخ ثبت:</strong>
            <span>{{ jalali($request->created_at, 'Y/m/d H:i') }}</span>
        </div>
        <div class="info-row">
            <strong>IP:</strong>
            <span style="font-family:monospace;font-size:12px;color:#94a3b8;">{{ $request->ip_address ?? '—' }}</span>
        </div>

        <div style="margin-top:16px;">
            <strong style="color:#64748b;font-size:13px;">متن درخواست:</strong>
            <div class="desc">{{ $request->description }}</div>
        </div>

        @if($request->attachment)
            <div style="margin-top:16px;">
                <strong style="color:#64748b;font-size:13px;">فایل پیوست:</strong><br><br>
                <a href="{{ asset('storage/' . $request->attachment) }}" target="_blank" style="display:inline-block;padding:8px 16px;background:#f1f8e9;color:#2e7d32;text-decoration:none;border-radius:8px;border:1px solid #a5d6a7;">📎 دانلود فایل</a>
            </div>
        @endif
    </div>

    @if($request->admin_response)
        <div class="card">
            <h3>✍️ پاسخ ثبت‌شده</h3>
            <div class="desc">{{ $request->admin_response }}</div>
        </div>
    @endif

    <div class="card" style="background:#f8fafc;">
        <p style="margin:0;font-size:13px;color:#546e7a;line-height:2;">
            ℹ️ پاسخ به درخواست‌های روستایی توسط دهیاری همان روستا ثبت می‌شود.
            برای پیگیری یا ارجاع، با دهیار روستای
            <strong>{{ optional($request->village)->name ?? '—' }}</strong>
            تماس بگیرید.
        </p>
        <div style="margin-top:16px;">
            <a href="{{ route('admin.sections.all-requests', $section->id) }}" class="btn-cancel">← بازگشت به لیست</a>
        </div>
    </div>

</div>

</body>
</html>

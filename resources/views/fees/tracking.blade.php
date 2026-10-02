<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>پیگیری قبض | روستا نما</title>
<style>
    *{box-sizing:border-box}
    body{font-family:Tahoma,sans-serif;background:#f4f7fb;margin:0;padding:16px;color:#1e3a5f;line-height:1.8}
    .card{max-width:720px;margin:0 auto 16px;background:#fff;border-radius:14px;padding:22px;box-shadow:0 6px 24px rgba(30,58,95,.08)}
    h1{font-size:19px;color:#1565c0;margin:0 0 16px;text-align:center}
    label{display:block;margin:12px 0 6px;font-size:14px;font-weight:bold}
    input{width:100%;padding:11px 13px;border:1px solid #cfd8dc;border-radius:9px;font-family:inherit;font-size:14px}
    .btn{display:block;width:100%;margin-top:16px;padding:12px;background:#1565c0;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:bold;cursor:pointer}
    .err{background:#ffebee;color:#c62828;padding:10px 14px;border-radius:9px;margin-bottom:14px}
    .kv{display:grid;grid-template-columns:auto 1fr;gap:8px 14px;font-size:14px;margin:12px 0}
    .kv b{color:#37474f}
    .st{display:inline-block;padding:4px 11px;border-radius:20px;font-size:12.5px;font-weight:bold}
    .st-unpaid{background:#ffebee;color:#c62828}
    .st-pending_review{background:#fff8e1;color:#ef6c00}
    .st-paid{background:#e8f5e9;color:#2e7d32}
    .st-rejected{background:#eceff1;color:#455a64}
</style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
</head>
<body>
<div class="card">
    <h1>🔎 پیگیری قبض عوارض</h1>

    @if($error)
        <div class="err">{{ $error }}</div>
    @endif

    <form method="POST" action="{{ route('fees.tracking') }}">
        @csrf
        <label>کد پیگیری قبض</label>
        <input type="text" name="tracking_code" value="{{ old('tracking_code', request('tracking_code')) }}" required dir="ltr" placeholder="BN260930XXXXX">

        <label>شماره موبایل مؤدی</label>
        <input type="text" name="mobile" value="{{ old('mobile', request('mobile')) }}" required dir="ltr" placeholder="09xxxxxxxxx">

        <button class="btn" type="submit">پیگیری</button>
    </form>
</div>

@if($bill)
<div class="card">
    <h2 style="font-size:16px;color:#1565c0;margin:0 0 12px">وضعیت قبض</h2>
    <div class="kv">
        <b>کد پیگیری:</b><span dir="ltr" style="font-family:monospace">{{ $bill->bill_number }}</span>
        <b>مؤدی:</b><span>{{ $bill->taxpayer_name }}</span>
        <b>نوع عوارض:</b><span>{{ $bill->fee_type_name }}</span>
        <b>مبلغ:</b><span style="font-family:monospace">{{ number_format($bill->amount_rial) }} ریال</span>
        <b>وضعیت:</b><span class="st st-{{ $bill->status }}">{{ \App\Models\Bill::statusLabel($bill->status) }}</span>
    </div>
    <a href="{{ route('fees.bill.show', $bill->bill_number) }}" style="display:inline-block;margin-top:12px;padding:10px 18px;background:#1565c0;color:#fff;border-radius:9px;text-decoration:none">مشاهده جزئیات کامل</a>
</div>
@endif
</body>
</html>

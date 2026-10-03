<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'مدیریت عوارض') | روستا نما</title>
<style>
*{box-sizing:border-box}
body{margin:0;font-family:Tahoma,Arial,sans-serif;background:#eef2f7;color:#263238;line-height:1.7}
.header{background:linear-gradient(135deg,#1e5b9c,#2470b8);color:#fff;padding:14px 22px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;box-shadow:0 3px 12px rgba(30,91,156,.25)}
.header h1{margin:0;font-size:17px}
.header a{color:#fff;text-decoration:none;background:rgba(255,255,255,.15);padding:7px 13px;border-radius:8px;font-size:13px}
.nav{background:#fff;border-bottom:1px solid #e0e0e0;padding:0 18px;display:flex;gap:4px;overflow-x:auto}
.nav a{padding:12px 14px;color:#37474f;text-decoration:none;font-size:13.5px;border-bottom:3px solid transparent;white-space:nowrap}
.nav a:hover{background:#f5f9ff}
.nav a.active{color:#1565c0;border-bottom-color:#1565c0;font-weight:bold}
.container{max-width:1200px;margin:20px auto;padding:0 16px}
.card{background:#fff;border-radius:12px;padding:18px;box-shadow:0 4px 16px rgba(30,58,95,.06);margin-bottom:16px}
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:16px}
.stat{background:#fff;border-radius:12px;padding:16px;box-shadow:0 4px 16px rgba(30,58,95,.06);text-align:center}
.stat .num{font-size:22px;font-weight:bold;color:#1565c0;font-family:monospace;direction:ltr;display:block;margin:4px 0}
.stat .lbl{font-size:12.5px;color:#607d8b}
table{width:100%;border-collapse:collapse;font-size:13.5px}
th,td{padding:10px 9px;border-bottom:1px solid #eceff1;text-align:right}
th{background:#f5f9ff;color:#1565c0;font-weight:bold}
tr:hover{background:#fafcff}
.btn{display:inline-block;padding:6px 12px;border-radius:7px;text-decoration:none;font-size:12.5px;border:none;cursor:pointer;font-family:inherit}
.btn-blue{background:#1565c0;color:#fff}
.btn-green{background:#2e7d32;color:#fff}
.btn-red{background:#c62828;color:#fff}
.btn-gray{background:#eceff1;color:#37474f}
.form-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px;margin-bottom:12px}
input,select,textarea{width:100%;padding:8px 10px;border:1px solid #cfd8dc;border-radius:7px;font-family:inherit;font-size:13px;background:#fbfdff}
input:focus,select:focus,textarea:focus{outline:none;border-color:#1565c0;background:#fff}
label{font-size:12.5px;color:#546e7a;display:block;margin-bottom:3px}
.alert{padding:11px 14px;border-radius:9px;margin-bottom:14px;font-size:13.5px}
.alert-success{background:#e8f5e9;color:#2e7d32;border-right:4px solid #2e7d32}
.alert-error{background:#ffebee;color:#c62828;border-right:4px solid #c62828}
.pill{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11.5px;font-weight:bold}
.pill-unpaid{background:#ffebee;color:#c62828}
.pill-pending_review{background:#fff8e1;color:#ef6c00}
.pill-paid{background:#e8f5e9;color:#2e7d32}
.pill-rejected{background:#eceff1;color:#455a64}
.pill-approved{background:#e8f5e9;color:#2e7d32}
.pill-pending{background:#fff8e1;color:#ef6c00}
.muted{color:#607d8b;font-size:12px}
.mono{font-family:monospace;direction:ltr;display:inline-block}
@media(max-width:600px){.nav{font-size:12px}th,td{padding:8px 6px;font-size:12.5px}}
</style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
<link rel="stylesheet" href="{{ asset('css/green-theme.css') }}">
</head>
<body>
<div class="header">
    <h1>💰 مدیریت عوارض و بهای خدمات</h1>
    <div>
        <a href="{{ route('admin.fees.dashboard') }}">داشبورد</a>
        <a href="/admin">بازگشت به پنل اصلی</a>
    </div>
</div>

<div class="nav">
    <a href="{{ route('admin.fees.dashboard') }}" class="@if(request()->routeIs('admin.fees.dashboard')) active @endif">📊 داشبورد</a>
    <a href="{{ route('admin.fees.payments.index') }}" class="@if(request()->routeIs('admin.fees.payments.*')) active @endif">
        💳 پرداخت‌ها
        @php $pending = \App\Models\Payment::where('status','pending_review')->count(); @endphp
        @if($pending > 0) <span style="background:#c62828;color:#fff;padding:1px 7px;border-radius:10px;font-size:11px">{{ $pending }}</span> @endif
    </a>
    <a href="{{ route('admin.fees.bills.index') }}" class="@if(request()->routeIs('admin.fees.bills.*')) active @endif">🧾 قبوض</a>
    <a href="{{ route('admin.fees.tariffs.index') }}" class="@if(request()->routeIs('admin.fees.tariffs.*')) active @endif">📋 تعرفه‌ها</a>
    <a href="{{ route('admin.fees.accounts.index') }}" class="@if(request()->routeIs('admin.fees.accounts.*')) active @endif">🏦 حساب‌ها</a>
    <a href="{{ route('admin.fees.reports.index') }}" class="@if(request()->routeIs('admin.fees.reports.*')) active @endif">📈 گزارش‌ها</a>
    <a href="{{ route('admin.passwords.index') }}" class="@if(request()->routeIs('admin.passwords.*')) active @endif">🔑 رمزها</a>
</div>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success">✅ {{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
        </div>
    @endif

    @yield('content')
</div>
</body>
</html>

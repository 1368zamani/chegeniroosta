<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'تعرفه سالانه') | پنل ادمین</title>
<link rel="icon" type="image/png" href="{{ url('/favicon.png') }}">
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-light-theme.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-master.css') }}?v=20261005">
<style>
* { box-sizing: border-box; }
body { margin: 0; font-family: 'Vazirmatn', Tahoma, sans-serif; background: #f5f9ff; color: #1565c0; }
.topbar { background: linear-gradient(135deg, #0d3b2e 0%, #065f46 50%, #0d3b2e 100%); color: #fff; padding: 14px 22px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; border-bottom: 3px solid #ffd54f; }
.topbar h1 { margin: 0; font-size: 17px; }
.topbar a { color: #fff; text-decoration: none; background: rgba(255,255,255,0.15); padding: 7px 13px; border-radius: 8px; font-size: 13px; margin-right: 6px; font-weight: 700; }
.wrap { display: flex; gap: 16px; align-items: flex-start; width: min(1400px, 96%); margin: 16px auto; }
.content { flex: 1; min-width: 0; }
.card { background: #fff; border-radius: 14px; padding: 20px; margin-bottom: 16px; border-top: 4px solid #1565c0; box-shadow: 0 4px 15px rgba(13,46,79,0.06); }
.card h3 { margin: 0 0 12px; font-size: 15px; color: #0d47a1; border-bottom: 2px solid #e3f2fd; padding-bottom: 8px; }
table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
th, td { padding: 10px 8px; border-bottom: 1px solid #eceff1; text-align: right; }
th { background: #e3f2fd; color: #0d47a1; font-weight: 800; }
tr:hover { background: #f5f9ff; }
.btn { display: inline-block; padding: 8px 16px; border-radius: 8px; text-decoration: none; border: none; cursor: pointer; font-family: inherit; font-weight: 700; font-size: 13px; }
.btn-blue { background: #1565c0; color: #fff; }
.btn-green { background: #10b981; color: #fff; }
.btn-red { background: #D32F2F; color: #fff; }
.btn-gray { background: #607d8b; color: #fff; }
.btn-yellow { background: #f9a825; color: #fff; }
.alert { padding: 12px 16px; border-radius: 10px; margin-bottom: 14px; font-weight: 700; }
.alert-success { background: #e8f5e9; color: #2e7d32; border-right: 4px solid #2e7d32; }
.alert-error { background: #ffebee; color: #c62828; border-right: 4px solid #c62828; }
.alert-info { background: #e3f2fd; color: #0d47a1; border-right: 4px solid #1565c0; }
.pill { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 800; color: #fff; }
.pill-draft { background: #607d8b; }
.pill-proposed { background: #f9a825; }
.pill-village_council_approved { background: #0288d1; }
.pill-district_council_approved { background: #1976d2; }
.pill-finalized { background: #6a1b9a; }
.pill-published { background: #2e7d32; }
input, select, textarea { padding: 10px 14px; border: 1.5px solid #cfd8dc; border-radius: 8px; font-family: inherit; width: 100%; }
input:focus { outline: none; border-color: #1565c0; box-shadow: 0 0 0 3px rgba(21,101,192,0.12); }
label { display: block; font-size: 12.5px; color: #37474f; font-weight: 700; margin-bottom: 5px; }
.amount { font-family: monospace; direction: ltr; display: inline-block; }
@media(max-width: 900px){ .wrap { display: block; } th, td { padding: 7px 5px; font-size: 12.5px; } }
</style>
</head>
<body>

<div class="topbar">
    <h1>📋 تعرفه عوارض و بهای خدمات</h1>
    <div>
        <a href="{{ route('admin.tariffs.index') }}">لیست مجموعه‌ها</a>
        <a href="/admin">بازگشت</a>
    </div>
</div>

<div class="wrap">
    @include('partials.admin.sidebar')

    <div class="content">
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
</div>

</body>
</html>

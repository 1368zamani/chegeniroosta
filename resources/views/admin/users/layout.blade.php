<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #fff7e9; color: #2b2119; }
        .header { background: #c94a2b; color: white; padding: 18px 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header a { color: white; text-decoration: none; font-size: 13px; padding: 6px 12px; background: rgba(255,255,255,0.15); border-radius: 6px; }
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        .success { background: #fbdcd0; color: #c94a2b; padding: 12px 16px; border-radius: 8px; margin-bottom: 18px; font-size: 13px; }
        .error { background: #f6d5d2; color: #b3261e; padding: 12px 16px; border-radius: 8px; margin-bottom: 18px; font-size: 13px; }
        .card { background: white; border-radius: 12px; padding: 28px; box-shadow: 0 6px 20px rgba(0,0,0,0.06); margin-bottom: 20px; }
        .btn { display: inline-block; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-family: inherit; font-size: 13px; cursor: pointer; border: none; }
        .btn-primary { background: #c94a2b; color: white; }
        .btn-primary:hover { background: #8e2f18; }
        .btn-secondary { background: #e6d8c3; color: #2b2119; }
        .btn-secondary:hover { background: #e6d8c3; }
        .btn-danger { background: #b3261e; color: white; }
        .btn-danger:hover { background: #b3261e; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .field { margin-bottom: 18px; }
        .field label { display: block; margin-bottom: 6px; font-size: 13px; color: #5a4a3c; font-weight: 600; }
        .field input, .field select, .field textarea { width: 100%; padding: 11px 14px; border: 1px solid #e6d8c3; border-radius: 8px; font-family: inherit; font-size: 14px; outline: none; background: white; }
        .field input:focus, .field select:focus { border-color: #c94a2b; }
        .hint { font-size: 12px; color: #8a7a6a; margin-top: 5px; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media (max-width: 700px) { .row { grid-template-columns: 1fr; } }
        table { width: 100%; background: white; border-radius: 12px; overflow: hidden; border-collapse: collapse; }
        th, td { padding: 12px 14px; text-align: right; border-bottom: 1px solid #f5efe4; font-size: 13px; }
        th { background: #fffdf9; color: #5a4a3c; font-weight: 600; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: #fffdf9; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; display: inline-block; }
        .badge-super { background: #ffe0b2; color: #a8730f; }
        .badge-section { background: #fbdcd0; color: #c94a2b; }
        .badge-village { background: #fdeee7; color: #c94a2b; }
    </style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-light-theme.css') }}">
<link rel="stylesheet" href="/css/admin-master.css?v=2">
<link rel="stylesheet" href="{{ asset('css/admin-final-fix.css') }}?v=10">
<link rel="stylesheet" href="/css/admin-header-fix.css?v=1"><link rel="stylesheet" href="{{ asset('css/admin-utils.css') }}?v=1">
<link rel="stylesheet" href="{{ asset('css/admin-v3.css') }}?v=1">
</head>
<body>

<div class="header">
    <h1>@yield('header')</h1>
    <div>
        <a href="{{ route('admin.users.index') }}">لیست کاربران</a>
        <a href="/admin">داشبورد</a>
    </div>
</div>

<div class="container">
    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="error">
            @foreach($errors->all() as $err)
                <div>• {{ $err }}</div>
            @endforeach
        </div>
    @endif

    @yield('content')
</div>

</body>
</html>


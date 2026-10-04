<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #f5f7fa; color: #263238; }
        .header { background: #1565c0; color: white; padding: 18px 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header a { color: white; text-decoration: none; font-size: 13px; padding: 6px 12px; background: rgba(255,255,255,0.15); border-radius: 6px; }
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        .success { background: #c8e6c9; color: #2e7d32; padding: 12px 16px; border-radius: 8px; margin-bottom: 18px; font-size: 13px; }
        .error { background: #ffcdd2; color: #c62828; padding: 12px 16px; border-radius: 8px; margin-bottom: 18px; font-size: 13px; }
        .card { background: white; border-radius: 12px; padding: 28px; box-shadow: 0 6px 20px rgba(0,0,0,0.06); margin-bottom: 20px; }
        .btn { display: inline-block; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-family: inherit; font-size: 13px; cursor: pointer; border: none; }
        .btn-primary { background: #1565c0; color: white; }
        .btn-primary:hover { background: #0d47a1; }
        .btn-secondary { background: #cfd8dc; color: #37474f; }
        .btn-secondary:hover { background: #b0bec5; }
        .btn-danger { background: #e53935; color: white; }
        .btn-danger:hover { background: #c62828; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .field { margin-bottom: 18px; }
        .field label { display: block; margin-bottom: 6px; font-size: 13px; color: #546e7a; font-weight: 600; }
        .field input, .field select, .field textarea { width: 100%; padding: 11px 14px; border: 1px solid #cfd8dc; border-radius: 8px; font-family: inherit; font-size: 14px; outline: none; background: white; }
        .field input:focus, .field select:focus { border-color: #1565c0; }
        .hint { font-size: 12px; color: #90a4ae; margin-top: 5px; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media (max-width: 700px) { .row { grid-template-columns: 1fr; } }
        table { width: 100%; background: white; border-radius: 12px; overflow: hidden; border-collapse: collapse; }
        th, td { padding: 12px 14px; text-align: right; border-bottom: 1px solid #eceff1; font-size: 13px; }
        th { background: #f8fafc; color: #546e7a; font-weight: 600; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: #f8fafc; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; display: inline-block; }
        .badge-super { background: #ffe0b2; color: #e65100; }
        .badge-section { background: #c8e6c9; color: #2e7d32; }
        .badge-village { background: #e3f2fd; color: #1565c0; }
    </style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-light-theme.css') }}">
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

<link rel="stylesheet" href="{{ asset('css/admin-master.css') }}?v=1791127161">

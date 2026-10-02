<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title')</title>
<style>
* { box-sizing: border-box; }
body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #f5f7fa; color: #263238; }
.header { background: linear-gradient(135deg, #1565c0, #0d47a1); color: white; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
.header h1 { margin: 0; font-size: 19px; }
.header a { color: white; text-decoration: none; padding: 8px 16px; background: rgba(255,255,255,0.15); border-radius: 8px; font-size: 13px; }
.container { max-width: 1200px; margin: auto; padding: 30px 20px; }
.success { background: #c8e6c9; color: #2e7d32; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; }
.error { background: #ffcdd2; color: #c62828; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; }
.btn { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-family: inherit; font-size: 13px; cursor: pointer; border: none; display: inline-block; }
.btn-primary { background: #1565c0; color: white; }
.btn-danger { background: #e53935; color: white; }
.btn-secondary { background: #cfd8dc; color: #37474f; }
.btn-sm { padding: 7px 14px; font-size: 12px; }
.gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 18px; }
.gallery-item { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 18px rgba(0,0,0,0.07); }
.gallery-item img { width: 100%; height: 180px; object-fit: cover; display: block; }
.gallery-caption { padding: 12px 14px; font-size: 13px; color: #546e7a; }
.gallery-actions { padding: 10px 14px; border-top: 1px solid #eceff1; }
.empty { background: white; border-radius: 12px; padding: 60px 20px; text-align: center; color: #90a4ae; }
.empty .icon { font-size: 60px; opacity: .4; margin-bottom: 20px; }
.field { margin-bottom: 20px; }
.field label { display: block; margin-bottom: 8px; font-size: 13px; color: #546e7a; font-weight: 600; }
.field input { width: 100%; padding: 12px 14px; border: 1px solid #cfd8dc; border-radius: 8px; font-family: inherit; font-size: 14px; outline: none; }
.hint { font-size: 12px; color: #90a4ae; margin-top: 6px; }
.card { background: white; border-radius: 12px; padding: 30px; box-shadow: 0 6px 20px rgba(0,0,0,0.06); }
</style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
</head>
<body>
<div class="header">
    <h1>@yield('header')</h1>
    <div class="nav">
        <a href="{{ route('admin.sections.gallery.index', $section->id) }}">گالری</a>
        <a href="{{ route('admin.sections.villages', $section->id) }}">روستاها</a>
        <a href="{{ route('admin.dashboard') }}">داشبورد</a>
    </div>
</div>
<div class="container">
    @if(session('success'))<div class="success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="error">@foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach</div>@endif
    @yield('content')
</div>
</body>
</html>

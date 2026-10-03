<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title') | پنل {{ $village->name ?? '' }}</title>
<style>
* { box-sizing: border-box; }
body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #eef2f7; color: #263238; }

.header {
    background: linear-gradient(135deg, #0d2e4f, #1565c0);
    color: white;
    padding: 18px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    box-shadow: 0 4px 15px rgba(13,46,79,0.15);
}
.header h1 { margin: 0; font-size: 18px; display: flex; align-items: center; gap: 8px; }
.header .user-info { font-size: 12.5px; opacity: .9; }
.header .top-actions { display: flex; gap: 8px; flex-wrap: wrap; }
.header .top-actions a {
    color: white;
    text-decoration: none;
    padding: 7px 14px;
    background: rgba(255,255,255,0.15);
    border-radius: 8px;
    font-size: 12.5px;
}
.header .top-actions a:hover { background: rgba(255,255,255,0.25); }

.layout { display: flex; min-height: calc(100vh - 70px); }

.sidebar {
    width: 240px;
    background: white;
    border-left: 1px solid #e3f2fd;
    padding: 20px 0;
    box-shadow: 2px 0 10px rgba(0,0,0,0.03);
    flex-shrink: 0;
    overflow-y: auto;
}
.sidebar-menu { list-style: none; margin: 0; padding: 0; }
.sidebar-menu li { margin: 0; }
.sidebar-menu a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 22px;
    color: #37474f;
    text-decoration: none;
    font-size: 13.5px;
    border-right: 3px solid transparent;
    transition: all .2s;
}
.sidebar-menu a:hover {
    background: #f0f7ff;
    color: #1565c0;
    border-right-color: #1565c0;
}
.sidebar-menu a.active {
    background: #e3f2fd;
    color: #0d2e4f;
    font-weight: 700;
    border-right-color: #0d2e4f;
}

.content {
    flex: 1;
    padding: 24px;
    overflow-x: auto;
}
.page-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 22px;
    flex-wrap: wrap;
    gap: 12px;
}
.page-title h2 {
    margin: 0;
    color: #0d2e4f;
    font-size: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.success { background: #c8e6c9; color: #2e7d32; padding: 12px 18px; border-radius: 10px; margin-bottom: 18px; font-size: 13.5px; }
.error { background: #ffcdd2; color: #c62828; padding: 12px 18px; border-radius: 10px; margin-bottom: 18px; font-size: 13.5px; }

.btn {
    display: inline-block;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-family: inherit;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: all .2s;
}
.btn-primary { background: #1565c0; color: white; }
.btn-primary:hover { background: #0d47a1; }
.btn-success { background: #2e7d32; color: white; }
.btn-success:hover { background: #1b5e20; }
.btn-danger { background: #c62828; color: white; }
.btn-danger:hover { background: #b71c1c; }
.btn-warning { background: #f9a825; color: white; }
.btn-warning:hover { background: #f57f17; }
.btn-secondary { background: #cfd8dc; color: #37474f; }
.btn-secondary:hover { background: #b0bec5; }
.btn-sm { padding: 6px 12px; font-size: 12px; }

.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    padding: 24px;
    margin-bottom: 20px;
}
.card h3 {
    margin: 0 0 16px;
    color: #0d2e4f;
    font-size: 16px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e3f2fd;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 14px;
    margin-bottom: 22px;
}
.stat-card {
    background: white;
    border-radius: 12px;
    padding: 18px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    border-top: 4px solid #1565c0;
    text-decoration: none;
    color: inherit;
    transition: transform .2s;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(21,101,192,0.12); }
.stat-card .icon { font-size: 30px; display: block; margin-bottom: 6px; }
.stat-card .num { font-size: 24px; font-weight: 800; color: #0d2e4f; display: block; line-height: 1.2; }
.stat-card .label { font-size: 12px; color: #78909c; margin-top: 4px; display: block; font-weight: 600; }

table {
    width: 100%;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    border-collapse: collapse;
}
th, td {
    padding: 12px 14px;
    text-align: right;
    border-bottom: 1px solid #eceff1;
    font-size: 13px;
}
th { background: #f8fafc; color: #546e7a; font-weight: 700; }
tr:last-child td { border-bottom: none; }
tr:hover { background: #f8fafc; }

.field { margin-bottom: 18px; }
.field label {
    display: block;
    margin-bottom: 6px;
    font-size: 13px;
    color: #546e7a;
    font-weight: 600;
}
.field input[type="text"],
.field input[type="email"],
.field input[type="number"],
.field input[type="date"],
.field input[type="file"],
.field select,
.field textarea {
    width: 100%;
    padding: 11px 14px;
    border: 1px solid #cfd8dc;
    border-radius: 8px;
    font-family: inherit;
    font-size: 14px;
    outline: none;
    background: white;
}
.field input:focus, .field select:focus, .field textarea:focus {
    border-color: #1565c0;
    box-shadow: 0 0 0 3px rgba(21,101,192,0.1);
}
.field textarea { min-height: 140px; resize: vertical; }
.field .hint { font-size: 11.5px; color: #90a4ae; margin-top: 5px; }

.checkbox-field {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    background: #f8fafc;
    border-radius: 8px;
    margin-bottom: 10px;
    cursor: pointer;
}
.checkbox-field:hover { background: #f0f7ff; }
.checkbox-field input[type="checkbox"] { width: 18px; height: 18px; }
.checkbox-field label { margin: 0; cursor: pointer; font-weight: 600; color: #37474f; }

.row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

.badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 700;
}
.badge-active { background: #c8e6c9; color: #2e7d32; }
.badge-inactive { background: #ffcdd2; color: #c62828; }
.badge-warning { background: #ffe0b2; color: #e65100; }

.empty-box {
    background: white;
    border-radius: 12px;
    padding: 50px 20px;
    text-align: center;
    color: #90a4ae;
    font-size: 14px;
}
.empty-box .icon { font-size: 50px; opacity: .5; margin-bottom: 14px; display: block; }

.thumb-sm { width: 50px; height: 50px; border-radius: 8px; object-fit: cover; }
.thumb-empty { width: 50px; height: 50px; border-radius: 8px; background: #f0f3f7; display: inline-flex; align-items: center; justify-content: center; color: #90a4ae; }

@media (max-width: 900px) {
    .layout { flex-direction: column; }
    .sidebar { width: 100%; padding: 12px 0; }
    .sidebar-menu { display: flex; overflow-x: auto; }
    .sidebar-menu li { flex-shrink: 0; }
    .sidebar-menu a { padding: 10px 16px; border-right: none; border-bottom: 3px solid transparent; }
    .sidebar-menu a.active { border-bottom-color: #0d2e4f; border-right: none; }
    .content { padding: 14px; }
    .row-2 { grid-template-columns: 1fr; }
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
<link rel="stylesheet" href="{{ asset('css/green-theme.css') }}">
</head>
<body>

<div class="header">
    <div>
        <h1>🏘 پنل {{ $village->name }}</h1>
        <div class="user-info">{{ auth()->user()->name ?? 'کاربر' }} — {{ auth()->user()->email ?? '' }}</div>
    </div>
    <div class="top-actions">
        <a href="{{ request()->getScheme() }}://{{ config('app.base_domain') }}/village/{{ $village->slug }}" target="_blank">🌐 مشاهده سایت</a>
        <a href="{{ route('admin.dashboard') }}">🏠 داشبورد کل</a>
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" style="color:white; background:rgba(255,255,255,0.15); border:none; padding:7px 14px; border-radius:8px; cursor:pointer; font-family:inherit; font-size:12.5px;">خروج</button>
        </form>
    </div>
</div>

<div class="layout">
    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="{{ route('admin.village.dashboard', $village->id) }}" class="{{ request()->routeIs('admin.village.dashboard') ? 'active' : '' }}">📊 داشبورد</a></li>
            <li>
                <a href="{{ route('admin.village.requests.index', $village->id) }}">
                    📩 <span>درخواست‌های مردمی</span>
                </a>
            </li>
            <li><a href="{{ route('admin.village.settings.index', $village->id) }}" class="{{ request()->routeIs('admin.village.settings.*') ? 'active' : '' }}">⚙️ تنظیمات نمایش</a></li>
            <li><a href="{{ route('admin.village.news.index', $village->id) }}" class="{{ request()->routeIs('admin.village.news.*') ? 'active' : '' }}">📰 اخبار</a></li>
            <li><a href="{{ route('admin.village.projects.index', $village->id) }}" class="{{ request()->routeIs('admin.village.projects.*') ? 'active' : '' }}">🏗 پروژه‌ها</a></li>
            <li><a href="{{ route('admin.village.events.index', $village->id) }}" class="{{ request()->routeIs('admin.village.events.*') ? 'active' : '' }}">📅 رویدادها</a></li>
            <li><a href="{{ route('admin.village.facilities.index', $village->id) }}" class="{{ request()->routeIs('admin.village.facilities.*') ? 'active' : '' }}">🏛 امکانات</a></li>
            <li><a href="{{ route('admin.village.council.index', $village->id) }}" class="{{ request()->routeIs('admin.village.council.*') ? 'active' : '' }}">👥 شورا</a></li>
            <li><a href="{{ route('admin.village.resolutions.index', $village->id) }}" class="{{ request()->routeIs('admin.village.resolutions.*') ? 'active' : '' }}">📋 مصوبات شورا</a></li>
            <li><a href="{{ route('admin.village.attractions.index', $village->id) }}" class="{{ request()->routeIs('admin.village.attractions.*') ? 'active' : '' }}">🏞 جاذبه‌ها</a></li>
            <li><a href="{{ route('admin.village.images.index', $village->id) }}">🖼 گالری تصاویر</a></li>
        </ul>
    </aside>

    <main class="content">
        @if(session('success'))
            <div class="success">✅ {{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="error">
                @foreach($errors->all() as $err)
                    <div>❌ {{ $err }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </main>
</div>

</body>
</html>

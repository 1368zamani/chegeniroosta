<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title') | پنل مدیریت</title>
<style>
* { box-sizing: border-box; }
body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #eef2f7; color: #263238; }

.header {
    background: linear-gradient(135deg, #2e7d32, #388e3c);
    color: white;
    padding: 18px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    box-shadow: 0 4px 15px rgba(30,91,156,0.2);
}
.header h1 { margin: 0; font-size: 18px; }
.header .top-actions { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
.header .top-actions a, .header .top-actions button {
    color: white;
    text-decoration: none;
    padding: 7px 14px;
    background: rgba(255,255,255,0.15);
    border: none;
    border-radius: 8px;
    font-size: 12.5px;
    cursor: pointer;
    font-family: inherit;
}
.header .top-actions a:hover, .header .top-actions button:hover { background: rgba(255,255,255,0.25); }

.layout { display: flex; min-height: calc(100vh - 70px); }

.sidebar {
    width: 250px;
    background: white;
    border-left: 1px solid #e8f5e9;
    padding: 20px 0;
    box-shadow: 2px 0 10px rgba(0,0,0,0.03);
    flex-shrink: 0;
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
.sidebar-menu a:hover { background: #f1f8e9; color: #2e7d32; border-right-color: #2e7d32; }
.sidebar-menu a.active { background: #e8f5e9; color: #2e7d32; font-weight: 700; border-right-color: #2e7d32; }

.content { flex: 1; padding: 24px; overflow-x: auto; }

.page-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 22px;
    flex-wrap: wrap;
    gap: 12px;
}
.page-title h2 { margin: 0; color: #2e7d32; font-size: 20px; }

.success { background: #c8e6c9; color: #2e7d32; padding: 12px 18px; border-radius: 10px; margin-bottom: 18px; font-size: 13.5px; }

.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    padding: 24px;
    margin-bottom: 20px;
}
.card h3 {
    margin: 0 0 16px;
    color: #2e7d32;
    font-size: 16px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e8f5e9;
}

table { width: 100%; background: white; border-radius: 12px; overflow: hidden; border-collapse: collapse; }
th, td { padding: 12px 14px; text-align: right; border-bottom: 1px solid #eceff1; font-size: 13px; }
th { background: #f8fafc; color: #546e7a; font-weight: 700; }
tr:last-child td { border-bottom: none; }
tr:hover { background: #f8fafc; }

.field { margin-bottom: 18px; }
.field label { display: block; margin-bottom: 6px; font-size: 13px; color: #546e7a; font-weight: 600; }
.field input, .field select, .field textarea {
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
    border-color: #2e7d32;
    box-shadow: 0 0 0 3px rgba(30,91,156,0.1);
}
.field textarea { min-height: 100px; resize: vertical; }

.btn { display: inline-block; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-family: inherit; font-size: 13px; font-weight: 600; cursor: pointer; border: none; transition: all .2s; }
.btn-primary { background: #2e7d32; color: white; }
.btn-primary:hover { background: #1b5e20; }
.btn-success { background: #2e7d32; color: white; }
.btn-secondary { background: #cfd8dc; color: #37474f; }
.btn-sm { padding: 6px 12px; font-size: 12px; }

.badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; }
.badge-active { background: #c8e6c9; color: #2e7d32; }
.badge-inactive { background: #ffcdd2; color: #c62828; }

.empty-box { background: white; border-radius: 12px; padding: 50px 20px; text-align: center; color: #90a4ae; font-size: 14px; }
.empty-box .icon { font-size: 50px; opacity: .5; margin-bottom: 14px; display: block; }

@media (max-width: 900px) {
    .layout { flex-direction: column; }
    .sidebar { width: 100%; padding: 12px 0; }
    .sidebar-menu { display: flex; overflow-x: auto; }
    .sidebar-menu li { flex-shrink: 0; }
    .sidebar-menu a { padding: 10px 16px; border-right: none; border-bottom: 3px solid transparent; }
}
</style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-light-theme.css') }}">
<link rel="stylesheet" href="/css/admin-master.css?v=2">
<link rel="stylesheet" href="{{ asset('css/admin-final-fix.css') }}?v=10">
<link rel="stylesheet" href="/css/admin-header-fix.css?v=1"></head>
<body>

<header class="header">
    <h1>🏛 پنل مدیریت مسئولین شهرستان</h1>
    <div class="top-actions">
        <span style="font-size:12.5px;opacity:.9;">{{ auth()->user()->name }}</span>
        <a href="{{ route('admin.dashboard') }}">📊 داشبورد</a>
        <a href="{{ url('/') }}" target="_blank">🌐 سایت</a>
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit">🚪 خروج</button>
        </form>
    </div>
</header>

<div class="layout">
    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="{{ route('admin.dashboard') }}">📊 داشبورد</a></li>
            <li><a href="{{ route('admin.sections.index') }}">🏛 بخش‌ها</a></li>
            <li><a href="{{ route('admin.villages.index') }}">🏘 روستاها</a></li>
            <li><a href="{{ route('admin.users.index') }}">👥 کاربران</a></li>
            <li><a href="{{ route('admin.section-news.index') }}">📰 اخبار بخشداری</a></li>
            <li><a href="{{ route('admin.officials.index') }}" class="active">🏛 مسئولین شهرستان</a></li>
        </ul>
    </aside>

    <main class="content">
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>
</div>

</body>
</html>


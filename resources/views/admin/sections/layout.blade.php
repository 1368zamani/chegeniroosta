<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title') | پنل بخشداری {{ $section->name ?? '' }}</title>
<style>
* { box-sizing: border-box; }
body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #eef2f7; color: #263238; }

.header {
    background: linear-gradient(135deg, #1e5b9c, #2470b8);
    color: white;
    padding: 16px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    box-shadow: 0 4px 15px rgba(30,91,156,0.2);
}
.header h1 { margin: 0; font-size: 18px; display: flex; align-items: center; gap: 8px; }
.header .user-info { font-size: 12.5px; opacity: .9; }
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
.sidebar-menu a:hover { background: #f0f7ff; color: #1e5b9c; border-right-color: #1e5b9c; }
.sidebar-menu a.active { background: #e3f2fd; color: #1e5b9c; font-weight: 700; border-right-color: #1e5b9c; }
.sidebar-menu .menu-divider {
    height: 1px; background: #e3f2fd; margin: 10px 20px;
}
.sidebar-menu .menu-label {
    font-size: 11px;
    color: #94a3b8;
    padding: 8px 22px 4px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.content { flex: 1; padding: 24px; overflow-x: auto; }

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
    color: #1e5b9c;
    font-size: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.success { background: #c8e6c9; color: #2e7d32; padding: 12px 18px; border-radius: 10px; margin-bottom: 18px; font-size: 13.5px; }
.error { background: #ffcdd2; color: #c62828; padding: 12px 18px; border-radius: 10px; margin-bottom: 18px; font-size: 13.5px; }

.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    padding: 24px;
    margin-bottom: 20px;
}
.card h3 {
    margin: 0 0 16px;
    color: #1e5b9c;
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
    border-top: 4px solid #1e5b9c;
    text-decoration: none;
    color: inherit;
    transition: transform .2s;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(30,91,156,0.12); }
.stat-card .icon { font-size: 30px; display: block; margin-bottom: 6px; }
.stat-card .num { font-size: 24px; font-weight: 800; color: #1e5b9c; display: block; line-height: 1.2; }
.stat-card .label { font-size: 12px; color: #78909c; margin-top: 4px; display: block; font-weight: 600; }

table { width: 100%; background: white; border-radius: 12px; overflow: hidden; border-collapse: collapse; }
th, td { padding: 11px 13px; text-align: right; border-bottom: 1px solid #eceff1; font-size: 13px; }
th { background: #f8fafc; color: #546e7a; font-weight: 700; }
tr:last-child td { border-bottom: none; }
tr:hover { background: #f8fafc; }

.badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; }
.badge-active { background: #c8e6c9; color: #2e7d32; }
.badge-inactive { background: #ffcdd2; color: #c62828; }
.badge-submitted { background: #dbeafe; color: #1e40af; }
.badge-reviewing, .badge-assigned, .badge-processing { background: #fef3c7; color: #92400e; }
.badge-resolved { background: #d1fae5; color: #065f46; }
.badge-rejected { background: #fee2e2; color: #991b1b; }

.btn {
    display: inline-block;
    padding: 9px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-family: inherit;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: all .2s;
}
.btn-primary { background: #1e5b9c; color: white; }
.btn-primary:hover { background: #14497f; }
.btn-success { background: #2e7d32; color: white; }
.btn-success:hover { background: #1b5e20; }
.btn-secondary { background: #cfd8dc; color: #37474f; }
.btn-secondary:hover { background: #b0bec5; }
.btn-sm { padding: 6px 12px; font-size: 12px; }

.empty-box {
    background: white;
    border-radius: 12px;
    padding: 40px 20px;
    text-align: center;
    color: #90a4ae;
    font-size: 14px;
}

@media (max-width: 900px) {
    .layout { flex-direction: column; }
    .sidebar { width: 100%; padding: 12px 0; }
    .sidebar-menu { display: flex; overflow-x: auto; }
    .sidebar-menu li { flex-shrink: 0; }
    .sidebar-menu a { padding: 10px 16px; border-right: none; border-bottom: 3px solid transparent; }
    .sidebar-menu a.active { border-bottom-color: #1e5b9c; border-right: none; }
    .sidebar-menu .menu-divider, .sidebar-menu .menu-label { display: none; }
}
</style>
</head>
<body>

<header class="header">
    <h1>🏛 پنل بخشداری {{ $section->name ?? '' }}</h1>
    <div class="top-actions">
        <span class="user-info">{{ auth()->user()->name }}</span>
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
            <li><a href="{{ route('admin.sections.dashboard', $section->id) }}" class="{{ request()->routeIs('admin.sections.dashboard') ? 'active' : '' }}">📊 داشبورد</a></li>
            <li><a href="{{ route('admin.sections.edit', $section->id) }}" class="{{ request()->routeIs('admin.sections.edit') ? 'active' : '' }}">⚙️ تنظیمات بخش</a></li>
            <li><a href="{{ route('admin.sections.council.index', $section->id) }}" class="{{ request()->routeIs('admin.sections.council.*') ? 'active' : '' }}">👥 شورای بخش</a></li>

            <li class="menu-divider"></li>
            <li class="menu-label">مدیریت</li>

            <li><a href="{{ route('admin.sections.villages', $section->id) }}" class="{{ request()->routeIs('admin.sections.villages') ? 'active' : '' }}">🏘 روستاهای بخش</a></li>
            <li><a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">👥 کاربران بخش</a></li>
            <li><a href="{{ route('admin.sections.all-requests', $section->id) }}" class="{{ request()->routeIs('admin.sections.all-requests*') ? 'active' : '' }}">📬 همه درخواست‌ها</a></li>
            <li><a href="{{ route('admin.sections.requests.index', $section->id) }}" class="{{ request()->routeIs('admin.sections.requests.*') ? 'active' : '' }}">📩 درخواست‌های بخشداری</a></li>

            <li class="menu-divider"></li>
            <li class="menu-label">محتوا</li>

            <li><a href="{{ route('admin.section-news.index') }}" class="{{ request()->routeIs('admin.section-news.*') ? 'active' : '' }}">📰 اخبار بخشداری</a></li>
            <li><a href="{{ route('admin.sections.gallery.index', $section->id) }}" class="{{ request()->routeIs('admin.sections.gallery.*') ? 'active' : '' }}">🖼 گالری بخشداری</a></li>
        </ul>
    </aside>

    <main class="content">
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</div>

</body>
</html>

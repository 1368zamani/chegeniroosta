<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل مدیریت سامانه بخشداری</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #f5f7fa; color: #263238; }

        .header {
            background: linear-gradient(135deg, #1565c0, #0d47a1);
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header h1 { margin: 0; font-size: 20px; }
        .header .user-info { font-size: 13px; opacity: .9; }
        .header .nav { display: flex; gap: 10px; flex-wrap: wrap; }
        .header .nav a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            background: rgba(255,255,255,0.15);
            border-radius: 8px;
            font-size: 13px;
            transition: background .2s;
        }
        .header .nav a:hover { background: rgba(255,255,255,0.25); }

        .container { max-width: 1200px; margin: auto; padding: 30px 20px; }

        .success {
            background: #c8e6c9;
            color: #2e7d32;
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 24px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 14px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 5px 18px rgba(0,0,0,0.05);
            text-align: center;
        }

        .stat-card .num {
            font-size: 32px;
            font-weight: bold;
            color: #1565c0;
            display: block;
        }

        .stat-card .label {
            font-size: 13px;
            color: #78909c;
            margin-top: 6px;
            display: block;
        }

        .section-title {
            font-size: 17px;
            color: #37474f;
            margin: 0 0 18px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e3f2fd;
        }

        .sections-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .section-card {
            background: white;
            border-radius: 14px;
            padding: 26px 22px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.06);
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            border-top: 4px solid #1565c0;
            transition: all .25s;
        }

        .section-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 35px rgba(21,101,192,0.15);
        }

        .section-card .icon {
            width: 52px;
            height: 52px;
            background: #e3f2fd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 16px;
        }

        .section-card h3 {
            margin: 0 0 8px;
            color: #1565c0;
            font-size: 18px;
        }

        .section-card .count {
            color: #78909c;
            font-size: 13px;
            margin-bottom: 16px;
        }

        .section-card .enter {
            display: inline-block;
            background: #1565c0;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            align-self: flex-start;
        }

        .empty {
            text-align: center;
            padding: 60px 20px;
            color: #90a4ae;
            background: white;
            border-radius: 12px;
        }

        .empty .icon { font-size: 50px; margin-bottom: 16px; opacity: .5; }
    </style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
<link rel="stylesheet" href="{{ asset('css/green-theme.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-light-theme.css') }}">
</head>
<body>

<div class="header">
    <div>
        <h1>پنل مدیریت سامانه بخشداری</h1>
        <div class="user-info">
            خوش آمدی، {{ auth()->user()->name }}
            @if(auth()->user()->isSuperAdmin())
                (سوپر ادمین)
            @elseif(auth()->user()->isSectionAdmin())
                (ادمین بخش {{ auth()->user()->section->name ?? '' }})
            @elseif(auth()->user()->isVillageAdmin())
                (ادمین روستا {{ auth()->user()->village->name ?? '' }})
            @endif
        </div>
    </div>

    <div class="nav">
        @if(auth()->user()->isSuperAdmin())
            @if(auth()->user()->isSuperAdmin())<a href="{{ route('admin.settings.index') }}" style="background: rgba(255,213,79,0.3);">⚙️ تنظیمات</a>@endif
            <a href="{{ route('admin.users.index') }}">👥 کاربران</a>
            <li><a href="{{ route('admin.officials.index') }}" class="{{ request()->routeIs('admin.officials.*') ? 'active' : '' }}">🏛 مسئولین شهرستان</a></li>
            <li></li>
            <li></li>
            <li></li>
            <a href="{{ route('admin.sections.index') }}">📋 بخش‌ها</a>
            <a href="{{ route('admin.export.villages') }}" style="background: rgba(76,175,80,0.25);">📊 خروجی Excel</a>
        @endif
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isSectionAdmin())
            <a href="{{ route('admin.section-news.index') }}">📰 اخبار بخش‌ها</a>
        @endif
        <a href="/" target="_blank">🌐 سایت</a>
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" style="background:rgba(255,255,255,0.2); color:white; border:none; padding:8px 16px; border-radius:8px; cursor:pointer; font-family:inherit; font-size:13px;">
                خروج
            </button>
        </form>
    </div>
</div>

<div class="container">

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    @if(auth()->user()->isSuperAdmin())
        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));">
            <div class="stat-card">
                <span class="num">{{ $stats['total_sections'] }}</span>
                <span class="label">📁 بخش</span>
            </div>
            <div class="stat-card">
                <span class="num">{{ number_format($stats['total_villages']) }}</span>
                <span class="label">🏘 روستا</span>
            </div>
            <div class="stat-card">
                <span class="num">{{ number_format($stats['total_users']) }}</span>
                <span class="label">👥 کاربر</span>
            </div>
            <div class="stat-card">
                <span class="num">{{ number_format($stats['total_news'] ?? 0) }}</span>
                <span class="label">📰 خبر</span>
            </div>
            <div class="stat-card">
                <span class="num">{{ number_format($stats['total_population'] ?? 0) }}</span>
                <span class="label">🧑 کل جمعیت</span>
            </div>
            <div class="stat-card">
                <span class="num">{{ number_format($stats['total_households'] ?? 0) }}</span>
                <span class="label">👥 کل خانوار</span>
            </div>
        </div>
    @endif

    <h2 class="section-title">📁 بخش‌ها</h2>

    @if($sections->count() > 0)
        <div class="sections-grid">
            @foreach($sections as $section)
                <a href="{{ route('admin.sections.villages', $section->id) }}" class="section-card">
                    <div class="icon">📍</div>
                    <h3>{{ $section->name }}</h3>
                    <div class="count">{{ $section->villages_count }} روستا</div>
                    <div style="display:flex; gap:6px; flex-wrap:wrap;">
                        <span class="enter">مدیریت ←</span>
                        <a href="{{ route('admin.sections.gallery.index', $section->id) }}" onclick="event.stopPropagation();" style="text-decoration:none; background:#7b1fa2; color:white; padding:8px 12px; border-radius:8px; font-size:12px;">🖼 گالری</a>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="empty">
            <div class="icon">📭</div>
            <p>شما به هیچ بخشی دسترسی ندارید.</p>
        </div>
    @endif

</div>

</body>
</html>

<link rel="stylesheet" href="{{ asset('css/admin-master.css') }}?v=1791127161">

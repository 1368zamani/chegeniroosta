<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>پنل مدیریت {{ $section->name }}</title>
<style>
* { box-sizing: border-box; }
body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #f5f7fa; color: #263238; }
.header { background: linear-gradient(135deg, #1565c0, #0d47a1); color: white; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
.header h1 { margin: 0; font-size: 20px; }
.header .user { font-size: 13px; opacity: .9; }
.header .nav { display: flex; gap: 10px; flex-wrap: wrap; }
.header .nav a { color: white; text-decoration: none; padding: 8px 16px; background: rgba(255,255,255,0.15); border-radius: 8px; font-size: 13px; }
.header .nav a:hover { background: rgba(255,255,255,0.25); }
.container { max-width: 1200px; margin: auto; padding: 30px 20px; }
.stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 32px; }
.stat { background: white; padding: 22px; border-radius: 12px; box-shadow: 0 5px 18px rgba(0,0,0,0.05); text-align: center; }
.stat .num { font-size: 32px; font-weight: bold; color: #1565c0; display: block; }
.stat .label { font-size: 13px; color: #78909c; margin-top: 6px; display: block; }
.section-title { font-size: 17px; color: #37474f; margin: 0 0 18px; padding-bottom: 10px; border-bottom: 2px solid #e3f2fd; }
.actions-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px; }
.action-card { background: white; border-radius: 12px; padding: 24px; text-decoration: none; color: inherit; box-shadow: 0 5px 18px rgba(0,0,0,0.05); border-top: 4px solid #1565c0; transition: all .2s; }
.action-card:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(21,101,192,0.15); }
.action-card .icon { font-size: 32px; margin-bottom: 12px; }
.action-card h3 { margin: 0 0 8px; color: #1565c0; font-size: 16px; }
.action-card p { margin: 0; color: #78909c; font-size: 13px; }
</style>
</head>
<body>
<div class="header">
    <div>
        <h1>{{ $section->name }}</h1>
        <div class="user">خوش آمدی، {{ auth()->user()->name }}</div>
    </div>
    <div class="nav">
        <a href="{{ route('admin.sections.villages', $section->id) }}">🏘 روستاها</a>
        <a href="{{ route('admin.sections.gallery.index', $section->id) }}">🖼 گالری</a>
        <a href="{{ route('admin.section-news.index') }}">📰 اخبار</a>
        <a href="{{ route('admin.sections.edit', $section->id) }}">⚙️ تنظیمات بخش</a>
        <a href="{{ request()->getScheme() }}://{{ $section->subdomain }}.{{ config('app.base_domain') }}" target="_blank">🌐 سایت بخش</a>
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">@csrf<button type="submit" style="background:rgba(255,255,255,0.2); color:white; border:none; padding:8px 16px; border-radius:8px; cursor:pointer; font-family:inherit; font-size:13px;">خروج</button></form>
    </div>
</div>
<div class="container">
    <div class="stats">
        <div class="stat">
            <span class="num">{{ $section->villages()->count() }}</span>
            <span class="label">🏘 روستا</span>
        </div>
        <div class="stat">
            <span class="num">{{ number_format($section->villages()->sum('population')) }}</span>
            <span class="label">🧑 جمعیت</span>
        </div>
        <div class="stat">
            <span class="num">{{ number_format($section->villages()->sum('households')) }}</span>
            <span class="label">👥 خانوار</span>
        </div>
        <div class="stat">
            <span class="num">{{ $section->sectionNews()->count() }}</span>
            <span class="label">📰 خبر</span>
        </div>
        <div class="stat">
            <span class="num">{{ $section->galleryImages()->count() }}</span>
            <span class="label">🖼 تصویر</span>
        </div>
    </div>

    <h2 class="section-title">🚀 دسترسی سریع</h2>
    <div class="actions-grid">
        <a href="{{ route('admin.sections.villages', $section->id) }}" class="action-card">
            <div class="icon">🏘</div>
            <h3>مدیریت روستاها</h3>
            <p>افزودن، ویرایش، حذف روستاهای بخش</p>
        </a>
        <a href="{{ route('admin.sections.gallery.index', $section->id) }}" class="action-card">
            <div class="icon">🖼</div>
            <h3>گالری تصاویر</h3>
            <p>آپلود و مدیریت تصاویر بخش</p>
        </a>
        <a href="{{ route('admin.section-news.index') }}" class="action-card">
            <div class="icon">📰</div>
            <h3>اخبار و اطلاعیه</h3>
            <p>انتشار و مدیریت خبرها</p>
        </a>
        <a href="{{ route('admin.sections.edit', $section->id) }}" class="action-card">
            <div class="icon">⚙️</div>
            <h3>تنظیمات بخش</h3>
            <p>ویرایش نام، بخشدار، لوگو</p>
        </a>
    </div>
</div>
</body>
</html>

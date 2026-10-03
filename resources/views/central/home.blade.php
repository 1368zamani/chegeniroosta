<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پایگاه اطلاع‌رسانی بخش‌های شهرستان چگنی | استان لرستان</title>
    <meta name="description" content="سامانه رسمی معرفی و اطلاع‌رسانی بخش‌های شهرستان چگنی، استان لرستان">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#0d2e4f">

    {{-- Open Graph --}}
    <meta property="og:site_name" content="سامانه اطلاع‌رسانی چگنی">
    <meta property="og:type" content="website">
    <meta property="og:title" content="پایگاه اطلاع‌رسانی بخش‌های شهرستان چگنی">
    <meta property="og:description" content="سامانه رسمی معرفی و اطلاع‌رسانی بخش‌های شهرستان چگنی">
    <meta property="og:url" content="{{ url()->current() }}">

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect fill='%230d2e4f' width='100' height='100' rx='20'/><text x='50' y='68' font-size='55' text-anchor='middle' fill='%23ffd54f' font-family='Tahoma'>چ</text></svg>">

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Tahoma, 'Segoe UI', Arial, sans-serif;
            background: #eef2f7;
            color: #263238;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            width: min(1400px, 96%);
            margin: auto;
            flex: 1;
            padding: 50px 0 30px;
        }

        .page-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-title h2 {
            color: #0d2e4f;
            margin: 0 0 10px;
            font-size: 28px;
            font-weight: 800;
        }

        .page-title p {
            color: #78909c;
            margin: 0;
            font-size: 15px;
        }

        .page-title .line {
            width: 80px;
            height: 3px;
            background: #ffd54f;
            margin: 18px auto 0;
            border-radius: 2px;
        }

        .sections-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 28px;
            padding-bottom: 50px;
        }

        .section-card {
            background: white;
            border-radius: 16px;
            padding: 0;
            box-shadow: 0 8px 30px rgba(13,46,79,0.08);
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border-top: 5px solid #0d2e4f;
            position: relative;
        }

        .section-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 45px rgba(13,46,79,0.15);
        }

        .section-card[data-slug="visian"] { border-top-color: #1565c0; }
        .section-card[data-slug="markazi"] { border-top-color: #2e7d32; }
        .section-card[data-slug="shahivand"] { border-top-color: #c62828; }

        .section-card-inner {
            padding: 32px 28px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .section-card .icon {
            width: 68px;
            height: 68px;
            background: #e3f2fd;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 32px;
            box-shadow: 0 5px 18px rgba(21,101,192,0.12);
        }

        .section-card[data-slug="visian"] .icon { background: #e3f2fd; color: #1565c0; }
        .section-card[data-slug="markazi"] .icon { background: #e8f5e9; color: #2e7d32; }
        .section-card[data-slug="shahivand"] .icon { background: #ffebee; color: #c62828; }

        .section-card h3 {
            margin: 0 0 12px;
            color: #0d2e4f;
            font-size: 22px;
            font-weight: 800;
        }

        .section-card .village-count {
            display: inline-block;
            background: #f0f4f8;
            color: #546e7a;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12px;
            margin-bottom: 16px;
            font-weight: 600;
        }

        .section-card .location {
            color: #78909c;
            font-size: 13px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .section-card .description {
            color: #607d8b;
            line-height: 1.9;
            font-size: 14px;
            margin-bottom: 20px;
            flex: 1;
        }

        .section-card .enter-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #0d2e4f;
            color: white;
            padding: 12px 22px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
            align-self: flex-start;
        }

        .section-card:hover .enter-btn {
            background: #ffd54f;
            color: #0d2e4f;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #90a4ae;
        }

        .empty-state .icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        @media (max-width: 700px) {
            .page-title h2 { font-size: 22px; }
            .sections-grid { grid-template-columns: 1fr; gap: 20px; }
            .section-card h3 { font-size: 18px; }
        }
    
/* ============================================
   MOBILE FIXES - نمایش بهتر روی موبایل
   ============================================ */

/* ---------- موبایل کوچک (تا 600px) ---------- */
@media (max-width: 600px) {

    /* بدنه اصلی */
    body {
        font-size: 14px;
        line-height: 1.7;
    }

    /* ---- نوار بالا ---- */
    .gov-topbar,
    .gv-topbar,
    .s-header-top {
        padding: 6px 0 !important;
        font-size: 11px !important;
    }
    .gov-topbar-inner,
    .gv-topbar-inner,
    .s-header-top-inner {
        padding: 0 10px !important;
        gap: 6px !important;
        justify-content: center !important;
        text-align: center !important;
    }
    .gov-date,
    .gov-date-box,
    .gv-topbar-date,
    .s-top-right {
        font-size: 11px !important;
    }

    /* ---- نوار شعر ---- */
    .daily-poem-inline,
    .gv-poetry-inner {
        padding: 0 10px !important;
        font-size: 11px !important;
        flex-direction: column !important;
        gap: 3px !important;
    }
    .daily-poem-inline .poem-text,
    .gv-poetry-text {
        font-size: 11.5px !important;
        line-height: 1.6 !important;
    }
    .daily-poem-inline .poem-poet,
    .gv-poetry-poet {
        font-size: 10px !important;
    }

    /* ---- هدر اصلی ---- */
    .gov-header,
    .gv-header-main,
    .s-header-main {
        padding: 12px 0 !important;
    }
    .gov-header-inner,
    .gv-header-inner,
    .s-header-main-inner {
        padding: 0 10px !important;
        gap: 10px !important;
        flex-direction: column !important;
        align-items: center !important;
        text-align: center !important;
    }

    /* آرم کوچیک‌تر */
    .gov-emblem,
    .gv-emblem,
    .s-emblem {
        width: 52px !important;
        height: 52px !important;
    }

    /* عنوان */
    .gov-main-title,
    .gv-main-title,
    .s-titles h1 {
        font-size: 15px !important;
        line-height: 1.4 !important;
        text-align: center !important;
    }
    .gov-sub-title,
    .gv-sub-title,
    .s-titles p {
        font-size: 10.5px !important;
        justify-content: center !important;
        text-align: center !important;
        gap: 4px !important;
    }
    .gov-titles,
    .gv-titles,
    .s-titles {
        text-align: center !important;
    }

    /* ---- عکس رهبری در هدر ---- */
    .gov-right-block,
    .gov-leader-block {
        display: none !important;
    }

    /* ---- منوی ناوبری ---- */
    .gov-navbar-inner,
    .gv-nav-inner,
    .s-nav-inner {
        padding: 0 8px !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        flex-wrap: nowrap !important;
    }
    .gov-navbar-inner::-webkit-scrollbar,
    .gv-nav-inner::-webkit-scrollbar,
    .s-nav-inner::-webkit-scrollbar {
        display: none !important;
    }
    .gov-nav-item,
    .gv-nav-inner a,
    .s-nav-inner a {
        padding: 10px 12px !important;
        font-size: 11.5px !important;
        gap: 4px !important;
    }
    .gov-nav-actions,
    .gv-nav-login {
        padding-left: 8px !important;
    }
    .gov-action-btn,
    .gv-nav-login .btn-login {
        padding: 6px 10px !important;
        font-size: 11px !important;
    }

    /* ---- نوار دولت وفاق ملی ---- */
    .gov-consensus-banner {
        padding: 8px 12px !important;
    }
    .gov-consensus-text {
        font-size: 12px !important;
        letter-spacing: 0.2px !important;
        padding: 0 12px !important;
    }
    .gov-consensus-star {
        font-size: 11px !important;
    }
    .gov-consensus-inner {
        gap: 8px !important;
    }

    /* ---- دو ستونی → تک ستونی ---- */
    .two-columns {
        display: flex !important;
        flex-direction: column !important;
        gap: 14px !important;
        padding: 12px 0 !important;
    }
    .two-columns main,
    .two-columns aside {
        width: 100% !important;
    }

    /* ---- کادرها ---- */
    .s-container,
    .container {
        padding: 0 10px !important;
        max-width: 100% !important;
    }
    .box {
        border-radius: 8px !important;
        margin-bottom: 12px !important;
    }
    .box-header {
        padding: 10px 12px !important;
    }
    .box-header h3 {
        font-size: 13px !important;
    }
    .box-body {
        padding: 12px !important;
    }

    /* ---- کارت Hero صفحه ---- */
    .page-hero {
        padding: 20px 12px !important;
        margin-bottom: 16px !important;
    }
    .page-hero h2 {
        font-size: 17px !important;
    }
    .page-hero p {
        font-size: 11.5px !important;
    }

    /* ---- اخبار ---- */
    .news-list-item {
        flex-direction: column !important;
        gap: 10px !important;
        padding: 12px 0 !important;
    }
    .news-thumb {
        width: 100% !important;
        height: 180px !important;
        border-radius: 8px !important;
    }
    .news-list-item h4 {
        font-size: 14px !important;
        line-height: 1.5 !important;
    }
    .news-list-item .meta {
        font-size: 10.5px !important;
    }
    .news-list-item p {
        font-size: 12px !important;
        line-height: 1.7 !important;
    }

    /* ---- کارت بخشدار ---- */
    .bakhshdar-side-card {
        padding: 16px 12px !important;
    }
    .bakhshdar-side-card .photo,
    .bakhshdar-side-card .photo-ph {
        width: 80px !important;
        height: 80px !important;
    }
    .bakhshdar-side-card h4 {
        font-size: 14px !important;
    }
    .bakhshdar-side-card .role {
        font-size: 11px !important;
    }
    .bakhshdar-side-card .btn {
        padding: 6px 14px !important;
        font-size: 11.5px !important;
    }

    /* ---- لیست روستاها ---- */
    .villages-side-list a {
        padding: 10px 12px !important;
        font-size: 12.5px !important;
    }
    .villages-side-list .count {
        font-size: 10px !important;
    }

    /* ---- فوتر ---- */
    .gov-footer-main,
    .s-footer-inner {
        grid-template-columns: 1fr !important;
        gap: 20px !important;
        padding: 24px 12px 20px !important;
    }
    .gov-footer h3,
    .s-footer h3 {
        font-size: 13px !important;
        margin-bottom: 10px !important;
    }
    .gov-footer p,
    .gov-footer ul a,
    .s-footer p,
    .s-footer ul a {
        font-size: 12px !important;
    }
    .gov-footer-bottom,
    .s-footer-bottom {
        padding: 12px !important;
        font-size: 11px !important;
    }
    .gov-footer-stats,
    .gov-footer-logos {
        display: none !important;
    }

    /* ---- گالری ---- */
    .gallery-grid,
    .gallery-grid-mini {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 8px !important;
    }
    .gallery-item-mini img,
    .gallery-item img {
        height: 100px !important;
    }

    /* ---- بخش‌های پرتال مرکزی ---- */
    .sections-grid {
        grid-template-columns: 1fr !important;
        gap: 14px !important;
    }
    .section-card-inner {
        padding: 20px 16px !important;
    }
    .section-card h3 {
        font-size: 16px !important;
    }
    .section-card .icon {
        width: 50px !important;
        height: 50px !important;
        font-size: 24px !important;
        margin-bottom: 12px !important;
    }
    .section-card .description {
        font-size: 12.5px !important;
    }

    /* ---- دکمه‌ها ---- */
    .btn {
        padding: 10px 18px !important;
        font-size: 13px !important;
    }

    /* ---- جدول‌ها (پنل ادمین) ---- */
    .table-responsive,
    table {
        font-size: 11.5px !important;
    }

    /* ---- جستجو ---- */
    .search-box input {
        padding: 12px 40px 12px 16px !important;
        font-size: 14px !important;
    }

    /* ---- Back to top ---- */
    #backToTop {
        width: 40px !important;
        height: 40px !important;
        bottom: 15px !important;
        right: 15px !important;
    }

    /* ---- Dark mode toggle ---- */
    #darkModeToggle {
        width: 38px !important;
        height: 38px !important;
        font-size: 16px !important;
    }
}

/* ---------- تبلت (601px تا 900px) ---------- */
@media (min-width: 601px) and (max-width: 900px) {

    .two-columns {
        grid-template-columns: 1fr !important;
    }

    .gov-main-title,
    .gv-main-title {
        font-size: 18px !important;
    }

    .gov-emblem,
    .gv-emblem {
        width: 60px !important;
        height: 60px !important;
    }

    .sections-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }

    .gov-right-block,
    .gov-leader-block {
        display: none !important;
    }
}

/* ---------- جلوگیری از اسکرول افقی ---------- */
html, body {
    overflow-x: hidden !important;
    max-width: 100vw !important;
    width: 100% !important;
}

* {
    max-width: 100%;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

img, iframe, video {
    max-width: 100% !important;
    height: auto !important;
}

/* ---------- جدول‌های پنل ادمین ---------- */
.table-scroll {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* ---------- دکمه‌های لمسی ---------- */
button, a, input[type="submit"] {
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
}


/* ============================================
   MOBILE OVERRIDE - نمایش بهتر
   ============================================ */
@media (max-width: 768px) {
    /* دیدگاه */
    .view, .container, .s-container {
        padding-left: 10px !important;
        padding-right: 10px !important;
    }
    /* فوتر */
    .gov-footer-main, .s-footer-inner {
        grid-template-columns: 1fr !important;
    }
    /* دو ستونی */
    .two-columns {
        grid-template-columns: 1fr !important;
        gap: 14px !important;
    }
}

/* جلوگیری از اسکرول افقی */
html, body {
    overflow-x: hidden !important;
    max-width: 100vw !important;
}

img, iframe, video {
    max-width: 100% !important;
    height: auto !important;
}

</style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
<link rel="stylesheet" href="{{ asset('css/green-theme.css') }}">
</head>
<body>

@include('partials.gov-header')

@include('partials.gov-hero', [
    'heroTitle' => 'پایگاه اطلاع‌رسانی بخش‌های شهرستان چگنی',
    'heroSubtitle' => 'سامانه رسمی معرفی، اطلاع‌رسانی و مدیریت بخش‌ها و روستاهای شهرستان چگنی، استان لرستان'
])

<main class="container">

    <div class="page-title">
        <h2>📁 بخش‌های شهرستان چگنی</h2>
        <p>برای ورود به پایگاه اطلاع‌رسانی هر بخش، روی آن کلیک کنید</p>
        <div class="line"></div>
    </div>

    @if($sections->count() > 0)
        <div class="sections-grid">
            @foreach($sections as $section)
                <a href="{{ request()->getScheme() }}://{{ $section->subdomain }}.{{ config('app.base_domain') }}"
                   class="section-card"
                   data-slug="{{ $section->slug }}">
                    <div class="section-card-inner">
                        <div class="icon">📍</div>
                        <h3>{{ $section->name }}</h3>

                        @if($section->county || $section->province)
                            <div class="location">
                                <span>🗺</span>
                                <span>
                                    @if($section->county) شهرستان {{ $section->county }} @endif
                                    @if($section->province) — {{ $section->province }} @endif
                                </span>
                            </div>
                        @endif

                        <div class="village-count">🏘 {{ $section->villages()->count() }} روستا</div>

                        <p class="description">
                            {{ $section->description
                                ? \Illuminate\Support\Str::limit($section->description, 120)
                                : 'اطلاعات و اخبار این بخش را مشاهده کنید.' }}
                        </p>

                        <span class="enter-btn">
                            ورود به سایت بخش ←
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="icon">📭</div>
            <p>هنوز بخشی ثبت نشده است.</p>
        </div>
    @endif

</main>

@include('partials.gov-footer')
@include('partials.back-to-top')
@include('partials.analytics')

</body>
</html>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $news->title }} | {{ $section->name }}</title>
    <meta name="description" content="{{ $news->excerpt ?: Illuminate\Support\Str::limit(strip_tags($news->content), 150) }}">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="{{ $news->title }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($news->image)
        <meta property="og:image" content="{{ asset('storage/' . $news->image) }}">
    @endif
    <meta name="theme-color" content="#0d2e4f">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #f0f3f7; color: #263238; line-height: 1.9; }
        .s-container { max-width: 1200px; margin: auto; padding: 0 15px; }

        .breadcrumb {
            padding: 14px 0;
            color: #78909c;
            font-size: 12.5px;
        }
        .breadcrumb a { color: #1565c0; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }

        .two-columns {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 24px;
            padding-bottom: 40px;
        }

        .box { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(13,46,79,0.06); margin-bottom: 20px; overflow: hidden; }
        .box-header { padding: 14px 18px; border-bottom: 2px solid #eceff1; display: flex; align-items: center; justify-content: space-between; }
        .box-header h3 { margin: 0; color: #0d2e4f; font-size: 14.5px; font-weight: 800; }
        .box-header .line { width: 40px; height: 3px; background: #ffd54f; border-radius: 2px; }
        .box-body { padding: 24px; }

        .article-title {
            color: #0d2e4f;
            font-size: 24px;
            font-weight: 800;
            margin: 0 0 14px;
            line-height: 1.6;
        }
        .article-meta {
            display: flex;
            gap: 18px;
            color: #90a4ae;
            font-size: 12.5px;
            padding-bottom: 16px;
            border-bottom: 1px solid #eceff1;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .article-image {
            width: 100%;
            max-height: 450px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 22px;
        }
        .article-body {
            font-size: 15px;
            line-height: 2.1;
            color: #37474f;
        }
        .article-body p { margin: 0 0 16px; }

        .back-link {
            display: inline-block;
            margin-top: 24px;
            color: #1565c0;
            text-decoration: none;
            font-size: 13.5px;
        }
        .back-link:hover { text-decoration: underline; }

        .related-list { list-style: none; margin: 0; padding: 0; }
        .related-list li { border-bottom: 1px solid #f0f3f7; }
        .related-list li:last-child { border-bottom: none; }
        .related-list a { display: block; padding: 12px 14px; color: #37474f; text-decoration: none; font-size: 13px; line-height: 1.7; }
        .related-list a:hover { background: #f5f7fa; color: #1565c0; }

        @media (max-width: 900px) {
            .two-columns { grid-template-columns: 1fr; }
            .article-title { font-size: 19px; }
            .box-body { padding: 18px; }
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

</style>

<style>
/* CARD-HOVER-2026 */
.info-item,
.info-card,
[class*="info-"] {
    transition: transform .25s ease, box-shadow .25s ease !important;
}
.info-item:hover,
.info-card:hover {
    transform: translateY(-3px) !important;
    box-shadow: 0 8px 24px rgba(13,46,79,0.15) !important;
}
.v-gallery-item:hover {
    transform: translateY(-3px) !important;
    box-shadow: 0 12px 30px rgba(13,46,79,0.18) !important;
}
/* tel: — لینک تماس */
a[href^="tel:"] {
    direction: ltr;
    display: inline-block;
    font-weight: 600;
}
</style>

</head>
<body>

@include('partials.gov-header', ['headerTitle' => 'پایگاه اطلاع‌رسانی ' . $section->name])

<div class="s-container">
    <div class="breadcrumb">
        <a href="/">خانه</a> › <a href="/news">اخبار</a> › {{ Illuminate\Support\Str::limit($news->title, 50) }}
    </div>

    <div class="two-columns">
        <main>
            <div class="box">
                <div class="box-body">
                    <h1 class="article-title">{{ $news->title }}</h1>

                    <div class="article-meta">
                        <span>🗓 {{ $news->published_at ? $news->published_at->format('Y/m/d') : $news->created_at->format('Y/m/d') }}</span>
                        @if($news->user)
                            <span>✍️ {{ $news->user->name }}</span>
                        @endif
                    </div>

                    @if($news->image)
                        <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}" class="article-image">
                    @endif

                    <div class="article-body">
                        {!! nl2br(e($news->content)) !!}
                    </div>

                    <a href="/news" class="back-link">← بازگشت به لیست اخبار</a>
                </div>
            </div>
        </main>

        <aside>
            @if($related->count() > 0)
                <div class="box">
                    <div class="box-header">
                        <h3>📰 اخبار مرتبط</h3>
                        <div class="line"></div>
                    </div>
                    <ul class="related-list">
                        @foreach($related as $item)
                            <li>
                                <a href="{{ route('section.news.show', $item->slug) }}">
                                    {{ Illuminate\Support\Str::limit($item->title, 70) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </aside>
    </div>
</div>

@include('partials.gov-footer')
@include('partials.dark-mode')
@include('partials.back-to-top')
@include('partials.analytics')

</body>
</html>

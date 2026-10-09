<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $section->name }} | پایگاه اطلاع‌رسانی</title>
    <meta name="description" content="{{ $section->description ?? 'پایگاه اطلاع‌رسانی بخش ' . $section->name . ' شهرستان چگنی' }}">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="{{ $section->name }} | پایگاه اطلاع‌رسانی">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="theme-color" content="#0d2e4f">

    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Tahoma, Arial, sans-serif;
            background: #f0f3f7;
            color: #263238;
            line-height: 1.8;
        }

        .s-container {
            max-width: 1200px;
            margin: auto;
            padding: 0 15px;
        }

        /* ==== کادر عنوان بخش ==== */
        
        
        

        /* ==== چیدمان دو ستونی ==== */
        .two-columns {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 24px;
            padding-bottom: 40px;
        }

        /* ==== کادر عمومی ==== */
        .box {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(13,46,79,0.06);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .box-header {
            padding: 14px 18px;
            border-bottom: 2px solid #eceff1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .box-header h3 {
            margin: 0;
            color: #0d2e4f;
            font-size: 15px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .box-header .line {
            width: 40px;
            height: 3px;
            background: #ffd54f;
            border-radius: 2px;
        }
        .box-body {
            padding: 18px;
        }

        /* ==== لیست اخبار ==== */
        .news-list-item {
            display: flex;
            gap: 14px;
            padding: 16px 0;
            border-bottom: 1px solid #eceff1;
            align-items: flex-start;
        }
        .news-list-item:last-child { border-bottom: none; }
        .news-thumb {
            width: 110px;
            height: 110px;
            flex-shrink: 0;
            border-radius: 8px;
            overflow: hidden;
            background: #f0f3f7;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .news-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .news-thumb-empty {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #e3f2fd, #f0f3f7);
            color: #90a4ae;
            font-size: 32px;
        }
        .news-content {
            flex: 1;
            min-width: 0;
        }
        .news-list-item:last-child { border-bottom: none; }
        .news-list-item h4 {
            margin: 0 0 6px;
            font-size: 15px;
            line-height: 1.6;
        }
        .news-list-item h4 a {
            color: #0d2e4f;
            text-decoration: none;
        }
        .news-list-item h4 a:hover { color: #1565c0; }
        .news-list-item .meta {
            font-size: 11.5px;
            color: #90a4ae;
            margin-bottom: 8px;
        }
        .news-list-item p {
            margin: 0;
            color: #546e7a;
            font-size: 13px;
            line-height: 1.9;
        }
        .pin-badge {
            display: inline-block;
            background: #ffd54f;
            color: #0d2e4f;
            font-size: 10.5px;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 6px;
            font-weight: 700;
        }

        /* ==== لیست روستاها (سایدبار) ==== */
        
        
        
        
        
        

        /* ==== کارت بخشدار در سایدبار ==== */
        .bakhshdar-side-card {
            text-align: center;
            padding: 22px 16px;
        }
        
        
        .bakhshdar-side-card .label {
            display: inline-block;
            font-size: 10.5px;
            background: #0d2e4f;
            color: #ffd54f;
            padding: 2px 10px;
            border-radius: 12px;
            margin-bottom: 8px;
            font-weight: 700;
        }
        .bakhshdar-side-card h4 {
            margin: 0 0 4px;
            color: #0d2e4f;
            font-size: 15px;
        }
        .bakhshdar-side-card .role {
            font-size: 12px;
            color: #78909c;
            margin: 0 0 14px;
        }
        .bakhshdar-side-card .btn {
            display: inline-block;
            background: #0d2e4f;
            color: #fff;
            padding: 8px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12.5px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .bakhshdar-side-card .btn:hover {
            background: #ffd54f;
            color: #0d2e4f;
        }

        .empty-note {
            color: #90a4ae;
            text-align: center;
            padding: 24px 0;
            font-size: 13px;
        }

        @media (max-width: 900px) {
            .two-columns { grid-template-columns: 1fr; }
            
        }
    
        .gallery-grid-mini {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 12px;
        }
        .gallery-item-mini {
            border-radius: 8px;
            overflow: hidden;
            background: #f5f7fa;
            box-shadow: 0 3px 10px rgba(0,0,0,0.06);
            transition: transform .2s;
        }
        .gallery-item-mini:hover { transform: translateY(-3px); }
        .gallery-item-mini img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            display: block;
        }
        .gallery-caption-mini {
            padding: 8px 10px;
            font-size: 11.5px;
            color: #546e7a;
            background: #fff;
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


        /* ============ عکس بخشدار - مربع و بزرگ ============ */
        .bakhshdar-side-card {
            text-align: center;
            padding: 22px 16px;
        }
        .bakhshdar-side-card .photo {
            width: 100%;
            max-width: 220px;
            height: 220px;
            border-radius: 12px;
            object-fit: cover;
            border: 3px solid #ffd54f;
            box-shadow: 0 8px 25px rgba(255,213,79,0.4);
            display: block;
            margin: 0 auto 14px;
        }
        .bakhshdar-side-card .photo-ph {
            width: 100%;
            max-width: 220px;
            height: 220px;
            border-radius: 12px;
            background: linear-gradient(135deg, #e3f2fd, #f5f7fa);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 80px;
            color: #90a4ae;
            margin: 0 auto 14px;
            border: 3px solid #ffd54f;
            box-shadow: 0 8px 25px rgba(255,213,79,0.3);
        }
        .bakhshdar-side-card .label {
            display: inline-block;
            font-size: 11px;
            background: #0d2e4f;
            color: #ffd54f;
            padding: 3px 14px;
            border-radius: 14px;
            margin-bottom: 10px;
            font-weight: 700;
        }
        .bakhshdar-side-card h4 {
            margin: 0 0 4px;
            color: #0d2e4f;
            font-size: 17px;
            font-weight: 800;
        }
        .bakhshdar-side-card .role {
            font-size: 12.5px;
            color: #78909c;
            margin: 0 0 16px;
        }
        .bakhshdar-side-card .btn {
            display: inline-block;
            background: #0d2e4f;
            color: #fff;
            padding: 10px 22px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .bakhshdar-side-card .btn:hover {
            background: #ffd54f;
            color: #0d2e4f;
            transform: translateY(-2px);
        }

        @media (max-width: 700px) {
            .bakhshdar-side-card .photo,
            .bakhshdar-side-card .photo-ph {
                max-width: 180px;
                height: 180px;
            }
        }

    
        /* ============ لیست روستاها ============ */
        .villages-side-list {
            list-style: none;
            margin: 0;
            padding: 0;
            max-height: 400px;
            overflow-y: auto;
        }
        .villages-side-list li {
            border-bottom: 1px solid #f0f3f7;
        }
        .villages-side-list li:last-child {
            border-bottom: none;
        }

        .village-item-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 14px;
            color: #37474f;
            text-decoration: none;
            font-size: 13.5px;
            transition: all 0.2s;
            gap: 10px;
            position: relative;
        }
        .village-item-link:hover {
            background: linear-gradient(90deg, #f0f7ff 0%, #f5f7fa 100%);
            padding-right: 18px;
        }
        .village-item-link::before {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%) scaleY(0);
            width: 3px;
            height: 70%;
            background: #1565c0;
            border-radius: 2px;
            transition: transform 0.2s;
        }
        .village-item-link:hover::before {
            transform: translateY(-50%) scaleY(1);
        }

        .village-name-text {
            font-weight: 700;
            color: #0d2e4f;
            flex: 1;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .village-item-link:hover .village-name-text {
            color: #1565c0;
        }

        .village-enter-text {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            color: #1565c0;
            background: #e3f2fd;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 600;
            white-space: nowrap;
            flex-shrink: 0;
            transition: all 0.2s;
        }
        .village-item-link:hover .village-enter-text {
            background: #1565c0;
            color: white;
        }
        .village-enter-text .enter-arrow {
            font-size: 12px;
            font-weight: 700;
        }

        /* موبایل */
        @media (max-width: 700px) {
            .village-item-link {
                padding: 10px 12px;
                font-size: 12.5px;
            }
            .village-enter-text {
                font-size: 10px;
                padding: 3px 8px;
            }
            .village-enter-text .enter-label {
                display: none;
            }
            .village-enter-text::before {
                content: 'ورود';
                font-size: 10px;
            }
        }

    
/* ═══════ تعرفه عوارض — dropdown بخشداری ═══════ */
.s-tariff-dropdown { position: relative; display: inline-block; }
.s-tariff-btn { display: inline-flex; align-items: center; gap: 8px; cursor: pointer; border: 1px solid rgba(255,255,255,0.4); background: rgba(255,255,255,0.15); color: #fff; padding: 12px 28px; border-radius: 8px; font-family: inherit; font-size: 15px; font-weight: 600; transition: all .2s; }
.s-tariff-btn:hover { background: rgba(255,255,255,0.28); transform: translateY(-1px); }
.s-tariff-btn .caret { font-size: 11px; transition: transform .25s; }
.s-tariff-dropdown.open .caret { transform: rotate(180deg); }
.s-tariff-menu { display: none; position: absolute; top: calc(100% + 10px); right: 50%; transform: translateX(50%); min-width: 300px; background: #fff; border-radius: 14px; box-shadow: 0 20px 55px rgba(13,46,79,0.25), 0 4px 12px rgba(0,0,0,0.08); padding: 8px 0; z-index: 99999; overflow: hidden; border: 1px solid #e3f2fd; text-align: right; }
@keyframes sMenuIn { from { opacity: 0; transform: translateX(50%) translateY(-6px) scale(0.98); } to { opacity: 1; transform: translateX(50%) translateY(0) scale(1); } }
.s-tariff-dropdown.open .s-tariff-menu { display: block; animation: sMenuIn .22s cubic-bezier(0.2, 0.9, 0.3, 1.2); }
.s-tariff-menu a { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #37474f; text-decoration: none; font-size: 13.5px; font-weight: 500; transition: background .15s, color .15s, padding-right .15s; white-space: nowrap; border-right: 3px solid transparent; }
.s-tariff-menu a:hover { background: linear-gradient(90deg, #f0f7ff, #e3f2fd); color: #1e5b9c; border-right-color: #1e5b9c; padding-right: 24px; }
.s-tariff-menu a .m-icon { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: #f0f7ff; border-radius: 8px; font-size: 17px; flex-shrink: 0; }
.s-tariff-menu .menu-divider { height: 1px; background: linear-gradient(90deg, transparent, #e3f2fd 30%, #e3f2fd 70%, transparent); margin: 6px 12px; }
.s-tariff-menu .menu-label { font-size: 10.5px; color: #94a3b8; padding: 10px 20px 4px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.7px; }
.s-tariff-menu .menu-note { font-size: 11.5px; color: #78350f; background: #fef3c7; padding: 10px 20px; line-height: 1.7; border-right: 3px solid #d97706; margin: 4px 12px 8px; border-radius: 8px; }
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

<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
<link rel="stylesheet" href="{{ asset('css/green-theme.css') }}">
</head>
<body>

@include('partials.gov-header', ['headerTitle' => 'پایگاه اطلاع‌رسانی بخشداری ' . $section->name])

{{-- نوار باریک اخبار زیر هدر --}}
@include('partials.news-ticker', ['news' => $news ?? collect()])

<div class="s-container">

    <div class="two-columns">

        {{-- ستون اصلی: اخبار --}}
        <main>
            <div class="box">
                <div class="box-header">
                    <h3>📰 آخرین اخبار و اطلاعیه‌ها</h3>
                    <div class="line"></div>
                </div>
                <div class="box-body">
                    @if(isset($news) && $news->count() > 0)
                        @foreach($news as $item)
                            <div class="news-list-item">
                                <div class="news-thumb">
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" loading="lazy">
                                    @else
                                        <div class="news-thumb-empty">📰</div>
                                    @endif
                                </div>
                                <div class="news-content">
                                    <h4>
                                        @if($item->is_pinned)
                                            <span class="pin-badge">📌 مهم</span>
                                        @endif
                                        <a href="{{ route('section.news.show', $item->slug) }}">{{ $item->title }}</a>
                                    </h4>
                                    <div class="meta">
                                        🗓 {{ $item->published_at ? jalali($item->published_at) : jalali($item->created_at) }}
                                    </div>
                                    <p>{{ Illuminate\Support\Str::limit($item->excerpt ?: strip_tags($item->content), 150) }}</p>
                                </div>
                            </div>
                        @endforeach
                        <div style="text-align:center; padding-top:16px;">
                            <a href="/news" style="color:#1565c0; text-decoration:none; font-size:13px;">مشاهده همه اخبار ←</a>
                        </div>
                    @else
                        <div class="empty-note">هنوز خبری منتشر نشده است.</div>
                    @endif
                </div>
            </div>

            {{-- گالری تصاویر بخش --}}
            @if(isset($gallery) && count($gallery) > 0)
                <div class="box">
                    <div class="box-header">
                        <h3>🖼 گالری تصاویر {{ $section->name }}</h3>
                        <div class="line"></div>
                    </div>
                    <div class="box-body">
                        <div class="gallery-grid-mini">
                            @foreach($gallery as $img)
                                <div class="gallery-item-mini sg-lightbox-trigger"
                                     data-image="{{ asset('storage/' . $img->image_path) }}"
                                     data-caption="{{ $img->caption ?: $section->name }}">
                                    <img src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $img->caption ?: $section->name }}" loading="lazy">
                                    @if($img->caption)
                                        <div class="gallery-caption-mini">{{ $img->caption }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </main>

        {{-- سایدبار --}}
        <aside>

            {{-- کارت بخشدار --}}
            @if($section->bakhshdar_name)
                <div class="box">
                    <div class="bakhshdar-side-card">
                        @if($section->bakhshdar_photo)
                            <img src="{{ asset('storage/' . $section->bakhshdar_photo) }}" alt="{{ $section->bakhshdar_name }}" class="photo">
                        @else
                            <div class="photo-ph">👤</div>
                        @endif
                        <br>
                        <span class="label">👤 بخشدار</span>
                        <h4>{{ $section->bakhshdar_name }}</h4>
                        <p class="role">بخشدار {{ $section->name }}</p>
                        <a href="/bakhshdar" class="btn">مشاهده پروفایل ←</a>
                    </div>
                </div>
            @endif

            {{-- لیست روستاها --}}
            <div class="box" id="villages">
                <div class="box-header">
                    <h3>🏘 روستاهای {{ $section->name }}</h3>
                    <div class="line"></div>
                </div>
                @if($villages->count() > 0)
                    <ul class="villages-side-list">
                        @foreach($villages as $village)
                            <li>
                                <a href="{{ route('public.village.show', $village->slug) }}" class="village-item-link">
                                    <span class="village-name-text">{{ $village->name }}</span>
                                    <span class="village-enter-text">
                                        <span class="enter-label">ورود به سایت</span>
                                        <span class="enter-arrow">←</span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="empty-note">هنوز روستایی ثبت نشده است.</div>
                @endif
            </div>

        </aside>

    </div>

</div>


{{-- section-gallery-lightbox --}}
<style>
    .sg-lightbox-trigger { cursor: pointer; position: relative; }
    .sg-lightbox-trigger::after {
        content: '🔍';
        position: absolute;
        top: 8px;
        left: 8px;
        background: rgba(13,46,79,.75);
        color: #fff;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        opacity: 0;
        transition: opacity .2s;
        pointer-events: none;
    }
    .sg-lightbox-trigger:hover::after { opacity: 1; }

    .sg-lightbox-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.92);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: sgFadeIn .2s ease;
    }
    .sg-lightbox-overlay.active { display: flex; }
    @keyframes sgFadeIn { from{opacity:0} to{opacity:1} }

    .sg-lightbox-content {
        position: relative;
        max-width: 90vw;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .sg-lightbox-image {
        max-width: 90vw;
        max-height: 78vh;
        border-radius: 10px;
        box-shadow: 0 15px 50px rgba(0,0,0,.6);
        object-fit: contain;
    }
    .sg-lightbox-caption {
        background: rgba(255,255,255,.95);
        color: #1e293b;
        padding: 12px 24px;
        border-radius: 10px;
        margin-top: 15px;
        font-family: 'Vazirmatn', Tahoma, sans-serif;
        direction: rtl;
        font-size: 14px;
        font-weight: 600;
        text-align: center;
        max-width: 90vw;
    }
    .sg-lightbox-close {
        position: absolute;
        top: -55px;
        left: 0;
        background: rgba(255,255,255,.15);
        color: #fff;
        border: none;
        font-size: 26px;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        cursor: pointer;
        line-height: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .15s;
    }
    .sg-lightbox-close:hover { background: rgba(255,255,255,.3); }
    @media (max-width: 700px) {
        .sg-lightbox-close { top: 8px; left: 8px; }
        .sg-lightbox-image { max-height: 65vh; }
    }

        /* ===== HOVER: متن خبر سفید ===== */
        .news-list-item {
            transition: background .2s ease;
            padding: 12px 14px;
            border-radius: 8px;
        }
        .news-list-item:hover {
            background: #0d2e4f !important;
        }
        .news-list-item:hover h4,
        .news-list-item:hover h4 a,
        .news-list-item:hover h4 a:hover,
        .news-list-item:hover p,
        .news-list-item:hover span,
        .news-list-item:hover .meta,
        .news-list-item:hover .date,
        .news-list-item:hover .news-content,
        .news-list-item:hover .news-content * {
            color: #ffffff !important;
        }
        /* ===== END ===== */
</style>

<div class="sg-lightbox-overlay" id="sgLightbox" role="dialog" aria-modal="true">
    <div class="sg-lightbox-content">
        <button class="sg-lightbox-close" id="sgLightboxClose" aria-label="بستن">×</button>
        <img src="" alt="" class="sg-lightbox-image" id="sgLightboxImage">
        <div class="sg-lightbox-caption" id="sgLightboxCaption" style="display:none"></div>
    </div>
</div>

<script>
(function(){
    var overlay = document.getElementById('sgLightbox');
    if (!overlay) return;
    var img = document.getElementById('sgLightboxImage');
    var cap = document.getElementById('sgLightboxCaption');
    var closeBtn = document.getElementById('sgLightboxClose');

    function open(src, caption) {
        img.src = src;
        if (caption && caption.trim()) {
            cap.textContent = caption;
            cap.style.display = 'block';
        } else {
            cap.style.display = 'none';
        }
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function close() {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    document.addEventListener('click', function(e) {
        var trigger = e.target.closest('.sg-lightbox-trigger');
        if (trigger) {
            e.preventDefault();
            open(trigger.dataset.image || '', trigger.dataset.caption || '');
        }
    });

    if (closeBtn) closeBtn.addEventListener('click', close);
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) close();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && overlay.classList.contains('active')) close();
    });
})();
</script>


{{-- باکس درخواست مردمی بخشداری --}}
<div class="s-container" style="margin-bottom: 30px;">
    <div class="section-request-box" style="background: linear-gradient(135deg, #1e5b9c, #2470b8); color: #fff; border-radius: 14px; padding: 30px 25px; box-shadow: 0 8px 25px rgba(13,46,79,0.15); text-align: center;">
        <h3 style="margin: 0 0 10px; font-size: 20px; color: #fff;">📩 درخواست خود را ثبت کنید</h3>
        <p style="margin: 0 0 20px; opacity: 0.9; font-size: 14px; line-height: 2;">
            درخواست‌های خود در زمینه آب، برق، گاز، راه، عمران و ... را آنلاین ثبت کنید و با کد پیگیری دنبال کنید.
        </p>
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('section.site.request.create') }}"
               style="display: inline-block; background: #ffd54f; color: #14497f; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 15px; transition: all .2s;">
                ✍️ ثبت درخواست
            </a>
            <a href="{{ route('section.site.request.track') }}"
               style="display: inline-block; background: rgba(255,255,255,0.15); color: #fff; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 15px; border: 1px solid rgba(255,255,255,0.3);">
                🔍 پیگیری درخواست
            </a>
            <div class="s-tariff-dropdown" id="sTariffDropdown">
                <button type="button" class="s-tariff-btn">
                    <span>🧾 تعرفه عوارض</span>
                    <span class="caret">▾</span>
                </button>
                <div class="s-tariff-menu" role="menu">
                    <div class="menu-label">مشاهده</div>
                    <a href="{{ route('section.site.tariff') }}" role="menuitem">
                        <span class="m-icon">📋</span>
                        <span>تعرفه‌های روستاهای بخش</span>
                    </a>
                    <div class="menu-divider"></div>
                    <div class="menu-note">
                        ⚠️ دریافت وجه نقد توسط دهیار یا کارکنان <strong>ممنوع</strong> است.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
(function () {
    function init() {
        var dd = document.getElementById('sTariffDropdown');
        if (!dd) return;
        var btn = dd.querySelector('.s-tariff-btn');
        btn.addEventListener('click', function (e) { e.stopPropagation(); dd.classList.toggle('open'); });
        document.addEventListener('click', function (e) { if (!dd.contains(e.target)) dd.classList.remove('open'); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') dd.classList.remove('open'); });
        dd.querySelectorAll('.s-tariff-menu a').forEach(function (a) { a.addEventListener('click', function () { dd.classList.remove('open'); }); });
    }
    document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', init) : init();
})();
</script>

<div class="section-tariff-admin-link" style="max-width: 1100px; margin: 0 auto 30px; padding: 0 20px;">
    <div style="background: #e8f5e9; border-right: 4px solid #2e7d32; color: #1b5e20; padding: 14px 20px; border-radius: 10px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
        <div style="font-size: 13.5px; line-height: 1.9;">
            <strong>🔐 کارشناسان فنی و مالی دهیاری‌ها:</strong>
            برای ثبت، ویرایش و مدیریت تعرفه‌ی عوارض روستاهای این بخش وارد شوید.
        </div>
        <a href="{{ route('expert.login') }}"
           style="display: inline-block; background: #2e7d32; color: #fff; padding: 10px 22px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13.5px; white-space: nowrap;">
            ورود کارشناسان ←
        </a>
    </div>
</div>

@include('partials.gov-footer')
@include('partials.back-to-top')
@include('partials.analytics')

</body>
</html>

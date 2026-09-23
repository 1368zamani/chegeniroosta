@php
    if (!function_exists('daily_persian_verse')) {
        function daily_persian_verse() {
            // گرفتن اشعار از تنظیمات
            $verses = setting('poetry_verses', null);

            // اگه تنظیمات نبود، اشعار پیش‌فرض
            if (!is_array($verses) || count($verses) === 0) {
                $verses = [
                    ['text' => 'بنی آدم اعضای یک پیکرند / که در آفرینش ز یک گوهرند', 'poet' => 'سعدی'],
                    ['text' => 'چو عضوی به درد آورد روزگار / دگر عضوها را نماند قرار', 'poet' => 'سعدی'],
                    ['text' => 'توانا بود هر که دانا بود / ز دانش دل پیر برنا بود', 'poet' => 'فردوسی'],
                    ['text' => 'دوش دیدم که ملائک در میخانه زدند / گِل آدم بسرشتند و به پیمانه زدند', 'poet' => 'حافظ'],
                ];
            }

            $dayOfYear = (int) now()->format('z');
            return $verses[$dayOfYear % count($verses)];
        }
    }
@endphp

{{-- ============================================
     هدر رسمی دولتی
     ============================================ --}}
@php
    if (!function_exists('gregorian_to_jalali_hdr')) {
        function gregorian_to_jalali_hdr($gy, $gm, $gd) {
            $g_d_m = [0,31,59,90,120,151,181,212,243,273,304,334];
            $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
            $days = 355666 + (365 * $gy) + ((int)(($gy2 + 3) / 4)) - ((int)(($gy2 + 99) / 100))
                  + ((int)(($gy2 + 399) / 400)) + $gd + $g_d_m[$gm - 1];
            $jy = -1595 + (33 * ((int)($days / 12053)));
            $days %= 12053;
            $jy += 4 * ((int)($days / 1461));
            $days %= 1461;
            if ($days > 365) {
                $jy += (int)(($days - 1) / 365);
                $days = ($days - 1) % 365;
            }
            if ($days < 186) {
                $jm = 1 + (int)($days / 31);
                $jd = 1 + ($days % 31);
            } else {
                $jm = 7 + (int)(($days - 186) / 30);
                $jd = 1 + (($days - 186) % 30);
            }
            return [$jy, $jm, $jd];
        }
    }
    if (!function_exists('today_jalali_hdr')) {
        function today_jalali_hdr() {
            $now = now();
            [$jy, $jm, $jd] = gregorian_to_jalali_hdr($now->year, $now->month, $now->day);
            $months = ['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'];
            $days = ['یکشنبه','دوشنبه','سه‌شنبه','چهارشنبه','پنجشنبه','جمعه','شنبه'];
            return $days[$now->dayOfWeek] . '، ' . $jd . ' ' . $months[$jm - 1] . ' ' . $jy;
        }
    }
@endphp

<style>
    /* ==================== نوار بالا ==================== */
    .gov-topbar {
        background: linear-gradient(90deg, #0d2e4f 0%, #103d68 50%, #0d2e4f 100%);
        color: white;
        padding: 8px 0;
        font-size: 13px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .gov-topbar-inner {
        width: min(1400px, 96%);
        margin: auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    .gov-topbar-left, .gov-topbar-right {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }
    /* پرتال زیر تاریخ (عمودی) */
    .gov-date-portal-stack {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
        line-height: 1.3;
    }
    .gov-date-box {
        color: rgba(255,255,255,0.95);
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12.5px;
        font-weight: 600;
    }
    .gov-topbar-link {
        color: rgba(255,255,255,0.75);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 11.5px;
        padding-right: 22px;
        transition: all .2s;
    }
    .gov-topbar-link:hover { color: #ffd54f; padding-right: 26px; }
    .gov-slogan-topbar {
        color: #ffd54f;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        text-align: left;
    }

    @media (max-width: 700px) {
        .gov-topbar-inner { justify-content: center; text-align: center; }
        .gov-topbar-right, .gov-topbar-left { justify-content: center; text-align: center; }
        .gov-date-portal-stack { align-items: center; }
        .gov-topbar-link { padding-right: 0; }
        .gov-topbar-link:hover { padding-right: 0; }
        .gov-slogan-topbar { font-size: 11px; text-align: center; }
    }

/* ==================== هدر اصلی ==================== */
    

    /* پس‌زمینه پل کشکان */
    }');
        background-size: cover;
        background-position: center 40%;
        background-repeat: no-repeat;
        opacity: 0.45;
        z-index: 0;
        pointer-events: none;
    }

    /* گرادیان محوکننده */
    

    /* ==================== بخش وسط: آرم + عنوان ==================== */
    
    
    
    
    
    
    .gov-sub-title span { display: inline-flex; align-items: center; gap: 4px; }

    /* ==================== بخش راست: عکس رهبری + شعار ==================== */

    

    
    

    /* ==================== بخش چپ: خالی ==================== */

    /* ==================== منوی ناوبری ==================== */
    .gov-navbar {
        background: linear-gradient(90deg, #0d2e4f 0%, #103d68 100%);
        border-top: 1px solid rgba(255,213,79,0.3);
        position: relative;
        z-index: 5;
    }
    .gov-navbar-inner {
        width: min(1400px, 96%);
        margin: auto;
        display: flex;
        align-items: center;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .gov-navbar-inner::-webkit-scrollbar { display: none; }
    .gov-nav-item {
        color: white;
        text-decoration: none;
        padding: 15px 22px;
        font-size: 13.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-bottom: 3px solid transparent;
        transition: all .2s;
        white-space: nowrap;
    }
    .gov-nav-item:hover {
        background: rgba(255,255,255,0.08);
        border-bottom-color: #ffd54f;
        color: #ffd54f;
    }
    .gov-nav-item.active {
        border-bottom-color: #ffd54f;
        color: #ffd54f;
    }
    .gov-nav-actions {
        margin-right: auto;
        display: flex;
        gap: 8px;
        padding-left: 20px;
    }
    .gov-action-btn {
        color: white;
        text-decoration: none;
        padding: 8px 14px;
        background: rgba(255,255,255,0.1);
        border-radius: 6px;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border: 1px solid rgba(255,255,255,0.15);
        transition: all .2s;
    }
    .gov-action-btn:hover {
        background: rgba(255,213,79,0.2);
        border-color: #ffd54f;
        color: #ffd54f;
    }

    /* ==================== ریسپانسیو ==================== */
    @media (max-width: 1000px) {

        
        
    }
    @media (max-width: 600px) {
        .gov-topbar-inner { justify-content: center; text-align: center; }
        .gov-topbar-left, .gov-topbar-right { justify-content: center; }
        
        

    }

    /* ==================== کانتینر هدر ==================== */
    

    /* ==================== راست: آرم + عنوان ==================== */
    
    
    
    
    
    
    .gov-sub-title span {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* ==================== چپ: عکس رهبری + شعار ==================== */
    

    

    
    
    

    /* ==================== ریسپانسیو ==================== */
    @media (max-width: 1000px) {
        
        
        
        
        
        
    }
    @media (max-width: 600px) {
        
        
        
        
        
    }

    /* ============ شعر روز در نوار بالا ============ */
    .daily-poem-inline {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: #ffd54f;
        font-weight: 700;
        font-size: 13px;
        text-align: center;
        justify-content: center;
        flex: 1;
    }
    .daily-poem-inline .poem-text {
        font-family: 'B Nazanin', Tahoma, serif;
        font-weight: 700;
        letter-spacing: 0.3px;
    }
    .daily-poem-inline .poem-poet {
        color: rgba(255,213,79,0.75);
        font-size: 11.5px;
        white-space: nowrap;
    }
    @media (max-width: 700px) {
        .daily-poem-inline { font-size: 11px; gap: 6px; }
        .daily-poem-inline .poem-poet { font-size: 10px; }
    }

    .gov-consensus-banner {
        background: linear-gradient(90deg, #0d2e4f 0%, #1a4a7a 30%, #1565c0 50%, #1a4a7a 70%, #0d2e4f 100%);
        color: white;
        padding: 12px 20px;
        text-align: center;
        border-bottom: 2px solid #ffd54f;
        box-shadow: 0 4px 15px rgba(13,46,79,0.15);
        position: relative;
        overflow: hidden;
    }
    .gov-consensus-banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,213,79,0.15), transparent);
        animation: govShine 6s ease-in-out infinite;
    }
    @keyframes govShine {
        0% { left: -100%; }
        50%, 100% { left: 100%; }
    }
    .gov-consensus-inner {
        position: relative;
        z-index: 1;
        display: inline-flex;
        align-items: center;
        gap: 16px;
        justify-content: center;
    }
    .gov-consensus-star {
        color: #ffd54f;
        font-size: 14px;
        animation: govTwinkle 3s ease-in-out infinite;
    }
    .gov-consensus-star:nth-child(3) { animation-delay: 1.5s; }
    @keyframes govTwinkle {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(0.85); }
    }
    .gov-consensus-text {
        font-family: 'B Nazanin', Tahoma, serif;
        font-size: 17px;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: 1px;
        text-shadow: 0 1px 2px rgba(0,0,0,0.3), 0 0 15px rgba(255,213,79,0.25);
        padding: 0 22px;
        position: relative;
    }
    .gov-consensus-text::before,
    .gov-consensus-text::after {
        content: '';
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 10px;
        height: 10px;
        border: 2px solid #ffd54f;
        border-radius: 50%;
        opacity: 0.7;
    }
    .gov-consensus-text::before { right: 0; }
    .gov-consensus-text::after { left: 0; }
    .gov-consensus-highlight { color: #ffd54f; font-weight: 900; }
    @media (max-width: 700px) {
        .gov-consensus-banner { padding: 10px 15px; }
        .gov-consensus-text { font-size: 13.5px; letter-spacing: 0.5px; padding: 0 16px; }
        .gov-consensus-star { font-size: 12px; }
        .gov-consensus-inner { gap: 10px; }
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
    

    .gov-emblem,
    

    .sections-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }

    .gov-right-block,
    
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

    /* ============ عکس رهبری - بزرگ و واضح ============ */
    
    

    /* شعار سال - بزرگ‌تر و واضح‌تر */
    
    
    
    

    /* ============ فونت ایران نستعلیق برای عنوان ============ */
    @font-face {
        src: url('https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css');
        font-display: swap;
    }

    /* فونت نستعلیق از CDN */
    @import url('https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css');

    /* عنوان اصلی با فونت نستعلیق */
    .gov-main-title,
    .gv-main-title,
    .s-titles h1 {
        font-family: 'B Nazanin', 'Vazirmatn', Tahoma, serif !important;
        font-size: 34px !important;
        font-weight: 900 !important;
        color: #ffffff !important;
        text-shadow:
            3px 3px 0 #0d2e4f,
            -1px -1px 0 #0d2e4f,
            1px -1px 0 #0d2e4f,
            -1px 1px 0 #0d2e4f,
            0 4px 15px rgba(13,46,79,0.9),
            0 0 25px rgba(255,213,79,0.5) !important;
        letter-spacing: 0 !important;
        line-height: 1.4 !important;
        padding: 8px 16px !important;
        margin: 0 0 8px 0 !important;
        
        
        backdrop-filter: blur(6px) !important;
        
        display: inline-block !important;
    }

    /* زیرعنوان هم کمی بهتر */
    .gov-sub-title,
    .gv-sub-title,
    .s-titles p {
        color: #ffffff !important;
        font-weight: 700 !important;
        text-shadow:
            0 2px 4px rgba(13,46,79,0.9),
            0 0 10px rgba(13,46,79,0.7) !important;
        
        padding: 4px 12px !important;
        border-radius: 8px !important;
        backdrop-filter: blur(4px) !important;
        display: inline-block !important;
        margin: 0 !important;
    }

    /* تنظیمات موبایل */
    @media (max-width: 700px) {
        .gov-main-title,
        
        .gov-sub-title,
        
    }

    /* مرکز - چیدمان */
    .gov-center-block,
    

    /* ============ هدر - ارتفاع ثابت ============ */
    

    }');
        background-size: cover;
        background-position: center 45%;
        background-repeat: no-repeat;
        opacity: 0.4;
        z-index: 0;
    }

    

    

    /* ============ سمت چپ: عکس رهبری ============ */
    

    

    
    

    /* شعار سال کنار عکس */
    
    
    

    /* ============ وسط: آرم + عنوان ============ */
    

    
    

    .gov-titles,
    

    /* عنوان بزرگ بدون کادر */
    .gov-main-title,
    .gv-main-title,
    .s-titles h1 {
        margin: 0 0 6px 0 !important;
        font-family: 'B Nazanin', 'Tahoma', serif !important;
        font-size: 32px !important;
        font-weight: 900 !important;
        color: #ffffff !important;
        text-shadow:
            2px 2px 0 #0d2e4f,
            -1px -1px 0 #0d2e4f,
            1px -1px 0 #0d2e4f,
            -1px 1px 0 #0d2e4f,
            0 3px 12px rgba(13,46,79,0.95),
            0 0 20px rgba(255,213,79,0.4) !important;
        letter-spacing: 0 !important;
        line-height: 1.3 !important;
        padding: 0 !important;
        background: none !important;
        border: none !important;
        border-radius: 0 !important;
        display: block !important;
    }

    .gov-sub-title,
    .gv-sub-title,
    .s-titles p {
        margin: 0 !important;
        color: #ffffff !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        text-shadow:
            0 2px 5px rgba(13,46,79,0.95),
            0 0 12px rgba(13,46,79,0.7) !important;
        background: none !important;
        padding: 0 !important;
        border: none !important;
        display: flex !important;
        gap: 8px !important;
        justify-content: flex-end !important;
        flex-wrap: wrap !important;
    }

    /* ============ ریسپانسیو ============ */
    @media (max-width: 1000px) {
        
        
        
        
        
        
        
        
    }

    @media (max-width: 700px) {
        
        
        
        
        
        
        
        
    }

    /* ============================================
       هدر رسمی - بازنویسی کامل
       ============================================ */

    .gov-header {
        position: relative;
        background: #ffffff;
        border-bottom: 3px solid #0d2e4f;
        overflow: hidden;
        height: 200px;
        display: flex;
        align-items: center;
    }

    /* پس‌زمینه پل کشکان */
    .gov-header::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: url('{{ asset("images/gov/kashkan-bridge.jpg") }}');
        background-size: cover;
        background-position: center 45%;
        background-repeat: no-repeat;
        opacity: 0.5;
        z-index: 0;
    }

    /* لایه محوکننده */
    .gov-header::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg,
            rgba(255,255,255,0.25) 0%,
            rgba(255,255,255,0.05) 50%,
            rgba(238,244,250,0.75) 100%);
        z-index: 0;
        pointer-events: none;
    }

    .gov-header-inner {
        width: min(1400px, 96%);
        margin: auto;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 30px;
        position: relative;
        z-index: 3;
        height: 100%;
    }

    /* ============================================
       سمت راست: آرم + عنوان
       ============================================ */
    .gov-brand-block {
        display: flex;
        align-items: center;
        gap: 18px;
        flex-shrink: 1;
        min-width: 0;
    }

    .gov-emblem {
        width: 100px;
        height: 100px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        filter: drop-shadow(0 4px 12px rgba(13,46,79,0.35));
    }

    .gov-emblem img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
    }

    .gov-titles {
        text-align: right;
        flex-shrink: 1;
        min-width: 0;
    }

    /* عنوان - قرمز بزرگ بدون کادر */
    .gov-main-title {
        margin: 0 0 8px 0;
        font-family: 'B Nazanin', Tahoma, serif;
        font-size: 34px;
        font-weight: 900;
        color: #c62828;
        text-shadow:
            2px 2px 0 #ffffff,
            -1px -1px 0 #ffffff,
            1px -1px 0 #ffffff,
            -1px 1px 0 #ffffff,
            0 3px 12px rgba(198,40,40,0.4);
        line-height: 1.3;
        letter-spacing: 0;
    }

    /* زیرعنوان - سفید با حاشیه تیره */
    .gov-sub-title {
        margin: 0;
        color: #ffffff;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        flex-wrap: wrap;
        text-shadow:
            0 2px 4px rgba(13,46,79,1),
            0 0 8px rgba(13,46,79,0.9);
    }

    /* ============================================
       سمت چپ: عکس رهبری + شعار
       ============================================ */
    .gov-leader-block {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-shrink: 0;
    }

    .gov-slogan-side {
        text-align: left;
        max-width: 200px;
    }

    .gov-slogan-tag {
        display: inline-block;
        font-size: 11px;
        color: #ffd54f;
        background: rgba(13,46,79,0.9);
        padding: 4px 12px;
        border-radius: 4px;
        border-left: 3px solid #ffd54f;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .gov-slogan-text {
        font-size: 12px;
        color: #0d2e4f;
        font-weight: 900;
        line-height: 1.8;
        text-shadow:
            0 1px 3px rgba(255,255,255,1),
            0 0 8px rgba(255,255,255,0.9);
    }

    /* عکس رهبری - بدون قاب */
    .gov-leader-square-wrap {
        width: 160px;
        height: 160px;
        flex-shrink: 0;
        -webkit-mask-image: radial-gradient(ellipse at center,
            rgba(0,0,0,1) 60%,
            rgba(0,0,0,0.9) 75%,
            rgba(0,0,0,0.4) 90%,
            rgba(0,0,0,0) 100%);
        mask-image: radial-gradient(ellipse at center,
            rgba(0,0,0,1) 60%,
            rgba(0,0,0,0.9) 75%,
            rgba(0,0,0,0.4) 90%,
            rgba(0,0,0,0) 100%);
        filter: drop-shadow(0 4px 15px rgba(0,0,0,0.25)) brightness(1.06);
    }

    .gov-leader-square-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        border-radius: 0;
    }

    /* ============================================
       ریسپانسیو
       ============================================ */
    @media (max-width: 1000px) {
        .gov-header { height: 170px; }
        .gov-emblem { width: 75px; height: 75px; }
        .gov-main-title { font-size: 24px; }
        .gov-sub-title { font-size: 11px; }
        .gov-leader-square-wrap { width: 120px; height: 120px; }
        .gov-slogan-text { font-size: 10.5px; }
        .gov-slogan-tag { font-size: 9.5px; }
        .gov-slogan-side { max-width: 150px; }
    }

    @media (max-width: 700px) {
        .gov-header {
            height: auto;
            min-height: 150px;
            padding: 14px 0;
        }
        .gov-header-inner {
            flex-direction: column;
            gap: 12px;
            padding: 0 12px;
        }
        .gov-brand-block { gap: 12px; }
        .gov-emblem { width: 60px; height: 60px; }
        .gov-main-title { font-size: 18px; text-align: center; }
        .gov-sub-title { font-size: 10px; justify-content: center; }
        .gov-titles { text-align: center; }
        .gov-leader-block { gap: 12px; flex-direction: row-reverse; }
        .gov-leader-square-wrap { width: 90px; height: 90px; }
        .gov-slogan-side { text-align: center; max-width: 160px; }
        .gov-slogan-text { font-size: 10px; }
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

    /* ============================================
       MODERN-HEADER-2026 — Override
       فقط خاصیت‌های ظاهری، بدون تغییر ساختار
       ============================================ */

    /* ۱. لایه تیره روی عکس پل — خوانایی متن سفید */
    .gov-header::after {
        background: linear-gradient(180deg,
            rgba(13, 27, 42, 0.45) 0%,
            rgba(13, 27, 42, 0.60) 50%,
            rgba(13, 27, 42, 0.70) 100%) !important;
    }

    /* ۲. فونت استاندارد + عنوان سفید Bold */
    .gov-main-title,
    .gv-main-title,
    .s-titles h1 {
        font-family: 'Vazirmatn', 'B Nazanin', Tahoma, sans-serif !important;
        color: #ffffff !important;
        font-size: 40px !important;
        font-weight: 800 !important;
        text-shadow:
            0 2px 4px rgba(0,0,0,0.55),
            0 4px 24px rgba(0,0,0,0.35) !important;
        letter-spacing: -0.3px !important;
        line-height: 1.35 !important;
    }

    .gov-sub-title,
    .gv-sub-title,
    .s-titles p {
        color: #f1f5f9 !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        text-shadow: 0 2px 6px rgba(0,0,0,0.6) !important;
    }

    /* حذف رنگ‌های ناهمگون سبز/قرمز — همه کرم روشن یا طلایی */
    .gov-consensus-highlight {
        color: #ffd54f !important;
    }
    .gov-slogan-tag {
        color: #ffd54f !important;
        background: rgba(13,46,79,0.75) !important;
        font-size: 10.5px !important;
        padding: 3px 10px !important;
    }
    .gov-slogan-text {
        color: #ffffff !important;
        font-size: 12px !important;
        text-shadow: 0 2px 6px rgba(0,0,0,0.6) !important;
    }

    /* ۳. پاک‌سازی هدر — عکس رهبری کوچیک‌تر و نامحسوس */
    .gov-leader-square-wrap {
        width: 100px !important;
        height: 100px !important;
        filter: drop-shadow(0 3px 10px rgba(0,0,0,0.35)) brightness(1.05) !important;
    }
    .gov-slogan-side {
        max-width: 150px !important;
    }

    /* ۴. منو برجسته‌تر */
    .gov-navbar {
        border-top: 2px solid rgba(255,213,79,0.55) !important;
        box-shadow: 0 6px 20px rgba(13,46,79,0.25) !important;
    }
    .gov-nav-item {
        font-size: 15.5px !important;
        font-weight: 700 !important;
        padding: 17px 24px !important;
        letter-spacing: 0.1px !important;
    }
    .gov-nav-item:hover,
    .gov-nav-item.active {
        background: rgba(255,213,79,0.15) !important;
        border-bottom-color: #ffd54f !important;
        color: #ffd54f !important;
    }

    /* ۵. دکمه پنل مدیریت → آیکون چرخ‌دنده گرد */
    .gov-action-btn {
        width: 38px !important;
        height: 38px !important;
        padding: 0 !important;
        font-size: 18px !important;
        border-radius: 50% !important;
        justify-content: center !important;
        align-items: center !important;
        background: rgba(255,213,79,0.12) !important;
        border: 1px solid rgba(255,213,79,0.35) !important;
        color: #ffd54f !important;
    }
    .gov-action-btn:hover {
        background: rgba(255,213,79,0.25) !important;
        transform: rotate(30deg);
        transition: transform .25s ease;
    }
    .gov-nav-actions {
        padding-left: 12px !important;
    }

    /* ============ موبایل ============ */
    @media (max-width: 1000px) {
        .gov-main-title { font-size: 30px !important; }
        .gov-leader-square-wrap { width: 90px !important; height: 90px !important; }
        .gov-slogan-side { max-width: 130px !important; }
    }
    @media (max-width: 700px) {
        .gov-main-title { font-size: 22px !important; text-align: center !important; }
        .gov-sub-title { font-size: 11px !important; justify-content: center !important; }
        .gov-leader-square-wrap { width: 70px !important; height: 70px !important; }
        .gov-slogan-side { max-width: 110px !important; text-align: center !important; }
        .gov-slogan-text { font-size: 10.5px !important; }
        .gov-slogan-tag { font-size: 9.5px !important; padding: 2px 7px !important; }
        .gov-nav-item {
            font-size: 13px !important;
            padding: 12px 14px !important;
        }
        .gov-action-btn {
            width: 34px !important;
            height: 34px !important;
            font-size: 15px !important;
        }
    }

    /* پایان MODERN-HEADER-2026 */

    /* ============================================
   HEADER-V2.1 — کارت شعار تمیز
   ============================================ */

.gov-slogan-card {
    background: rgba(13, 27, 42, 0.55);
    -webkit-backdrop-filter: blur(14px) saturate(140%);
    backdrop-filter: blur(14px) saturate(140%);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-right: 3px solid #ffd54f;
    border-radius: 10px;
    padding: 16px 22px;
    box-shadow:
        0 8px 28px rgba(0, 0, 0, 0.35),
        inset 0 1px 0 rgba(255, 255, 255, 0.08);
    max-width: 360px;
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
}
.gov-slogan-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(120deg, rgba(255, 213, 79, 0.06) 0%, transparent 55%);
    pointer-events: none;
}
.gov-slogan-card-text {
    color: #ffffff;
    font-family: 'Vazirmatn', 'IRANSans', Tahoma, sans-serif;
    font-size: 15px;
    font-weight: 600;
    line-height: 2;
    letter-spacing: 0.2px;
    text-align: right;
    position: relative;
    z-index: 1;
    text-shadow: 0 1px 4px rgba(0, 0, 0, 0.5);
}

/* لوگو بزرگ‌تر و متعادل */
.gov-emblem {
    width: 110px !important;
    height: 110px !important;
}

/* ============ عنوان اصلی — فونت بهتر ============ */
.gov-main-title,
.gv-main-title,
.s-titles h1 {
    font-family: 'Vazirmatn', 'IRANSans', Tahoma, sans-serif !important;
    font-size: 34px !important;
    font-weight: 800 !important;
    color: #ffffff !important;
    letter-spacing: -0.5px !important;
    line-height: 1.5 !important;
    text-shadow:
        0 2px 6px rgba(0, 0, 0, 0.55),
        0 4px 20px rgba(0, 0, 0, 0.35) !important;
}

.gov-sub-title,
.gv-sub-title,
.s-titles p {
    font-family: 'Vazirmatn', 'IRANSans', Tahoma, sans-serif !important;
    font-size: 13.5px !important;
    font-weight: 500 !important;
    color: #f1f5f9 !important;
    letter-spacing: 0.1px !important;
    text-shadow: 0 2px 6px rgba(0, 0, 0, 0.55) !important;
}

/* ============ منوی مدرن‌تر ============ */
.gov-action-btn {
    width: 38px !important;
    height: 38px !important;
    padding: 0 !important;
    font-size: 16px !important;
    border-radius: 50% !important;
    justify-content: center !important;
    align-items: center !important;
    background: rgba(255, 213, 79, 0.15) !important;
    border: 1px solid rgba(255, 213, 79, 0.45) !important;
    color: #ffd54f !important;
    display: inline-flex !important;
}
.gov-action-btn:hover {
    background: rgba(255, 213, 79, 0.3) !important;
    color: #ffffff !important;
    transform: scale(1.08) !important;
    transition: all .25s ease !important;
}

/* ============ موبایل ============ */
@media (max-width: 1000px) {
    .gov-slogan-card {
        padding: 12px 16px;
        max-width: 260px;
    }
    .gov-slogan-card-text {
        font-size: 13px;
        line-height: 1.9;
    }
    .gov-emblem { width: 88px !important; height: 88px !important; }
    .gov-main-title { font-size: 28px !important; }
}
@media (max-width: 700px) {
    .gov-slogan-card {
        padding: 12px 16px;
        max-width: 100%;
        text-align: center;
        border-right: none;
        border-top: 3px solid #ffd54f;
    }
    .gov-slogan-card-text {
        font-size: 12.5px;
        text-align: center;
        line-height: 1.85;
    }
    .gov-emblem { width: 70px !important; height: 70px !important; }
    .gov-main-title { font-size: 21px !important; text-align: center !important; }
    .gov-sub-title { font-size: 11px !important; justify-content: center !important; }
}

/* پایان HEADER-V2.1 */

</style>

{{-- ================ TOP BAR ================ --}}
<div class="gov-topbar">
    <div class="gov-topbar-inner">
        {{-- راست: تاریخ + پرتال مرکزی (عمودی) --}}
        <div class="gov-topbar-right">
            <div class="gov-date-portal-stack">
                <div class="gov-date-box">
                    📅 {{ today_jalali_hdr() }}
                </div>
                <a href="{{ request()->getScheme() }}://{{ config('app.base_domain') }}" class="gov-topbar-link">
                    🏠 پرتال مرکزی
                </a>
            </div>
        </div>

        {{-- چپ: شعار سال --}}
        <div class="gov-topbar-left">
            <div class="daily-poem-inline">
                    @php $verse = daily_persian_verse(); @endphp
                    <span class="poem-text">{{ $verse['text'] }}</span>
                    <span class="poem-poet">— {{ $verse['poet'] }}</span>
                </div>
        </div>
    </div>
</div>

{{-- ================ HEADER ================ --}}
<header class="gov-header">
    <div class="gov-header-inner">

        {{-- سمت راست (اول در RTL): آرم + عنوان --}}
        <div class="gov-brand-block">
            <div class="gov-emblem" title="وزارت کشور">
                <img src="{{ \App\Models\SiteSetting::emblemUrl($section->logo ?? null) }}" alt="آرم وزارت کشور">
            </div>
            <div class="gov-titles">
                <h1 class="gov-main-title">{{ $headerTitle ?? 'پایگاه اطلاع‌رسانی بخش‌های شهرستان چگنی' }}</h1>
                <p class="gov-sub-title">
                    <span>🏛️ وزارت کشور</span>
                    <span>•</span>
                    <span>استانداری لرستان</span>
                    <span>•</span>
                    <span>فرمانداری چگنی</span>
                </p>
            </div>
        </div>

        {{-- سمت چپ (آخر در RTL): کارت شعار سال --}}
        <div class="gov-slogan-card">
            <div class="gov-slogan-card-text">امنیت ملی و وحدت ملی<br>در سایه اقتصاد مقاومتی</div>
        </div>

    </div>
</header>

{{-- ================ NAVBAR ================ --}}
<nav class="gov-navbar">
    <div class="gov-navbar-inner">
        <a href="/" class="gov-nav-item {{ request()->path() === '/' ? 'active' : '' }}">
            🏠 خانه
        </a>
        @if(isset($section))
            <a href="/#villages" class="gov-nav-item">🏘 روستاهای بخش</a>
            <a href="/news" class="gov-nav-item {{ str_contains(request()->path(), 'news') ? 'active' : '' }}">📰 اخبار</a>
        @else
            <a href="/#sections" class="gov-nav-item">📁 بخش‌ها</a>
        @endif
        <a href="{{ route('search.index') }}" class="gov-nav-item">🔍 جستجو</a>
        @if(isset($section) && ($section->bakhshdar_name ?? false))
            <a href="/bakhshdar" class="gov-nav-item {{ str_contains(request()->path(), 'bakhshdar') ? 'active' : '' }}">👤 معرفی بخشدار</a> {{-- menu-moved-2026 --}}
        @endif
        <div class="gov-nav-actions">
            @auth
                <a href="{{ route('admin.dashboard') }}" class="gov-action-btn">⚙️</a>
            @else
                <a href="{{ route('login') }}" class="gov-action-btn">🔐 ورود</a>
            @endauth
        </div>
    </div>
</nav>

{{-- نوار دولت چهاردهم، دولت وفاق ملی --}}
<div class="gov-consensus-banner">
    <div class="gov-consensus-inner">
        <span class="gov-consensus-star">✦</span>
        <span class="gov-consensus-text">
            <span class="gov-consensus-highlight">{{ setting('banner_highlight_1', 'دولت چهاردهم') }}</span>، <span class="gov-consensus-highlight">{{ setting('banner_highlight_2', 'دولت وفاق ملی') }}</span>
        </span>
        <span class="gov-consensus-star">✦</span>
    </div>
</div>


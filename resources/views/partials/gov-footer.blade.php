{{-- ============================================
     فوتر رسمی دولتی
     ============================================ --}}

<style>
    .gov-footer {
        background: linear-gradient(180deg, #0d3b2e 0%, #071a2f 100%);
        color: rgba(255,255,255,0.85);
        padding: 50px 0 0;
        margin-top: 60px;
        border-top: none;
        position: relative;
    }

    .gov-footer-main {
        width: min(1400px, 96%);
        margin: auto;
        display: grid;
        grid-template-columns: 1.5fr 1fr 1fr 1.3fr;
        gap: 40px;
        padding-bottom: 40px;
    }

    .gov-footer h3 {
        color: #ffd54f;
        font-size: 15px;
        margin: 0 0 18px;
        padding-bottom: 12px;
        border-bottom: 2px solid rgba(255,213,79,0.25);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .gov-footer p {
        margin: 0 0 12px;
        font-size: 13px;
        line-height: 2;
        color: rgba(255,255,255,0.75);
    }

    .gov-footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .gov-footer-links li {
        margin-bottom: 10px;
    }
    .gov-footer-links a {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all .2s;
        padding: 4px 0;
    }
    .gov-footer-links a:hover {
        color: #ffd54f;
        padding-right: 6px;
    }
    .gov-footer-links a::before {
        content: '◄';
        font-size: 9px;
        color: #ffd54f;
        opacity: 0.6;
    }

    .gov-footer-contact {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .gov-footer-contact li {
        display: flex;
        gap: 10px;
        margin-bottom: 14px;
        font-size: 13px;
        align-items: flex-start;
        color: rgba(255,255,255,0.8);
    }
    .gov-footer-contact li .icon {
        flex-shrink: 0;
        width: 26px;
        height: 26px;
        background: rgba(255,213,79,0.15);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        color: #ffd54f;
    }
    .gov-footer-contact a {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        direction: ltr;
        display: inline-block;
    }
    .gov-footer-contact a:hover { color: #ffd54f; }

    .gov-footer-logos {
        display: flex;
        gap: 16px;
        margin-top: 16px;
        align-items: center;
        justify-content: flex-start;
        flex-wrap: wrap;
    }
    .gov-footer-logo-box {
        background: rgba(255,255,255,0.08);
        border-radius: 8px;
        padding: 8px 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1px solid rgba(255,213,79,0.2);
    }
    .gov-footer-logo-box svg { width: 32px; height: 32px; }
    .gov-footer-logo-box span {
        font-size: 11px;
        color: rgba(255,255,255,0.85);
        font-weight: 600;
    }

    .gov-footer-bottom {
        background: rgba(0,0,0,0.3);
        border-top: 1px solid rgba(255,255,255,0.08);
        padding: 18px 0;
        text-align: center;
        font-size: 12px;
        color: rgba(255,255,255,0.6);
    }
    .gov-footer-bottom-inner {
        width: min(1400px, 96%);
        margin: auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .gov-footer-bottom a {
        color: rgba(255,213,79,0.85);
        text-decoration: none;
    }
    .gov-footer-bottom a:hover { color: #ffd54f; }

    .gov-footer-stats {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        margin-top: 8px;
    }
    .gov-footer-stat {
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,213,79,0.15);
        border-radius: 8px;
        padding: 10px 14px;
        text-align: center;
        flex: 1;
        min-width: 80px;
    }
    .gov-footer-stat .num {
        display: block;
        font-size: 20px;
        font-weight: 700;
        color: #ffd54f;
        line-height: 1.2;
    }
    .gov-footer-stat .label {
        display: block;
        font-size: 10px;
        color: rgba(255,255,255,0.7);
        margin-top: 4px;
    }

    @media (max-width: 900px) {
        .gov-footer-main {
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
    }
    @media (max-width: 600px) {
        .gov-footer-main { grid-template-columns: 1fr; }
        .gov-footer-bottom-inner { justify-content: center; text-align: center; }
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

<footer class="gov-footer">


<div class="gov-footer-main">

        {{-- درباره --}}
        <div>
            <h3>🏛️ درباره سامانه</h3>
            <p>{{ setting('footer_about', 'سامانه اطلاع‌رسانی روستاهای شهرستان چگنی، استان لرستان.') }}</p>

            <div class="gov-footer-logos">
                <div class="gov-footer-logo-box">
                    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <path d="M 50,15 L 75,25 L 75,55 C 75,72 62,82 50,87 C 38,82 25,72 25,55 L 25,25 Z"
                              fill="none" stroke="#ffd54f" stroke-width="2"/>
                        <circle cx="50" cy="50" r="10" fill="none" stroke="#ffd54f" stroke-width="1.5"/>
                    </svg>
                    <span>وزارت کشور</span>
                </div>
                <div class="gov-footer-logo-box">
                    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="50" cy="50" r="38" fill="none" stroke="#ffd54f" stroke-width="2"/>
                        <text x="50" y="60" text-anchor="middle" font-size="26" font-weight="bold"
                              fill="#ffd54f" font-family="Tahoma">لر</text>
                    </svg>
                    <span>استانداری لرستان</span>
                </div>
            </div>

            {{-- آمار کلی --}}
            
        </div>

        {{-- لینک‌های مفید --}}
        <div>
            <h3>🔗 لینک‌های مفید</h3>
            <ul class="gov-footer-links">
                <li><a href="https://www.moi.ir" target="_blank" rel="noopener">وزارت کشور</a></li>
                <li><a href="https://lorestan.ostan-lr.ir" target="_blank" rel="noopener">استانداری لرستان</a></li>
                <li><a href="#" target="_blank" rel="noopener">فرمانداری چگنی</a></li>
                <li><a href="https://www.ir" target="_blank" rel="noopener">درگاه ملی ایران</a></li>
                <li><a href="https://www.dolat.ir" target="_blank" rel="noopener">پایگاه اطلاع‌رسانی دولت</a></li>
            </ul>
        </div>

        {{-- دسترسی سریع --}}
        <div>
            <h3>⚡ دسترسی سریع</h3>
            <ul class="gov-footer-links">
                <li><a href="/">صفحه اصلی</a></li>
                @if(isset($section))
                    <li><a href="/news">اخبار و اطلاعیه‌ها</a></li>
                    @if($section->bakhshdar_name ?? false)
                        <li><a href="/bakhshdar">معرفی بخشدار</a></li>
                    @endif
                @else
                    <li><a href="/#sections">فهرست بخش‌ها</a></li>
                @endif
                <li><a href="{{ route('search.index') }}">جستجو در سامانه</a></li>
                <li><a href="/sitemap.xml">نقشه سایت</a></li>
                <li><a href="/feed.xml">خوراک خبری (RSS)</a></li>
            </ul>
        </div>

        {{-- تماس --}}
        <div>
            <h3>📞 اطلاعات تماس</h3>
            <ul class="gov-footer-contact">
                <li>
                    <span class="icon">📍</span>
                    <span>
                        @if(isset($section) && $section->address)
                            {{ $section->address }}
                        @else
                            لرستان، شهرستان چگنی، بخشداری
                        @endif
                    </span>
                </li>
                @if(isset($section) && $section->phone)
                    <li>
                        <span class="icon">📞</span>
                        <a href="tel:{{ $section->phone }}">{{ $section->phone }}</a>
                    </li>
                @endif
                @if(isset($section) && $section->email)
                    <li>
                        <span class="icon">✉️</span>
                        <a href="mailto:{{ $section->email }}">{{ $section->email }}</a>
                    </li>
                @endif
                <li>
                    <span class="icon">🌐</span>
                    <a href="https://{{ config('app.base_domain') }}">{{ config('app.base_domain') }}</a>
                </li>
            </ul>
        </div>

    </div>

    {{-- نوار پایین --}}
    <div class="gov-footer-bottom">
        <div class="gov-footer-bottom-inner">
            <div>
                {{ setting('footer_copyright', '© ' . date('Y') . ' — سامانه اطلاع‌رسانی روستاهای چگنی') }}<a href="/">سامانه اطلاع‌رسانی روستاهای چگنی</a>
                محفوظ است.
            </div>
            <div>
                طراحی و توسعه:
                <a href="#" target="_blank" rel="noopener">تیم فنی</a>
            </div>
        </div>
    </div>


        </div>

</footer>


<style>
    /* ============================================
       LIGHT-BLUE-FOOTER-2026 — هماهنگ با هدر
       ============================================ */

    /* فوتر اصلی — آبی روشن مثل هدر */
    .gov-footer,
    .s-footer,
    footer.gov-footer,
    .gov-footer-main {
        background: linear-gradient(135deg, #065f46 0%, #10b981 100%) !important;
        color: #ffffff !important;
        border-top: none !important;
    }

    /* اگر فوتر نوار بالایی دارد */
    .gov-footer-top,
    .gov-footer-header {
        background: rgba(13, 46, 79, 0.25) !important;
        border-bottom: 1px solid rgba(255, 213, 79, 0.3) !important;
    }

    /* عناوین ستون‌ها — طلایی */
    .gov-footer h3,
    .gov-footer h4,
    .gov-footer .footer-title,
    .s-footer h3,
    .s-footer h4 {
        color: #ffd54f !important;
        font-weight: 700 !important;
        text-shadow: 0 1px 3px rgba(13, 46, 79, 0.4) !important;
    }

    /* متن‌ها و لینک‌ها — سفید */
    .gov-footer p,
    .gov-footer span,
    .gov-footer li,
    .gov-footer a,
    .s-footer p,
    .s-footer a,
    .s-footer li {
        color: #e8f0fa !important;
    }

    .gov-footer a:hover,
    .s-footer a:hover {
        color: #ffd54f !important;
    }

    /* خطوط جداکننده — طلایی کم‌رنگ */
    .gov-footer hr,
    .gov-footer .divider {
        border-color: rgba(255, 213, 79, 0.3) !important;
    }

    /* آیکون‌ها */
    .gov-footer .icon,
    .gov-footer i {
        color: #ffd54f !important;
    }

    /* شبکه‌های اجتماعی */
    .gov-footer .social a,
    .gov-footer .social-link {
        background: rgba(255, 213, 79, 0.15) !important;
        border: 1px solid rgba(255, 213, 79, 0.4) !important;
        color: #ffd54f !important;
        transition: all .2s !important;
    }
    .gov-footer .social a:hover,
    .gov-footer .social-link:hover {
        background: #ffd54f !important;
        color: #14497f !important;
    }

    /* لوگوها و نمادها — پس‌زمینه سفید برای خوانایی */
    .gov-footer-logos img,
    .gov-footer .logo img {
        background: #fff !important;
        padding: 6px !important;
        border-radius: 6px !important;
        opacity: 0.95 !important;
    }

    /* آمار فوتر */
    .gov-footer-stats,
    .gov-footer .stats {
        background: rgba(13, 46, 79, 0.25) !important;
        border-radius: 8px !important;
    }
    .gov-footer-stats .number,
    .gov-footer .stat-number {
        color: #ffd54f !important;
        font-weight: 800 !important;
    }
    .gov-footer-stats .label,
    .gov-footer .stat-label {
        color: #e8f0fa !important;
    }

    /* نوار پایین (copyright) — تیره‌تر */
    .gov-footer-bottom,
    .s-footer-bottom {
        background: rgba(13, 46, 79, 0.35) !important;
        border-top: 1px solid rgba(255, 213, 79, 0.25) !important;
        color: #d1fae5 !important;
    }
    .gov-footer-bottom a,
    .s-footer-bottom a {
        color: #ffd54f !important;
    }

    /* پایان LIGHT-BLUE-FOOTER-2026 */
</style>

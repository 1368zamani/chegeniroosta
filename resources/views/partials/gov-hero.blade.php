{{-- ============================================
     Hero با تصویر پل کشکان
     ============================================ --}}

<style>
    .gov-hero {
        position: relative;
        min-height: 320px;
        background: linear-gradient(135deg, #0d3b2e 0%, #14432f 100%);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
    }

    /* تصویر پل کشکان (نقاشی SVG جایگزین) */
    .gov-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse at 20% 40%, rgba(255,213,79,0.15) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 60%, rgba(100,181,246,0.2) 0%, transparent 50%),
            url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 400' preserveAspectRatio='xMidYMid slice'><defs><linearGradient id='sky' x1='0' y1='0' x2='0' y2='1'><stop offset='0' stop-color='%231a3a5c'/><stop offset='1' stop-color='%230d2e4f'/></linearGradient><linearGradient id='river' x1='0' y1='0' x2='0' y2='1'><stop offset='0' stop-color='%232c5a8a'/><stop offset='1' stop-color='%231a3a5c'/></linearGradient><linearGradient id='stone' x1='0' y1='0' x2='0' y2='1'><stop offset='0' stop-color='%23d4a862'/><stop offset='1' stop-color='%238b6d3a'/></linearGradient></defs><rect width='1200' height='400' fill='url(%23sky)'/><circle cx='950' cy='80' r='40' fill='%23ffd54f' opacity='0.4'/><g opacity='0.3' fill='%23ffffff'><circle cx='200' cy='60' r='2'/><circle cx='400' cy='90' r='1.5'/><circle cx='600' cy='50' r='2'/><circle cx='800' cy='80' r='1.5'/><circle cx='300' cy='120' r='1'/></g><path d='M 0,280 Q 300,250 600,270 T 1200,260 L 1200,320 L 0,320 Z' fill='url(%23river)'/><g fill='url(%23stone)'><path d='M 100,260 L 1100,260 L 1100,270 L 100,270 Z'/><path d='M 150,270 L 200,270 L 200,360 L 150,360 Z'/><path d='M 300,270 L 350,270 L 350,360 L 300,360 Z'/><path d='M 450,270 L 500,270 L 500,360 L 450,360 Z'/><path d='M 600,270 L 650,270 L 650,360 L 600,360 Z'/><path d='M 750,270 L 800,270 L 800,360 L 750,360 Z'/><path d='M 900,270 L 950,270 L 950,360 L 900,360 Z'/><path d='M 1050,270 L 1100,270 L 1100,360 L 1050,360 Z'/></g><g fill='%23d4a862' opacity='0.9'><rect x='95' y='255' width='1010' height='10'/><rect x='95' y='240' width='1010' height='8'/></g></svg>");
        background-size: cover;
        background-position: center;
        opacity: 0.35;
        z-index: 1;
    }

    /* لایه تاریک اضافه برای خوانایی */
    .gov-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(13,46,79,0.5) 0%, rgba(13,46,79,0.75) 100%);
        z-index: 2;
    }

    .gov-hero-content {
        position: relative;
        z-index: 3;
        width: min(900px, 92%);
        padding: 60px 20px;
    }

    .gov-hero h2 {
        margin: 0 0 16px;
        font-size: 38px;
        font-weight: 800;
        letter-spacing: -0.5px;
        text-shadow: 0 2px 15px rgba(0,0,0,0.4);
        line-height: 1.3;
    }

    .gov-hero-line {
        width: 80px;
        height: 4px;
        background: #ffd54f;
        margin: 20px auto 24px;
        border-radius: 2px;
        box-shadow: 0 0 20px rgba(255,213,79,0.6);
    }

    .gov-hero p {
        margin: 0;
        font-size: 16px;
        line-height: 2;
        color: rgba(255,255,255,0.9);
        max-width: 700px;
        margin: auto;
    }

    .gov-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255,213,79,0.15);
        border: 1px solid rgba(255,213,79,0.4);
        color: #ffd54f;
        padding: 8px 18px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 20px;
        backdrop-filter: blur(10px);
    }

    @media (max-width: 700px) {
        .gov-hero h2 { font-size: 26px; }
        .gov-hero p { font-size: 14px; }
        .gov-hero-content { padding: 45px 20px; }
        .gov-hero { min-height: 260px; }
    }
</style>

<section class="gov-hero">
    <div class="gov-hero-content">

        <div class="gov-hero-badge">
            🇮🇷 جمهوری اسلامی ایران
        </div>

        <h2>{{ $heroTitle ?? 'سامانه اطلاع‌رسانی روستاهای شهرستان چگنی' }}</h2>

        <div class="gov-hero-line"></div>

        <p>
            {{ $heroSubtitle ?? 'پایگاه رسمی اطلاع‌رسانی بخشداری‌های شهرستان چگنی، استان لرستان — با هدف معرفی روستاها، انتشار اخبار و ارائه خدمات الکترونیکی' }}
        </p>

    </div>
</section>

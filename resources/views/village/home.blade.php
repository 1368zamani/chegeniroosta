<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">

    <title>{{ $village->name }} | روستا نما</title>

    <meta name="description"
          content="{{ $village->description ?: 'معرفی و اطلاع‌رسانی روستای '.$village->name }}">

    <style>
        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: Tahoma, Arial, sans-serif;
            background: #f5f7fa;
            color: #263238;
            line-height: 1.9;
        }

        a {
            text-decoration: none;
        }

        .navbar {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-inner {
            width: min(1150px, 92%);
            margin: auto;
            min-height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand {
            color: #2e7d32;
            font-size: 22px;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            gap: 22px;
            flex-wrap: wrap;
        }

        .nav-links a {
            color: #455a64;
            font-size: 14px;
        }

        .nav-links a:hover {
            color: #2e7d32;
        }

        .hero {
            background: linear-gradient(135deg, #2e7d32, #388e3c);
            color: white;
            padding: 80px 20px;
        }

        .hero-inner {
            width: min(1100px, 92%);
            margin: auto;
        }

        .hero h1 {
            font-size: clamp(30px, 5vw, 52px);
            margin: 0 0 12px;
        }

        .hero p {
            max-width: 720px;
            margin: 0;
            font-size: 17px;
            opacity: .95;
        }

        .location {
            margin-top: 20px;
            font-size: 14px;
            opacity: .9;
        }

        .container {
            width: min(1100px, 92%);
            margin: 45px auto;
        }

        .section {
            margin-bottom: 55px;
        }

        .section-title {
            margin-bottom: 25px;
        }

        .section-title h2 {
            margin: 0 0 5px;
            color: #2e7d32;
        }

        .section-title p {
            margin: 0;
            color: #78909c;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
        }

        .stat {
            background: white;
            border-radius: 14px;
            padding: 24px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,.06);
        }

        .stat-number {
            font-size: 26px;
            font-weight: bold;
            color: #2e7d32;
        }

        .stat-label {
            color: #78909c;
            font-size: 13px;
        }

        .about {
            background: white;
            border-radius: 15px;
            padding: 28px;
            box-shadow: 0 4px 15px rgba(0,0,0,.06);
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .news-card {
            background: white;
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 4px 15px rgba(0,0,0,.06);
        }

        .news-card h3 {
            margin-top: 0;
            color: #2e7d32;
        }

        .news-card p {
            color: #607d8b;
        }

        .gallery {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .gallery img {
            width: 100%;
            height: 190px;
            object-fit: cover;
            border-radius: 12px;
            display: block;
        }

        .contact {
            background: white;
            border-radius: 15px;
            padding: 28px;
            box-shadow: 0 4px 15px rgba(0,0,0,.06);
        }

        .contact p {
            margin: 8px 0;
        }

        footer {
            background: #263238;
            color: #cfd8dc;
            text-align: center;
            padding: 35px 20px;
        }

        .empty {
            background: white;
            border-radius: 12px;
            padding: 25px;
            color: #78909c;
        }

        @media(max-width: 850px) {
            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .news-grid {
                grid-template-columns: 1fr;
            }

            .gallery {
                grid-template-columns: repeat(2, 1fr);
            }

            .nav-inner {
                flex-direction: column;
                padding: 15px 0;
            }
        }

        @media(max-width: 500px) {
            .stats {
                grid-template-columns: 1fr;
            }

            .gallery {
                grid-template-columns: 1fr;
            }

            .hero {
                padding: 55px 20px;
            }
        }
    </style>

    <meta property="og:title" content="{{ $village->name ?? 'روستا نما' }} | روستا نما">
    <meta property="og:description" content="{{ $village->description ?? 'سامانه معرفی و اطلاع‌رسانی روستاهای شهرستان چگنی' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    <link rel="canonical" href="{{ url()->current() }}">

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
@include('partials.site-animations')
@include('partials.pwa')
</head>

<body>

<nav class="navbar">
    <div class="nav-inner">

        <a class="brand" href="/">
            {{ $village->name }} | روستا نما
        </a>

        <div class="nav-links">
            <a href="#about">معرفی</a>
            <a href="#stats">آمار</a>
            <a href="#news">اخبار</a>
            <a href="#gallery">گالری</a>
            <a href="#contact">تماس</a>
        </div>

    </div>
</nav>

<section class="hero">

    <div class="hero-inner">

        <h1>روستای {{ $village->name }}</h1>

        <p>
            {{ $village->description ?: 'به سامانه روستا نما خوش آمدید.' }}
        </p>

        <div class="location">
            استان {{ $village->province ?: 'لرستان' }}
            |
            شهرستان {{ $village->county ?: 'چگنی' }}
            |
            بخش {{ $village->section ?: 'ویسیان' }}
        </div>

    </div>

</section>

<main class="container">

    <section class="section" id="stats">

        <div class="section-title">
            <h2>اطلاعات روستا</h2>
            <p>مشخصات و آمار ثبت‌شده روستا</p>
        </div>

        <div class="stats">

            <div class="stat">
                <div class="stat-number">
                    {{ $village->population ?? '-' }}
                </div>
                <div class="stat-label">جمعیت</div>
            </div>

            <div class="stat">
                <div class="stat-number">
                    {{ $village->households ?? '-' }}
                </div>
                <div class="stat-label">تعداد خانوار</div>
            </div>

            <div class="stat">
                <div class="stat-number">
                    {{ $village->dehyar_name ?: '-' }}
                </div>
                <div class="stat-label">دهیار</div>
            </div>

            <div class="stat">
                <div class="stat-number">
                    {{ $village->council_head_name ?: '-' }}
                </div>
                <div class="stat-label">رئیس شورا</div>
            </div>

        </div>

    </section>

    <section class="section" id="about">

        <div class="section-title">
            <h2>معرفی روستا</h2>
        </div>

        <div class="about">

            <p>
                {{ $village->description ?: 'اطلاعات معرفی این روستا هنوز ثبت نشده است.' }}
            </p>

            @if($village->address)
                <p>
                    <strong>نشانی:</strong>
                    {{ $village->address }}
                </p>
            @endif

        </div>

    </section>

    <section class="section" id="news">

        <div class="section-title">
            <h2>آخرین اخبار</h2>
            <p>اخبار و اطلاعیه‌های روستا</p>
        </div>

        @if($village->news && $village->news->where('is_published', true)->count())

            <div class="news-grid">

                @foreach($village->news->where('is_published', true)->take(6) as $news)

                    <article class="news-card">

                        <h3>{{ $news->title }}</h3>

                        @if($news->excerpt)
                            <p>{{ $news->excerpt }}</p>
                        @else
                            <p>
                                {{ \Illuminate\Support\Str::limit(strip_tags($news->content), 150) }}
                            </p>
                        @endif

                    </article>

                @endforeach

            </div>

        @else

            <div class="empty">
                هنوز خبری برای این روستا منتشر نشده است.
            </div>

        @endif

    </section>

    <section class="section" id="gallery">

        <div class="section-title">
            <h2>گالری تصاویر</h2>
            <p>تصاویر روستای {{ $village->name }}</p>
        </div>

        @if($village->images && $village->images->count())

            <div class="gallery">

                @foreach($village->images->take(8) as $image)

                    <img
                        src="{{ asset('storage/' . $image->image_path) }}"
                        alt="{{ $image->title ?: $village->name }}"
                    >

                @endforeach

            </div>

        @else

            <div class="empty">
                هنوز تصویری برای این روستا ثبت نشده است.
            </div>

        @endif

    </section>

    <section class="section" id="contact">

        <div class="section-title">
            <h2>اطلاعات تماس</h2>
        </div>

        <div class="contact">

            @if($village->phone)
                <p>
                    <strong>تلفن:</strong>
                    {{ $village->phone }}
                </p>
            @endif

            @if($village->email)
                <p>
                    <strong>ایمیل:</strong>
                    {{ $village->email }}
                </p>
            @endif

            @if(!$village->phone && !$village->email)
                <p>
                    اطلاعات تماس هنوز ثبت نشده است.
                </p>
            @endif

        </div>

    </section>

</main>

<footer>
    روستا نما — سامانه معرفی و اطلاع‌رسانی روستاهای شهرستان چگنی
    <br>
    © {{ date('Y') }}
</footer>

</body>
</html>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta name="google-site-verification" content="{{ config('services.google.search_console_verification') }}">
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جستجو: {{ $query }}</title>
    <meta name="robots" content="noindex, follow">
    <meta name="theme-color" content="#1565c0">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #f5f7fa; color: #263238; }

        .header { background: linear-gradient(135deg, #1565c0, #0d47a1); color: white; padding: 30px 20px; }
        .header-inner { width: min(1100px, 92%); margin: auto; text-align: center; }
        .header h1 { margin: 0 0 20px; font-size: 22px; }

        .search-box {
            width: min(600px, 100%);
            margin: auto;
            position: relative;
        }
        .search-box input {
            width: 100%;
            padding: 16px 50px 16px 20px;
            border: none;
            border-radius: 12px;
            font-family: inherit;
            font-size: 15px;
            outline: none;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .search-box button {
            position: absolute;
            left: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: #1565c0;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
        }

        .container { width: min(1100px, 92%); margin: auto; padding: 40px 0; }

        .result-count {
            color: #546e7a;
            font-size: 14px;
            margin-bottom: 30px;
            padding-bottom: 16px;
            border-bottom: 2px solid #e3f2fd;
        }

        .section-title {
            font-size: 18px;
            color: #1565c0;
            margin: 30px 0 18px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e3f2fd;
        }

        .result-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }

        .result-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 18px rgba(0,0,0,.06);
            text-decoration: none;
            color: inherit;
            border-top: 4px solid #1565c0;
            transition: transform .2s;
        }
        .result-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(21,101,192,0.12);
        }
        .result-card h3 { margin: 0 0 8px; color: #1565c0; font-size: 16px; }
        .result-card .meta { color: #90a4ae; font-size: 12px; margin-bottom: 10px; }
        .result-card p { margin: 0; color: #607d8b; font-size: 13px; line-height: 1.9; }

        .empty-result {
            background: white;
            border-radius: 12px;
            padding: 60px 20px;
            text-align: center;
            color: #90a4ae;
        }
        .empty-result .icon { font-size: 60px; margin-bottom: 20px; opacity: .5; }

        .footer { background: #263238; color: #cfd8dc; padding: 30px 20px; text-align: center; font-size: 13px; margin-top: 60px; }
        .footer a { color: #64b5f6; text-decoration: none; }
    </style>
    @include("partials.analytics")
</head>
<body>

<div class="header">
    <div class="header-inner">
        <h1>🔍 جستجو در سامانه</h1>
        <form method="GET" action="{{ route('search.index') }}" class="search-box">
            <input type="text" name="q" value="{{ $query }}" placeholder="نام روستا، بخش، دهیار، یا خبر..." autofocus>
            <button type="submit">جستجو</button>
        </form>
    </div>
</div>

<div class="container">

    @if(mb_strlen($query) < 2)
        <div class="empty-result">
            <div class="icon">🔍</div>
            <p>حداقل ۲ کاراکتر وارد کنید تا جستجو انجام شود.</p>
        </div>
    @else
        @php
            $total = $villages->count() + $sections->count() + $news->count();
        @endphp

        <div class="result-count">
            <strong>{{ $total }}</strong> نتیجه برای «{{ $query }}»
        </div>

        @if($total == 0)
            <div class="empty-result">
                <div class="icon">📭</div>
                <p>نتیجه‌ای یافت نشد. لطفاً عبارت دیگری را امتحان کنید.</p>
            </div>
        @else

            @if($sections->count() > 0)
                <h2 class="section-title">📁 بخش‌ها ({{ $sections->count() }})</h2>
                <div class="result-grid">
                    @foreach($sections as $section)
                        <a href="{{ request()->getScheme() }}://{{ $section->subdomain }}.{{ config('app.base_domain') }}" class="result-card">
                            <h3>{{ $section->name }}</h3>
                            <div class="meta">📍 بخش — {{ $section->county ?? 'چگنی' }}</div>
                            <p>{{ $section->description ?: 'ورود به پایگاه اطلاع‌رسانی بخش' }}</p>
                        </a>
                    @endforeach
                </div>
            @endif

            @if($villages->count() > 0)
                <h2 class="section-title">🏘 روستاها ({{ $villages->count() }})</h2>
                <div class="result-grid">
                    @foreach($villages as $village)
                        <a href="{{ route('public.village.show', $village->slug) }}" class="result-card">
                            <h3>{{ $village->name }}</h3>
                            <div class="meta">
                                📍 {{ $village->section ?? '' }}
                                @if($village->district) — {{ $village->district }} @endif
                            </div>
                            @if($village->dehyar_name)
                                <p style="margin-bottom:6px; font-size:12px;">👤 دهیار: {{ $village->dehyar_name }}</p>
                            @endif
                            @if($village->households || $village->population)
                                <div style="display:flex; gap:8px; font-size:12px; color:#546e7a; margin-top:8px;">
                                    @if($village->households) <span>👥 {{ number_format($village->households) }} خانوار</span> @endif
                                    @if($village->population) <span>🧑 {{ number_format($village->population) }} نفر</span> @endif
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif

            @if($news->count() > 0)
                <h2 class="section-title">📰 اخبار ({{ $news->count() }})</h2>
                <div class="result-grid">
                    @foreach($news as $item)
                        @php $sec = $item->section; @endphp
                        <a href="{{ request()->getScheme() }}://{{ $sec ? $sec->subdomain . '.' . config('app.base_domain') : config('app.base_domain') }}/news/{{ $item->slug }}" class="result-card">
                            <h3>{{ \Illuminate\Support\Str::limit($item->title, 80) }}</h3>
                            <div class="meta">
                                🗓 {{ $item->published_at ? $item->published_at->format('Y/m/d') : $item->created_at->format('Y/m/d') }}
                                @if($sec) — {{ $sec->name }} @endif
                            </div>
                            <p>{{ \Illuminate\Support\Str::limit($item->excerpt ?: strip_tags($item->content), 120) }}</p>
                        </a>
                    @endforeach
                </div>
            @endif

        @endif
    @endif

</div>

<footer class="footer">
    <p style="margin:0 0 8px;">سامانه اطلاع‌رسانی روستاهای شهرستان چگنی</p>
    <p style="margin:0; font-size:12px; opacity:.7;">&copy; {{ date('Y') }} — <a href="/">بازگشت به صفحه اصلی</a></p>
</footer>

@include('partials.back-to-top')

</body>
</html>

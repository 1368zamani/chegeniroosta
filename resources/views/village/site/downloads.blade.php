<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فایل‌های دانلود {{ $village->name }} | پایگاه اطلاع‌رسانی</title>
    <meta name="description" content="فایل‌ها، فرم‌ها و مستندات قابل دانلود روستای {{ $village->name }}">
    <meta name="robots" content="index, follow">
    <meta property="og:site_name" content="دهیاری روستای {{ $village->name }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="فایلهای دانلود روستای {{ $village->name }}">
    <meta property="og:description" content="فایلها، فرمها و مستندات قابل دانلود روستای {{ $village->name }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary">
    <link rel="canonical" href="{{ url()->current() }}">

    <link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
    @include('partials.site-animations')

    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Vazirmatn', Tahoma, Arial, sans-serif;
            background: #fff7e9;
            color: #2b2119;
        }

        .wrap { width: min(1140px, 94%); margin: 24px auto 60px; padding: 0 10px; }

        .back-link {
            display: inline-flex; align-items: center; gap: 7px;
            background: #fff; color: #c94a2b; text-decoration: none;
            padding: 10px 18px; border-radius: 10px; font-size: 13.5px; font-weight: 600;
            border: 1px solid #efe2d1; box-shadow: 0 2px 8px rgba(0,0,0,.04);
            margin-bottom: 20px; transition: background .2s;
        }
        .back-link:hover { background: #fdeee7; }

        .page-title { font-size: 25px; color: #5c2313; margin: 0 0 6px; font-weight: 800; }
        .page-title span { color: #c94a2b; }
        .page-sub { color: #8a7a6a; font-size: 13.5px; margin: 0 0 26px; line-height: 1.9; }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 14px;
        }

        .file-card {
            display: flex; align-items: flex-start; gap: 14px;
            background: #fff; border: 1.5px solid #f2e4cd; border-radius: 13px;
            padding: 16px; text-decoration: none; color: inherit;
            transition: border-color .22s, box-shadow .22s, transform .22s;
        }
        .file-card:hover {
            border-color: #c94a2b;
            box-shadow: 0 10px 28px rgba(201,74,43,.13);
            transform: translateY(-3px);
        }

        .file-icon {
            width: 50px; height: 50px; border-radius: 12px; background: #fdeee7;
            display: flex; align-items: center; justify-content: center;
            font-size: 25px; flex-shrink: 0;
        }

        .file-body { flex: 1; min-width: 0; }
        .file-title {
            font-weight: 700; color: #8e2f18; font-size: 14px;
            line-height: 1.6; margin-bottom: 4px;
        }
        .file-desc {
            font-size: 12px; color: #8a7a6a; line-height: 1.8; margin-bottom: 7px;
        }
        .file-meta {
            display: flex; flex-wrap: wrap; gap: 10px; align-items: center;
            font-size: 11px; color: #a2948a;
        }

        .file-arrow {
            color: #c94a2b; font-size: 19px; align-self: center; flex-shrink: 0;
        }

        .empty {
            background: #fff; border-radius: 14px; padding: 70px 20px;
            text-align: center; box-shadow: 0 4px 16px rgba(0,0,0,.05);
        }
        .empty .icon { font-size: 52px; margin-bottom: 14px; opacity: .5; }
        .empty p { color: #a2948a; font-size: 14px; margin: 0; line-height: 1.9; }

        @media (max-width: 560px) {
            .grid { grid-template-columns: 1fr; }
            .page-title { font-size: 20px; }
            .file-card { padding: 13px; }
            .file-icon { width: 42px; height: 42px; font-size: 21px; }
        }

        @media (prefers-reduced-motion: reduce) {
            .file-card:hover { transform: none !important; }
        }
    </style>
@include('partials.pwa')
    <link rel="stylesheet" href="{{ asset('css/ui-v3.css') }}?v=8">
</head>
<body class="v3-body">
@include('partials.v3-village-nav', ['label' => 'فایل‌های قابل دانلود'])
<div class="wrap">

    <a href="{{ route('public.village.show', $village->slug) }}" class="back-link">← بازگشت به {{ $village->name }}</a>

    <h1 class="page-title">فایل‌های دانلود <span>{{ $village->name }}</span></h1>
    <p class="page-sub">
        فرم‌ها، اطلاعیه‌ها و مستندات روستای {{ $village->name }} — برای دانلود روی هر فایل کلیک کنید.
    </p>

    @if($downloads->count() > 0)
        <div class="grid">
            @foreach($downloads as $d)
                <a href="{{ route('village.site.download', [$village->slug, $d->id]) }}" class="file-card">
                    <div class="file-icon">{{ $d->icon() }}</div>

                    <div class="file-body">
                        <div class="file-title">{{ $d->title }}</div>

                        @if($d->description)
                            <div class="file-desc">{{ $d->description }}</div>
                        @endif

                        <div class="file-meta">
                            <span>📦 {{ $d->sizeLabel() }}</span>
                            @if($d->download_count > 0)
                                <span>⬇️ {{ \App\Support\Number::fa(number_format($d->download_count)) }} بار دانلود</span>
                            @endif
                        </div>
                    </div>

                    <div class="file-arrow">⬇️</div>
                </a>
            @endforeach
        </div>
    @else
        <div class="empty">
            <div class="icon">📥</div>
            <p>هنوز فایلی برای این روستا بارگذاری نشده است.</p>
        </div>
    @endif

</div>

</body>
</html>

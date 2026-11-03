<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->title }} | محصولات {{ $village->name }}</title>
    <meta name="description" content="{{ $product->short_description ?: $product->title }}">

    <link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
    @include('partials.site-animations')

    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Vazirmatn', Tahoma, Arial, sans-serif;
            background: #f5f7fa;
            color: #263238;
        }

        .wrap { width: min(1140px, 94%); margin: 24px auto 60px; padding: 0 10px; }

        .back-link {
            display: inline-flex; align-items: center; gap: 7px;
            background: #fff; color: #2e7d32; text-decoration: none;
            padding: 10px 18px; border-radius: 10px; font-size: 13.5px; font-weight: 600;
            border: 1px solid #dfe7e1; box-shadow: 0 2px 8px rgba(0,0,0,.04);
            margin-bottom: 20px; transition: background .2s;
        }
        .back-link:hover { background: #e8f5e9; }

        /* ── چیدمان دو ستونی ── */
        .product-main {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            align-items: start;
            margin-bottom: 28px;
        }

        /* ── گالری ── */
        .gallery {
            background: #fff; border-radius: 16px; overflow: hidden;
            box-shadow: 0 6px 24px rgba(0,0,0,.07);
            border: 1px solid #eef2ef;
        }
        .gallery-main {
            width: 100%; height: 380px; object-fit: cover; display: block;
            background: #e8f5e9;
        }
        .gallery-main-ph {
            width: 100%; height: 380px; display: flex; align-items: center;
            justify-content: center; font-size: 96px;
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
        }
        .thumbs {
            display: flex; gap: 8px; padding: 10px; overflow-x: auto;
            background: #fafbfa;
        }
        .thumb {
            width: 66px; height: 66px; object-fit: cover; border-radius: 8px;
            cursor: pointer; flex-shrink: 0; border: 2.5px solid transparent;
            transition: border-color .2s, transform .2s;
        }
        .thumb:hover { transform: scale(1.05); }
        .thumb.active { border-color: #2e7d32; }

        /* ── اطلاعات ── */
        .info-card {
            background: #fff; border-radius: 16px; padding: 24px;
            box-shadow: 0 6px 24px rgba(0,0,0,.07);
            border: 1px solid #eef2ef;
        }

        .cat-badge {
            display: inline-block; font-size: 11.5px; color: #2e7d32;
            background: #e8f5e9; padding: 5px 14px; border-radius: 13px;
            margin-bottom: 12px; font-weight: 700;
        }

        .info-card h1 {
            margin: 0 0 14px; font-size: 24px; color: #0d3b2e;
            line-height: 1.5; font-weight: 800;
        }

        .price-row {
            display: flex; align-items: baseline; gap: 10px;
            padding: 16px 0; border-top: 1px solid #f1f5f4;
            border-bottom: 1px solid #f1f5f4; margin-bottom: 16px;
            flex-wrap: wrap;
        }
        .price { font-size: 27px; font-weight: 800; color: #2e7d32; }
        .price-note { font-size: 12px; color: #90a4ae; }

        .specs { display: grid; gap: 11px; margin-bottom: 18px; }
        .spec {
            display: flex; justify-content: space-between; align-items: center;
            gap: 12px; font-size: 13.5px; padding: 9px 13px;
            background: #f8faf9; border-radius: 9px;
        }
        .spec-label { color: #78909c; }
        .spec-value { font-weight: 700; color: #37474f; }

        .status-ok {
            display: inline-block; background: #e8f5e9; color: #2e7d32;
            padding: 4px 13px; border-radius: 11px; font-size: 12.5px; font-weight: 700;
        }
        .status-out {
            display: inline-block; background: #ffebee; color: #c62828;
            padding: 4px 13px; border-radius: 11px; font-size: 12.5px; font-weight: 700;
        }

        .actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 18px; }
        .btn-call {
            flex: 1; min-width: 170px; text-align: center;
            padding: 14px 20px; background: #2e7d32; color: #fff !important;
            text-decoration: none; border-radius: 11px; font-weight: 800;
            font-size: 14.5px; transition: background .2s, transform .2s;
            box-shadow: 0 4px 14px rgba(46,125,50,.25);
        }
        .btn-call:hover { background: #1b5e20; transform: translateY(-2px); }
        .btn-secondary {
            flex: 1; min-width: 140px; text-align: center;
            padding: 14px 20px; background: #f1f5f4; color: #2e7d32 !important;
            text-decoration: none; border-radius: 11px; font-weight: 700;
            font-size: 13.5px; border: 1px solid #dfe7e1;
            transition: background .2s;
        }
        .btn-secondary:hover { background: #e8f5e9; }

        /* ── بخش توضیحات ── */
        .section-card {
            background: #fff; border-radius: 16px; padding: 24px;
            box-shadow: 0 6px 24px rgba(0,0,0,.06);
            border: 1px solid #eef2ef; margin-bottom: 20px;
        }
        .section-card h2 {
            margin: 0 0 16px; font-size: 17px; color: #0d3b2e;
            padding-bottom: 12px; border-bottom: 2px solid #e8f5e9;
            display: flex; align-items: center; gap: 8px;
        }
        .description {
            font-size: 14px; line-height: 2.1; color: #455a64;
            white-space: pre-wrap; margin: 0;
        }

        /* ── فایل‌ها ── */
        .files-list { display: grid; gap: 10px; }
        .file-item {
            display: flex; align-items: center; gap: 13px;
            background: #f8faf9; border: 1px solid #e8eeea;
            border-radius: 11px; padding: 13px 15px;
            text-decoration: none; color: inherit;
            transition: border-color .2s, background .2s, transform .2s;
        }
        .file-item:hover { border-color: #2e7d32; background: #f1f8e9; transform: translateY(-1px); }
        .file-icon {
            width: 42px; height: 42px; border-radius: 10px; background: #e8f5e9;
            display: flex; align-items: center; justify-content: center;
            font-size: 21px; flex-shrink: 0;
        }
        .file-meta { flex: 1; min-width: 0; }
        .file-title { font-size: 13.5px; font-weight: 700; color: #1b5e20; }
        .file-size { font-size: 11.5px; color: #90a4ae; margin-top: 2px; }
        .file-dl {
            font-size: 12.5px; font-weight: 800; color: #2e7d32;
            white-space: nowrap; flex-shrink: 0;
        }

        /* ── محصولات دیگر ── */
        .more-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 14px;
        }
        .more-card {
            background: #fafbfa; border-radius: 12px; overflow: hidden;
            text-decoration: none; color: inherit; border: 1px solid #eef2ef;
            transition: transform .25s, box-shadow .25s;
        }
        .more-card:hover { transform: translateY(-4px); box-shadow: 0 10px 26px rgba(13,59,46,.12); }
        .more-card img { width: 100%; height: 110px; object-fit: cover; display: block; }
        .more-ph {
            width: 100%; height: 110px; display: flex; align-items: center; justify-content: center;
            font-size: 34px; background: #e8f5e9;
        }
        .more-body { padding: 11px; }
        .more-body h4 { margin: 0 0 5px; font-size: 12.5px; color: #0d3b2e; line-height: 1.6; }
        .more-price { font-size: 12px; font-weight: 800; color: #2e7d32; }

        @media (max-width: 820px) {
            .product-main { grid-template-columns: 1fr; gap: 16px; }
            .gallery-main, .gallery-main-ph { height: 260px; }
            .gallery-main-ph { font-size: 68px; }
            .info-card { padding: 18px; }
            .info-card h1 { font-size: 19px; }
            .price { font-size: 22px; }
        }

        @media (max-width: 560px) {
            .gallery-main, .gallery-main-ph { height: 210px; }
            .gallery-main-ph { font-size: 54px; }
            .section-card { padding: 17px; }
            .more-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (prefers-reduced-motion: reduce) {
            .btn-call:hover, .file-item:hover, .more-card:hover, .thumb:hover { transform: none !important; }
        }
    </style>
@include('partials.pwa')
</head>
<body>

<div class="wrap">

    <a href="{{ route('village.site.products', $village->slug) }}" class="back-link">← بازگشت به محصولات {{ $village->name }}</a>

    <div class="product-main">

        {{-- ═══ گالری تصاویر ═══ --}}
        <div class="gallery">
            @php $images = $product->images; @endphp

            @if($images->count() > 0)
                <img id="mainImage"
                     src="{{ asset('storage/' . $images->first()->image) }}"
                     alt="{{ $product->title }}"
                     class="gallery-main">

                @if($images->count() > 1)
                    <div class="thumbs">
                        @foreach($images as $i => $img)
                            <img src="{{ asset('storage/' . $img->image) }}"
                                 alt="تصویر {{ $i + 1 }}"
                                 class="thumb {{ $i === 0 ? 'active' : '' }}"
                                 onclick="changeImage(this, '{{ asset('storage/' . $img->image) }}')">
                        @endforeach
                    </div>
                @endif
            @else
                <div class="gallery-main-ph">{{ $product->categoryIcon() }}</div>
            @endif
        </div>

        {{-- ═══ اطلاعات محصول ═══ --}}
        <div class="info-card">
            @if($product->category)
                <span class="cat-badge">{{ $product->categoryIcon() }} {{ $product->category }}</span>
            @endif

            <h1>{{ $product->title }}</h1>

            <div class="price-row">
                <span class="price">{{ $product->priceLabel() }}</span>
                @if($product->price && $product->unit)
                    <span class="price-note">برای هر {{ $product->unit }}</span>
                @endif
            </div>

            <div class="specs">
                @if($product->quantity)
                    <div class="spec">
                        <span class="spec-label">📦 موجودی</span>
                        <span class="spec-value">{{ $product->quantity }}</span>
                    </div>
                @endif

                <div class="spec">
                    <span class="spec-label">✅ وضعیت</span>
                    <span class="spec-value">
                        @if($product->is_available)
                            <span class="status-ok">موجود برای فروش</span>
                        @else
                            <span class="status-out">فعلاً ناموجود</span>
                        @endif
                    </span>
                </div>

                @if($product->seller_name)
                    <div class="spec">
                        <span class="spec-label">👤 فروشنده</span>
                        <span class="spec-value">{{ $product->seller_name }}</span>
                    </div>
                @endif

                @if($product->delivery_info)
                    <div class="spec">
                        <span class="spec-label">🚚 تحویل</span>
                        <span class="spec-value" style="text-align:left;">{{ $product->delivery_info }}</span>
                    </div>
                @endif

                @if($product->views > 0)
                    <div class="spec">
                        <span class="spec-label">👁 بازدید</span>
                        <span class="spec-value">{{ \App\Support\Number::fa(number_format($product->views)) }}</span>
                    </div>
                @endif
            </div>

            <div class="actions">
                @if($product->seller_phone)
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $product->seller_phone) }}" class="btn-call">
                        📞 تماس با فروشنده
                    </a>
                @endif

                <a href="{{ route('village.site.products', $village->slug) }}" class="btn-secondary">
                    🛒 محصولات دیگر
                </a>
            </div>

            @if($product->seller_phone)
                <div style="text-align:center; margin-top:12px; font-size:12.5px; color:#78909c; direction:ltr;">
                    {{ $product->seller_phone }}
                </div>
            @endif
        </div>
    </div>

    {{-- ═══ توضیحات ═══ --}}
    @if($product->description)
        <div class="section-card">
            <h2>📝 مشخصات و توضیحات</h2>
            <p class="description">{{ $product->description }}</p>
        </div>
    @endif

    {{-- ═══ فایل‌های ضمیمه ═══ --}}
    @if($product->files->count() > 0)
        <div class="section-card">
            <h2>📎 فایل‌های قابل دانلود</h2>
            <div class="files-list">
                @foreach($product->files as $file)
                    <a href="{{ route('village.site.product.file', [$village->slug, $file->id]) }}" class="file-item">
                        <div class="file-icon">{{ $file->icon() }}</div>
                        <div class="file-meta">
                            <div class="file-title">{{ $file->title }}</div>
                            <div class="file-size">
                                {{ $file->sizeLabel() }}
                                @if($file->download_count > 0)
                                    · {{ \App\Support\Number::fa(number_format($file->download_count)) }} بار دانلود
                                @endif
                            </div>
                        </div>
                        <div class="file-dl">⬇️ دانلود</div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ═══ محصولات دیگر ═══ --}}
    @if($more->count() > 0)
        <div class="section-card">
            <h2>🛒 محصولات دیگر {{ $village->name }}</h2>
            <div class="more-grid">
                @foreach($more as $m)
                    @php $mimg = $m->images->first(); @endphp
                    <a href="{{ route('village.site.products.show', [$village->slug, $m->slug]) }}" class="more-card">
                        @if($mimg)
                            <img src="{{ asset('storage/' . $mimg->image) }}" alt="{{ $m->title }}" loading="lazy">
                        @else
                            <div class="more-ph">{{ $m->categoryIcon() }}</div>
                        @endif
                        <div class="more-body">
                            <h4>{{ \Illuminate\Support\Str::limit($m->title, 40) }}</h4>
                            <div class="more-price">{{ $m->priceLabel() }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>

<script>
function changeImage(thumb, src) {
    var main = document.getElementById('mainImage');
    if (main) main.src = src;

    document.querySelectorAll('.thumb').forEach(function (t) {
        t.classList.remove('active');
    });
    thumb.classList.add('active');
}
</script>

</body>
</html>

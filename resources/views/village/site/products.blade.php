<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>محصولات {{ $village->name }} | پایگاه اطلاع‌رسانی</title>
    <meta name="description" content="محصولات محلی و قابل فروش روستای {{ $village->name }}">

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

        .wrap { width: min(1140px, 94%); margin: 26px auto 60px; padding: 0 10px; }

        /* ── نوار بالا ── */
        .top-bar {
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 12px; margin-bottom: 22px;
        }
        .back-link {
            display: inline-flex; align-items: center; gap: 7px;
            background: #fff; color: #2e7d32; text-decoration: none;
            padding: 10px 18px; border-radius: 10px; font-size: 13.5px; font-weight: 600;
            border: 1px solid #dfe7e1; box-shadow: 0 2px 8px rgba(0,0,0,.04);
            transition: background .2s, transform .2s;
        }
        .back-link:hover { background: #e8f5e9; transform: translateY(-1px); }

        .page-title {
            font-size: 25px; color: #0d3b2e; margin: 0 0 6px;
            font-weight: 800;
        }
        .page-title span { color: #2e7d32; }
        .page-sub { color: #78909c; font-size: 13.5px; margin: 0 0 26px; }

        /* ── جست‌وجو و فیلتر ── */
        .filters {
            background: #fff; border-radius: 14px; padding: 16px;
            box-shadow: 0 4px 18px rgba(0,0,0,.05); margin-bottom: 24px;
        }
        .search-row { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 14px; }
        .search-row input[type="text"] {
            flex: 1; min-width: 200px; padding: 11px 16px;
            border: 1.5px solid #dfe7e1; border-radius: 9px;
            font-family: inherit; font-size: 13.5px;
        }
        .search-row button {
            padding: 11px 24px; background: #2e7d32; color: #fff;
            border: none; border-radius: 9px; font-family: inherit;
            font-size: 13.5px; font-weight: 700; cursor: pointer;
            transition: background .2s;
        }
        .search-row button:hover { background: #1b5e20; }

        .chips { display: flex; gap: 8px; flex-wrap: wrap; }
        .chip {
            padding: 7px 15px; border-radius: 20px; font-size: 12.5px;
            text-decoration: none; background: #f1f5f4; color: #4b6b52;
            border: 1px solid transparent; transition: all .2s;
        }
        .chip:hover { background: #e8f5e9; }
        .chip.active { background: #2e7d32; color: #fff; font-weight: 700; }

        /* ── شبکه‌ی محصولات ── */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 18px;
        }

        .p-card {
            background: #fff; border-radius: 14px; overflow: hidden;
            box-shadow: 0 4px 16px rgba(0,0,0,.06);
            text-decoration: none; color: inherit;
            display: flex; flex-direction: column;
            transition: transform .28s cubic-bezier(.16,1,.3,1), box-shadow .28s;
            border: 1px solid #eef2ef;
            position: relative;
        }
        .p-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 38px rgba(13,59,46,.14);
        }

        .p-thumb {
            width: 100%; height: 175px; object-fit: cover; display: block;
            background: #e8f5e9;
        }
        .p-thumb-ph {
            width: 100%; height: 175px; display: flex; align-items: center;
            justify-content: center; font-size: 52px;
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
        }

        .p-body { padding: 15px; display: flex; flex-direction: column; flex: 1; }

        .p-cat {
            display: inline-block; font-size: 10.5px; color: #2e7d32;
            background: #e8f5e9; padding: 3px 10px; border-radius: 11px;
            margin-bottom: 8px; align-self: flex-start;
        }

        .p-body h3 {
            margin: 0 0 7px; font-size: 15px; color: #0d3b2e;
            line-height: 1.6; font-weight: 700;
        }

        .p-desc {
            color: #607d8b; font-size: 12.5px; line-height: 1.85;
            margin: 0 0 12px; flex: 1;
        }

        .p-foot {
            display: flex; justify-content: space-between; align-items: center;
            gap: 8px; flex-wrap: wrap; padding-top: 11px;
            border-top: 1px solid #f1f5f4;
        }
        .p-price { font-size: 14px; font-weight: 800; color: #2e7d32; }
        .p-price small { font-size: 10.5px; font-weight: 500; color: #78909c; }

        .p-badge {
            position: absolute; top: 11px; right: 11px;
            background: #ffd54f; color: #6d4c00;
            font-size: 10.5px; font-weight: 800;
            padding: 4px 11px; border-radius: 11px;
            box-shadow: 0 2px 8px rgba(0,0,0,.12);
        }
        .p-badge-sold {
            position: absolute; top: 11px; left: 11px;
            background: #e53935; color: #fff;
            font-size: 10.5px; font-weight: 800;
            padding: 4px 11px; border-radius: 11px;
        }
        .p-files {
            position: absolute; bottom: 11px; left: 11px;
            background: rgba(255,255,255,.94); color: #2e7d32;
            font-size: 10.5px; font-weight: 700;
            padding: 3px 9px; border-radius: 10px;
        }

        /* ── حالت خالی ── */
        .empty {
            background: #fff; border-radius: 14px; padding: 70px 20px;
            text-align: center; box-shadow: 0 4px 16px rgba(0,0,0,.05);
        }
        .empty .icon { font-size: 52px; margin-bottom: 14px; opacity: .5; }
        .empty p { color: #90a4ae; font-size: 14px; margin: 0; }

        /* ── صفحه‌بندی ── */
        .pagination { margin-top: 30px; display: flex; justify-content: center; }
        .pagination nav { display: flex; gap: 6px; flex-wrap: wrap; }
        .pagination a, .pagination span {
            padding: 8px 14px; border-radius: 8px; background: #fff;
            color: #2e7d32; text-decoration: none; font-size: 13px;
            box-shadow: 0 2px 8px rgba(0,0,0,.05);
        }
        .pagination .current { background: #2e7d32; color: #fff; font-weight: 700; }

        @media (max-width: 640px) {
            .grid { grid-template-columns: repeat(auto-fill, minmax(155px, 1fr)); gap: 12px; }
            .p-thumb, .p-thumb-ph { height: 130px; }
            .p-thumb-ph { font-size: 38px; }
            .p-body { padding: 11px; }
            .p-body h3 { font-size: 13px; }
            .p-desc { font-size: 11.5px; }
            .p-price { font-size: 12.5px; }
            .page-title { font-size: 20px; }
        }

        @media (prefers-reduced-motion: reduce) {
            .p-card:hover, .back-link:hover { transform: none !important; }
        }
    </style>
@include('partials.pwa')
</head>
<body>

<div class="wrap">

    <div class="top-bar">
        <a href="{{ route('public.village.show', $village->slug) }}" class="back-link">← بازگشت به {{ $village->name }}</a>
    </div>

    <h1 class="page-title">🛒 محصولات <span>{{ $village->name }}</span></h1>
    <p class="page-sub">محصولات محلی و قابل فروش روستای {{ $village->name }} — برای خرید با فروشنده تماس بگیرید.</p>

    {{-- جست‌وجو و فیلتر --}}
    @if($products->count() > 0 || $category !== '' || $search !== '')
        <div class="filters">
            <form method="GET" action="{{ route('village.site.products', $village->slug) }}" class="search-row">
                <input type="text" name="q" value="{{ $search }}" placeholder="جست‌وجو در محصولات…">
                @if($category !== '')
                    <input type="hidden" name="category" value="{{ $category }}">
                @endif
                <button type="submit">🔍 جست‌وجو</button>
            </form>

            @if($categories->count() > 0)
                <div class="chips">
                    <a href="{{ route('village.site.products', array_filter(['slug' => $village->slug, 'q' => $search ?: null])) }}"
                       class="chip {{ $category === '' ? 'active' : '' }}">همه</a>
                    @foreach($categories as $cat)
                        <a href="{{ route('village.site.products', array_filter(['slug' => $village->slug, 'category' => $cat, 'q' => $search ?: null])) }}"
                           class="chip {{ $category === $cat ? 'active' : '' }}">{{ $cat }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- شبکه‌ی محصولات --}}
    @if($products->count() > 0)
        <div class="grid">
            @foreach($products as $p)
                @php $img = $p->images->first(); @endphp
                <a href="{{ route('village.site.products.show', [$village->slug, $p->slug]) }}" class="p-card">
                    @if($p->is_featured)
                        <span class="p-badge">⭐ ویژه</span>
                    @endif
                    @if(! $p->is_available)
                        <span class="p-badge-sold">ناموجود</span>
                    @endif

                    @if($img)
                        <img src="{{ asset('storage/' . $img->image) }}" alt="{{ $p->title }}" class="p-thumb" loading="lazy">
                    @else
                        <div class="p-thumb-ph">{{ $p->categoryIcon() }}</div>
                    @endif

                    @if($p->files->count() > 0)
                        <span class="p-files">📎 {{ \App\Support\Number::fa($p->files->count()) }} فایل</span>
                    @endif

                    <div class="p-body">
                        @if($p->category)
                            <span class="p-cat">{{ $p->category }}</span>
                        @endif

                        <h3>{{ $p->title }}</h3>

                        @if($p->short_description)
                            <p class="p-desc">{{ \Illuminate\Support\Str::limit($p->short_description, 85) }}</p>
                        @endif

                        <div class="p-foot">
                            <span class="p-price">{{ $p->priceLabel() }}</span>
                            @if($p->unit)
                                <small style="color:#90a4ae; font-size:11px;">{{ $p->unit }}</small>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="pagination">
            {{ $products->links() }}
        </div>
    @else
        <div class="empty">
            <div class="icon">🛒</div>
            <p>
                @if($search !== '' || $category !== '')
                    محصولی با این مشخصات پیدا نشد.
                @else
                    هنوز محصولی برای این روستا ثبت نشده است.
                @endif
            </p>
        </div>
    @endif

</div>

</body>
</html>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اخبار {{ $village->name }} | پایگاه اطلاع‌رسانی</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Vazirmatn', Tahoma, Arial, sans-serif; background: #f5f7fa; color: #263238; }
        .wrap { width: min(1100px, 94%); margin: 30px auto; padding: 0 10px; }
        .page-title { font-size: 26px; color: #0d3b2e; margin: 0 0 25px; padding-bottom: 15px; border-bottom: 3px solid #ffd54f; }
        .page-title span { color: #2e7d32; }
        .news-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .news-card {
            background: white; border-radius: 14px; overflow: hidden;
            box-shadow: 0 4px 18px rgba(0,0,0,.06);
            transition: transform .25s ease, box-shadow .25s ease;
            display: flex; flex-direction: column;
        }
        .news-card:hover { transform: translateY(-5px); box-shadow: 0 14px 35px rgba(13,59,46,.15); }
        .news-thumb { width: 100%; height: 200px; object-fit: cover; display: block; background: #e8f5e9; }
        .news-thumb-placeholder {
            width: 100%; height: 200px; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9); font-size: 60px; color: #90a4ae;
        }
        .news-body { padding: 20px; display: flex; flex-direction: column; flex: 1; }
        .news-meta { font-size: 12px; color: #78909c; margin-bottom: 8px; display: flex; gap: 12px; flex-wrap: wrap; }
        .news-meta .pinned { background: #ffd54f; color: #0d3b2e; padding: 2px 8px; border-radius: 12px; font-weight: 700; }
        .news-body h3 { margin: 0 0 10px; font-size: 17px; color: #0d3b2e; line-height: 1.6; }
        .news-body h3 a { color: inherit; text-decoration: none; }
        .news-body h3 a:hover { color: #2e7d32; }
        .news-excerpt { color: #546e7a; font-size: 13.5px; line-height: 1.9; margin: 0 0 15px; flex: 1; }
        .news-btn {
            display: inline-block; padding: 9px 18px; background: #0d3b2e; color: #fff;
            text-decoration: none; border-radius: 8px; font-size: 13px; font-weight: 600;
            align-self: flex-start; transition: background .2s;
        }
        .news-btn:hover { background: #ffd54f; color: #0d3b2e; }
        .pagination { margin-top: 30px; display: flex; justify-content: center; }
        .pagination a, .pagination span {
            display: inline-block; padding: 8px 14px; margin: 0 4px; border-radius: 6px;
            background: white; color: #0d3b2e; text-decoration: none; font-size: 13px;
            box-shadow: 0 2px 6px rgba(0,0,0,.06);
        }
        .pagination .active { background: #0d3b2e; color: #fff; }
        .empty-box {
            background: white; padding: 60px 20px; text-align: center;
            border-radius: 14px; color: #90a4ae; font-size: 15px;
        }
        .empty-box .icon { font-size: 60px; margin-bottom: 15px; }
        @media (max-width: 700px) {
            .page-title { font-size: 20px; }
            .news-grid { grid-template-columns: 1fr; }
        }
    </style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
@include('partials.site-animations')
@include('partials.pwa')
</head>
<body>
@include('partials.gov-header', ['headerTitle' => 'اخبار روستای ' . $village->name])

<div class="wrap">
    <h1 class="page-title">📰 اخبار و اطلاعیه‌های <span>{{ $village->name }}</span></h1>

    @if($news->count() > 0)
        <div class="news-grid">
            @foreach($news as $item)
                <div class="news-card">
                    @if($item->image)
                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="news-thumb" loading="lazy">
                    @else
                        <div class="news-thumb-placeholder">📰</div>
                    @endif
                    <div class="news-body">
                        <div class="news-meta">
                            @if($item->is_pinned)<span class="pinned">📌 سنجاق شده</span>@endif
                            <span>📅 {{ $item->published_at ? jalali($item->published_at) : jalali($item->created_at) }}</span>
                            <span title="تعداد بازدید">👁 {{ \App\Support\Number::fa(number_format($item->stat_views)) }} بازدید</span>
                        </div>
                        <h3><a href="{{ route('village.site.news.show', [$village->slug, $item->slug]) }}">{{ $item->title }}</a></h3>
                        @if($item->excerpt)
                            <p class="news-excerpt">{{ \Illuminate\Support\Str::limit($item->excerpt, 140) }}</p>
                        @else
                            <p class="news-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($item->body), 140) }}</p>
                        @endif
                        <a href="{{ route('village.site.news.show', [$village->slug, $item->slug]) }}" class="news-btn">ادامه مطلب →</a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pagination">{{ $news->links() }}</div>
    @else
        <div class="empty-box">
            <div class="icon">📰</div>
            <p>هنوز خبری برای این روستا ثبت نشده است.</p>
        </div>
    @endif
</div>

@include('partials.gov-footer')
</body>
</html>

<style>
    /* Fix Invisible News Text & Cards */
    .news-card {
        background-color: #ffffff !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 8px !important;
        margin-bottom: 15px !important;
        display: flex !important;
        gap: 15px !important;
        padding: 15px !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important;
    }
    .news-card .news-body {
        color: #333333 !important;
        width: 100% !important;
    }
    .news-card .news-body h3 a {
        color: #1f2937 !important;
        font-weight: bold !important;
        text-decoration: none !important;
        font-size: 18px !important;
    }
    .news-card .news-body h3 a:hover {
        color: #c62828 !important;
    }
    .news-card .news-body .news-excerpt {
        color: #4b5563 !important;
        font-size: 14px !important;
        line-height: 1.6 !important;
        margin: 10px 0 !important;
    }
    .news-card .news-meta {
        color: #6b7280 !important;
        font-size: 13px !important;
        display: flex !important;
        gap: 15px !important;
        margin-bottom: 10px !important;
    }
    .news-card .news-btn {
        display: inline-block !important;
        background-color: #10b981 !important;
        color: white !important;
        padding: 8px 18px !important;
        border-radius: 6px !important;
        text-decoration: none !important;
        font-size: 14px !important;
        margin-top: 10px !important;
        transition: 0.3s !important;
    }
    .news-card .news-btn:hover {
        background-color: #059669 !important;
    }
    .news-thumb {
        width: 150px !important;
        height: 150px !important;
        object-fit: cover !important;
        border-radius: 8px !important;
    }

    /* Fix Pagination Style */
    .pagination {
        margin-top: 30px !important;
        display: flex !important;
        justify-content: center !important;
        width: 100% !important;
    }
    .pagination nav {
        width: 100% !important;
    }
    .pagination nav > div:first-child {
        text-align: center !important;
        margin-bottom: 15px !important;
        color: #4b5563 !important;
        font-size: 14px !important;
    }
    .pagination ul {
        display: flex !important;
        justify-content: center !important;
        gap: 8px !important;
        padding: 0 !important;
        list-style: none !important;
        flex-wrap: wrap !important;
    }
    .pagination li a, .pagination li span {
        display: inline-block !important;
        padding: 8px 14px !important;
        background: #f3f4f6 !important;
        border: 1px solid #d1d5db !important;
        border-radius: 6px !important;
        color: #374151 !important;
        text-decoration: none !important;
        font-size: 14px !important;
    }
    .pagination li a:hover {
        background: #10b981 !important;
        color: white !important;
        border-color: #10b981 !important;
    }
    .pagination li.active span, .pagination li[aria-current="page"] span {
        background: #10b981 !important;
        color: white !important;
        border-color: #10b981 !important;
        font-weight: bold !important;
    }
    .pagination svg {
        width: 16px !important;
        height: 16px !important;
        display: inline-block !important;
    }
    @media (max-width: 768px) {
        .news-card { flex-direction: column !important; }
        .news-thumb { width: 100% !important; height: 200px !important; }
    }
</style>

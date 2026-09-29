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
        .page-title { font-size: 26px; color: #0d2e4f; margin: 0 0 25px; padding-bottom: 15px; border-bottom: 3px solid #ffd54f; }
        .page-title span { color: #1565c0; }
        .news-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .news-card {
            background: white; border-radius: 14px; overflow: hidden;
            box-shadow: 0 4px 18px rgba(0,0,0,.06);
            transition: transform .25s ease, box-shadow .25s ease;
            display: flex; flex-direction: column;
        }
        .news-card:hover { transform: translateY(-5px); box-shadow: 0 14px 35px rgba(13,46,79,.15); }
        .news-thumb { width: 100%; height: 200px; object-fit: cover; display: block; background: #e3f2fd; }
        .news-thumb-placeholder {
            width: 100%; height: 200px; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #e3f2fd, #bbdefb); font-size: 60px; color: #90a4ae;
        }
        .news-body { padding: 20px; display: flex; flex-direction: column; flex: 1; }
        .news-meta { font-size: 12px; color: #78909c; margin-bottom: 8px; display: flex; gap: 12px; flex-wrap: wrap; }
        .news-meta .pinned { background: #ffd54f; color: #0d2e4f; padding: 2px 8px; border-radius: 12px; font-weight: 700; }
        .news-body h3 { margin: 0 0 10px; font-size: 17px; color: #0d2e4f; line-height: 1.6; }
        .news-body h3 a { color: inherit; text-decoration: none; }
        .news-body h3 a:hover { color: #1565c0; }
        .news-excerpt { color: #546e7a; font-size: 13.5px; line-height: 1.9; margin: 0 0 15px; flex: 1; }
        .news-btn {
            display: inline-block; padding: 9px 18px; background: #0d2e4f; color: #fff;
            text-decoration: none; border-radius: 8px; font-size: 13px; font-weight: 600;
            align-self: flex-start; transition: background .2s;
        }
        .news-btn:hover { background: #ffd54f; color: #0d2e4f; }
        .pagination { margin-top: 30px; display: flex; justify-content: center; }
        .pagination a, .pagination span {
            display: inline-block; padding: 8px 14px; margin: 0 4px; border-radius: 6px;
            background: white; color: #0d2e4f; text-decoration: none; font-size: 13px;
            box-shadow: 0 2px 6px rgba(0,0,0,.06);
        }
        .pagination .active { background: #0d2e4f; color: #fff; }
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

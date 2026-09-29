<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $news->title }} | {{ $village->name }}</title>
    <meta name="description" content="{{ $news->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($news->body), 150) }}">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Vazirmatn', Tahoma, Arial, sans-serif; background: #f5f7fa; color: #263238; }
        .wrap { width: min(900px, 94%); margin: 30px auto 60px; padding: 0 10px; }
        .back-link {
            display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px;
            background: white; color: #0d2e4f; text-decoration: none; border-radius: 8px;
            font-size: 13px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,.06);
        }
        .back-link:hover { background: #0d2e4f; color: #fff; }
        .article {
            background: white; border-radius: 16px; overflow: hidden;
            box-shadow: 0 6px 30px rgba(0,0,0,.08);
        }
        .article-cover {
            width: 100%; max-height: 500px; object-fit: cover; display: block;
        }
        .article-body { padding: 35px 40px; }
        .article-body h1 {
            color: #0d2e4f; font-size: 28px; line-height: 1.5;
            margin: 0 0 20px; padding-bottom: 20px;
            border-bottom: 3px solid #ffd54f;
        }
        .article-meta {
            display: flex; flex-wrap: wrap; gap: 18px;
            color: #78909c; font-size: 13px; margin-bottom: 25px;
        }
        .article-meta span { display: inline-flex; align-items: center; gap: 5px; }
        .article-excerpt {
            background: #e3f2fd; color: #1565c0;
            padding: 15px 20px; border-right: 4px solid #1565c0;
            border-radius: 8px; font-size: 14.5px; line-height: 2; margin-bottom: 25px;
        }
        .article-content {
            font-size: 15.5px; line-height: 2.2; color: #37474f;
            white-space: pre-line; word-wrap: break-word;
        }
        .news-images-gallery {
            margin-top: 40px; padding-top: 30px; border-top: 2px dashed #e2e8f0;
        }
        .news-images-gallery h3 {
            color: #0d2e4f; font-size: 18px; margin: 0 0 20px;
            display: flex; align-items: center; gap: 8px;
        }
        .gallery-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 14px;
        }
        .gallery-item {
            border-radius: 12px; overflow: hidden; cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,.08);
            transition: transform .25s ease, box-shadow .25s ease;
            background: white;
        }
        .gallery-item:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(13,46,79,.18); }
        .gallery-item img {
            width: 100%; height: 180px; object-fit: cover; display: block;
        }
        .gallery-item .caption {
            padding: 10px 12px; font-size: 12.5px; color: #546e7a; text-align: center;
            background: #f8fafc;
        }
        .related-box { margin-top: 50px; }
        .related-box h3 {
            color: #0d2e4f; font-size: 19px; margin: 0 0 20px;
            padding-bottom: 12px; border-bottom: 2px solid #ffd54f;
        }
        .related-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 15px; }
        .related-card {
            background: white; border-radius: 12px; overflow: hidden;
            text-decoration: none; color: inherit;
            box-shadow: 0 3px 12px rgba(0,0,0,.06);
            transition: transform .25s ease;
        }
        .related-card:hover { transform: translateY(-3px); }
        .related-card img { width: 100%; height: 130px; object-fit: cover; display: block; }
        .related-card .body { padding: 12px 14px; font-size: 13.5px; line-height: 1.6; color: #0d2e4f; font-weight: 600; }
        @media (max-width: 700px) {
            .article-body { padding: 20px 18px; }
            .article-body h1 { font-size: 20px; }
            .gallery-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
            .gallery-item img { height: 120px; }
        }
    </style>
</head>
<body>
@include('partials.gov-header', ['headerTitle' => 'اخبار روستای ' . $village->name])

<div class="wrap">
    <a href="{{ route('village.site.news', $village->slug) }}" class="back-link">→ بازگشت به اخبار</a>

    <article class="article">
        @if($news->image)
            <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}" class="article-cover">
        @endif

        <div class="article-body">
            <h1>{{ $news->title }}</h1>

            <div class="article-meta">
                <span>📅 {{ $news->published_at ? jalali($news->published_at) : jalali($news->created_at) }}</span>
                @if($news->is_pinned)<span style="color:#f57f17;">📌 سنجاق شده</span>@endif
                @if($news->user)<span>✍️ {{ $news->user->name }}</span>@endif
            </div>

            @if($news->excerpt)
                <div class="article-excerpt">{{ $news->excerpt }}</div>
            @endif

            <div class="article-content">{{ $news->body }}</div>

            {{-- گالری چند عکس --}}
            @if($news->images && $news->images->count() > 0)
                <div class="news-images-gallery">
                    <h3>🖼 گالری تصاویر این خبر ({{ $news->images->count() }})</h3>
                    <div class="gallery-grid">
                        @foreach($news->images as $img)
                            <div class="gallery-item" onclick="openFullImage('{{ asset('storage/' . $img->image) }}')">
                                <img src="{{ asset('storage/' . $img->image) }}"
                                     alt="{{ $img->caption ?? $news->title }}"
                                     loading="lazy">
                                @if($img->caption)
                                    <div class="caption">{{ $img->caption }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </article>

    @if(isset($related) && $related->count() > 0)
        <div class="related-box">
            <h3>📰 اخبار مرتبط</h3>
            <div class="related-grid">
                @foreach($related as $rel)
                    <a href="{{ route('village.site.news.show', [$village->slug, $rel->slug]) }}" class="related-card">
                        @if($rel->image)
                            <img src="{{ asset('storage/' . $rel->image) }}" alt="{{ $rel->title }}" loading="lazy">
                        @endif
                        <div class="body">{{ $rel->title }}</div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>
function openFullImage(url) {
    var w = window.open('', '_blank');
    w.document.write('<html><head><title>تصویر</title><style>body{margin:0;background:#000;display:flex;align-items:center;justify-content:center;min-height:100vh;}img{max-width:95vw;max-height:95vh;border-radius:8px;}</style></head><body><img src="' + url + '"></body></html>');
    w.document.close();
}
</script>

@include('partials.gov-footer')
</body>
</html>

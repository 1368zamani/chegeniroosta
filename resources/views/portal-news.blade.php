@php
    $__fa = fn($n) => \App\Support\Number::fa($n);
@endphp
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اخبار بخش‌ها | پایگاه اطلاعرسانی بخش‌های شهرستان چگنی</title>
    <meta name="description" content="آخرین اخبار و اطلاعیههای بخشها و روستاهای شهرستان چگنی در پرتال چگنی.">
    <meta name="robots" content="index, follow">
    <meta property="og:site_name" content="پرتال چگنی">
    <meta property="og:type" content="website">
    <meta property="og:title" content="اخبار بخشها | پرتال چگنی">
    <meta property="og:description" content="آخرین اخبار و اطلاعیههای بخشها و روستاهای شهرستان چگنی در پرتال چگنی.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="stylesheet" href="{{ asset('css/ui-v3.css') }}?v=9">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body class="v3-body">
@include('partials.gov-header', ['headerTitle' => 'پایگاه اطلاعرسانی بخش‌های شهرستان چگنی'])

<div class="v3-wrap">
    <div class="crumbs">
        <a href="{{ url('/') }}">پرتال چگنی</a> / <b>اخبار همهٔ بخش‌ها</b>
    </div>

    <section class="block">
        <header class="block-head">
            <div>
                <span class="kicker-line">اخبار</span>
                <h1>آخرین اخبار</h1>
                <p>همهٔ اخبار منتشرشده در بخش‌ها و روستاهای شهرستان چگنی</p>
            </div>
        </header>
        <form method="GET" action="{{ route('portal.news.index') }}" class="filters">
            <input type="text" name="s" value="{{ request()->query('s') }}" placeholder="جستجو در اخبار..." class="search-input">
            
            <button type="submit">اعمال</button>
        </form>
    </section>

    <section class="block" id="news-list">
        <div class="news-grid">
            @forelse($news as $item)
                <a class="news-card" href="{{ $item->section?->subdomain ? 'https://' . $item->section->subdomain . '.' . config('app.base_domain') . '/news/' . $item->slug : url('/news/' . $item->slug) }}">
                    <span class="cat">{{ $item->section->name ?? 'بخش' }}</span>
                    <h3>{{ $item->title }}</h3>
                    @if($item->excerpt)
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($item->excerpt), 140) }}</p>
                    @endif
                    <div class="meta">
                        <span>{{ jalali($item->published_at ?: $item->created_at) }}</span>
                        
                    </div>
                </a>
            @empty
                <div class="empty-note">خبری منتشر نشده است.</div>
            @endforelse
        </div>
        @if($news->hasPages())
            <div class="pagination-wrap">{{ $news->links() }}</div>
        @endif
    </section>
</div>
@include('partials.pwa')
</body>
</html>
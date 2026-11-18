<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پایگاه اطلاع‌رسانی بخش‌های شهرستان چگنی | استان لرستان</title>
    <meta name="description" content="سامانه رسمی معرفی و اطلاع‌رسانی بخش‌های شهرستان چگنی، استان لرستان">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#c94a2b">

    <meta property="og:site_name" content="سامانه اطلاع‌رسانی چگنی">
    <meta property="og:type" content="website">
    <meta property="og:title" content="پایگاه اطلاع‌رسانی بخش‌های شهرستان چگنی">
    <meta property="og:description" content="سامانه رسمی معرفی و اطلاع‌رسانی بخش‌های شهرستان چگنی">
    <meta property="og:url" content="{{ url()->current() }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="stylesheet" href="{{ asset('css/ui-v3.css') }}?v=3">
    @include('partials.pwa')
</head>
<body class="v3-body">

@include('partials.gov-header')

<main>
    <div class="v3-wrap">

        {{-- ══════════ هیرو ══════════ --}}
        <section class="hero">
            <div class="hero-copy reveal">
                <span class="eyebrow"><i></i> سامانهٔ رسمی اطلاع‌رسانی · استان لرستان</span>
                <h1>به <em>چگنی</em> خوش آمدید</h1>
                <p class="lede">یک پایگاه واحد برای اخبار، خدمات و معرفی روستاها. هر بخش صفحهٔ مستقل خود را دارد و هر روستا نشانی دائمی.</p>

                <form class="search" role="search" action="{{ route('search.index') }}" method="GET">
                    <svg><use href="#i-search"/></svg>
                    <input type="search" name="q" placeholder="جستجو در روستاها، اخبار و خدمات…" aria-label="جستجو">
                    <button class="btn btn-primary" type="submit">جستجو</button>
                </form>

                <div class="hero-tags">
                    <span class="tag"><svg><use href="#i-gov"/></svg> {{ \App\Support\Number::fa(number_format($sections->count())) }} بخش</span>
                    <span class="tag"><svg><use href="#i-homes"/></svg>
                        {{ \App\Support\Number::fa(number_format($sections->sum(fn($s) => $s->villages()->count()))) }} روستا
                    </span>
                    <span class="tag gold"><svg><use href="#i-news"/></svg>
                        {{ \App\Support\Number::fa(number_format($newsTotal ?? ($news->count() ?? 0))) }} خبر
                    </span>
                </div>
            </div>

            <div class="hero-art reveal" style="--d:120ms">
                <div class="arch">
                    <div class="arch-medallion">
                        <div class="ring"><span>چ</span></div>
                        <b>شهرستان چگنی</b>
                        <small>استان لرستان</small>
                    </div>
                </div>
                <div class="float-card fc-1"><b>{{ \App\Support\Number::fa(number_format($sections->count())) }}</b><span>بخش</span></div>
                <div class="float-card fc-2"><b>{{ \App\Support\Number::fa(number_format($sections->sum(fn($s) => $s->villages()->count()))) }}</b><span>روستا</span></div>
            </div>
        </section>

        {{-- ══════════ آخرین اخبار بخش‌ها ══════════ --}}
        <section class="block" id="news" aria-labelledby="newsTitle">
            <header class="block-head reveal">
                <div>
                    <span class="kicker-line">تازه‌ترین‌ها</span>
                    <h2 id="newsTitle">آخرین اخبار بخش‌ها</h2>
                    <p>خبرهای همهٔ بخش‌های شهرستان چگنی — به ترتیب تاریخ انتشار</p>
                </div>
                <a class="link-more" href="#news">همهٔ اخبار <svg><use href="#i-arrow"/></svg></a>
            </header>

            @php
                $__featured = ($news ?? collect())->first();
                $__rest = ($news ?? collect())->slice(1, 6);
                $__newsUrl = function ($item) {
                    $sec = $item->section;
                    if ($sec && $sec->subdomain) {
                        return request()->getScheme() . '://' . $sec->subdomain . '.' . config('app.base_domain') . '/news/' . $item->slug;
                    }
                    return null;
                };
            @endphp

            @if($__featured)
                <section class="feature reveal" aria-label="خبر ویژه">
                    <a class="feature-main" href="{{ $__newsUrl($__featured) ?: '#' }}">
                        @if($__featured->image)
                            <span class="bg" style="background-image:url('{{ asset('storage/' . $__featured->image) }}')"></span>
                        @else
                            <span class="bg"></span>
                        @endif
                        <span class="veil"></span>
                        <div class="copy">
                            @if($__featured->section)
                                <span class="chip"><svg><use href="#i-pin"/></svg> بخش {{ $__featured->section->name }}</span>
                            @endif
                            <h2>{{ $__featured->title }}</h2>
                            @if($__featured->excerpt)
                                <p>{{ \Illuminate\Support\Str::limit(strip_tags($__featured->excerpt), 160) }}</p>
                            @endif
                            <div class="meta">
                                @if($__featured->published_at)
                                    <span><svg><use href="#i-calendar"/></svg> {{ \App\Support\Number::fa(\Morilog\Jalali\Jalalian::fromCarbon($__featured->published_at)->format('Y/m/d')) }}</span>
                                @endif
                                @if(($__featured->stat_views ?? 0) > 0)
                                    <span><svg><use href="#i-eye"/></svg> {{ \App\Support\Number::fa(number_format($__featured->stat_views)) }} بازدید</span>
                                @endif
                            </div>
                        </div>
                    </a>

                    <div class="feature-side">
                        @foreach($__rest->take(3) as $mini)
                            <a class="mini" href="{{ $__newsUrl($mini) ?: '#' }}">
                                <span class="mimg" @if($mini->image) style="background-image:url('{{ asset('storage/' . $mini->image) }}')" @endif></span>
                                <span class="mbody">
                                    <h3>{{ \Illuminate\Support\Str::limit($mini->title, 90) }}</h3>
                                    <span class="meta">
                                        @if($mini->published_at)
                                            <span><svg><use href="#i-calendar"/></svg> {{ \App\Support\Number::fa(\Morilog\Jalali\Jalalian::fromCarbon($mini->published_at)->format('Y/m/d')) }}</span>
                                        @endif
                                    </span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @php $__cards = ($news ?? collect())->slice(1, 6); @endphp
            @if($__cards->count() > 0)
                <div class="cards">
                    @foreach($__cards as $i => $item)
                        @php $__tone = ['tone-a', 'tone-b', 'tone-c'][$i % 3]; @endphp
                        <a class="card {{ $__tone }} reveal" href="{{ $__newsUrl($item) ?: '#' }}" style="--d:{{ ($i % 3) * 70 }}ms">
                            <div class="img">
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" loading="lazy">
                                @else
                                    <span class="art">
                                        <span class="glyph"><svg><use href="#i-news"/></svg></span>
                                    </span>
                                @endif
                                @if($item->section)
                                    <span class="cat"><span class="chip">{{ $item->section->name }}</span></span>
                                @endif
                            </div>
                            <div class="body">
                                <h3>{{ $item->title }}</h3>
                                @if($item->excerpt)
                                    <p class="excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($item->excerpt), 120) }}</p>
                                @endif
                                <div class="meta">
                                    @if($item->published_at)
                                        <span><svg><use href="#i-calendar"/></svg> {{ \App\Support\Number::fa(\Morilog\Jalali\Jalalian::fromCarbon($item->published_at)->format('Y/m/d')) }}</span>
                                    @endif
                                    @if(($item->stat_views ?? 0) > 0)
                                        <span><svg><use href="#i-eye"/></svg> {{ \App\Support\Number::fa(number_format($item->stat_views)) }} بازدید</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @elseif(!$__featured)
                <div class="empty-note reveal">هنوز خبری منتشر نشده است.</div>
            @endif
        </section>

        {{-- ══════════ بخش‌ها ══════════ --}}
        <section class="block" id="sections" aria-labelledby="sectionsTitle">
            <header class="block-head reveal">
                <div>
                    <span class="kicker-line">نقشهٔ شهرستان</span>
                    <h2 id="sectionsTitle">بخش‌های شهرستان چگنی</h2>
                    <p>برای ورود به پایگاه اطلاع‌رسانی هر بخش، روی آن بزنید</p>
                </div>
            </header>

            @if($sections->count() > 0)
                <div class="section-cards">
                    @foreach($sections as $i => $sec)
                        @php
                            $__sub = $sec->subdomain;
                            $__secUrl = $__sub
                                ? request()->getScheme() . '://' . $__sub . '.' . config('app.base_domain')
                                : '/#sections';
                            $__tone = ['', 'tone-b', 'tone-c'][$i % 3];
                        @endphp
                        <a class="s-card {{ $__tone }} reveal" href="{{ $__secUrl }}" style="--d:{{ ($i % 3) * 110 }}ms">
                            <span class="arc"></span>
                            <div class="head">
                                <span class="badge-icon"><svg><use href="#i-gov"/></svg></span>
                                <div>
                                    <h3>{{ $sec->name }}</h3>
                                    <span class="sub">
                                        @if($sec->county) شهرستان {{ $sec->county }}@endif
                                        @if($sec->province) — {{ $sec->province }}@endif
                                    </span>
                                </div>
                            </div>
                            <p class="desc">
                                {{ $sec->description
                                    ? \Illuminate\Support\Str::limit($sec->description, 120)
                                    : 'اطلاعات و اخبار این بخش را مشاهده کنید.' }}
                            </p>
                            <div class="foot">
                                <span class="pill"><svg width="13" height="13"><use href="#i-homes"/></svg> {{ \App\Support\Number::fa(number_format($sec->villages()->count())) }} روستا</span>
                                <span class="enter">ورود به سایت بخش <svg><use href="#i-arrow"/></svg></span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty-note reveal">هنوز بخشی ثبت نشده است.</div>
            @endif
        </section>

        {{-- ══════════ مسئولین ══════════ --}}
        <section class="block" id="officials" aria-labelledby="offTitle">
            <header class="block-head reveal">
                <div>
                    <span class="kicker-line">مسئولین</span>
                    <h2 id="offTitle">مسئولین شهرستان</h2>
                    <p>فرماندار، معاونین و بخشداران</p>
                </div>
            </header>
            <div class="people">
                <a class="person reveal" href="/officials/governor"><span class="av"><svg><use href="#i-star"/></svg></span><span><b>فرماندار چگنی</b><small>معرفی و سوابق</small></span></a>
                <a class="person reveal" style="--d:60ms" href="/officials/deputies"><span class="av"><svg><use href="#i-users"/></svg></span><span><b>معاونین فرماندار</b><small>فهرست معاونین</small></span></a>
                <a class="person reveal" style="--d:120ms" href="/officials/bakhshdars"><span class="av"><svg><use href="#i-gov"/></svg></span><span><b>بخشداران</b><small>بخش‌های شهرستان</small></span></a>
            </div>
        </section>

    </div>
</main>

@include('partials.gov-footer')
@include('partials.back-to-top')
@include('partials.analytics')

<script>
    (function () {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); }
            });
        }, { threshold: .1, rootMargin: '0px 0px -40px 0px' });
        document.querySelectorAll('.reveal').forEach(function (el) { io.observe(el); });
        var toTop = document.getElementById('backToTop') || document.querySelector('.to-top');
        if (toTop) {
            window.addEventListener('scroll', function () {
                toTop.classList.toggle('show', window.scrollY > 520);
            }, { passive: true });
        }
    })();
</script>
</body>
</html>

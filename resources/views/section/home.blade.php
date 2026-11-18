<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $section->name }} | پایگاه اطلاع‌رسانی</title>
    <meta name="description" content="{{ $section->description ? \Illuminate\Support\Str::limit(strip_tags($section->description), 155) : 'پایگاه اطلاع‌رسانی بخش ' . $section->name . ' شهرستان چگنی' }}">
    <meta name="theme-color" content="#c94a2b">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="stylesheet" href="{{ asset('css/ui-v3.css') }}?v=6">
    @include('partials.pwa')
</head>
<body class="v3-body">

@include('partials.gov-header')

@php
    $__fa = function ($n) {
        return \App\Support\Number::fa(number_format((int) $n));
    };
    $__vCount = isset($villages) ? $villages->count() : 0;
    $__nCount = isset($news) ? $news->count() : 0;
@endphp

{{-- ══════════ نوار خبری ══════════ --}}
@if(isset($news) && $news->count() > 0)
    <div class="sh-ticker">
        <span class="sh-ticker-label"><svg><use href="#i-news"/></svg> آخرین اخبار</span>
        <div class="sh-ticker-track">
            <div class="sh-ticker-move">
                @foreach([0, 1] as $__dup)
                    @foreach($news->take(6) as $__t)
                        <a href="{{ route('section.news.show', $__t->slug) }}">{{ $__t->title }}</a>
                    @endforeach
                @endforeach
            </div>
        </div>
        <a class="sh-ticker-more" href="/news">همه <svg><use href="#i-arrow"/></svg></a>
    </div>
@endif

<main>
    <div class="v3-wrap">

        <div class="crumbs">
            <a href="{{ request()->getScheme() }}://{{ config('app.base_domain') }}">پرتال چگنی</a>
            <span>/</span>
            <a href="{{ request()->getScheme() }}://{{ config('app.base_domain') }}/#sections">بخش‌ها</a>
            <span>/</span>
            <b>{{ $section->name }}</b>
        </div>

        {{-- ══════════ هیرو ══════════ --}}
        <section class="hero">
            <div class="hero-copy reveal">
                <span class="eyebrow"><i></i> بخش {{ $section->name }} · شهرستان {{ $section->county ?: 'چگنی' }}</span>
                <h1>خبرهای <em>{{ $section->name }}</em></h1>
                <p class="lede">
                    {{ $__fa($__vCount) }} روستا، یک بخشداری و یک پایگاه واحد. اخبار، اطلاعیه‌ها و خدمات بخش {{ $section->name }} را از همین‌جا دنبال کنید.
                </p>

                <form class="search" role="search" action="{{ route('search.index') }}" method="GET">
                    <svg><use href="#i-search"/></svg>
                    <input type="search" name="q" placeholder="جستجو در اخبار و روستاهای {{ $section->name }}…" aria-label="جستجو">
                    <button class="btn btn-primary" type="submit">جستجو</button>
                </form>

                <div class="hero-tags">
                    <span class="tag"><svg><use href="#i-homes"/></svg> {{ $__fa($__vCount) }} روستا</span>
                    <span class="tag"><svg><use href="#i-news"/></svg> {{ $__fa($__nCount) }} خبر</span>
                    @if($section->population_count > 0)
                        <span class="tag gold"><svg><use href="#i-users"/></svg> {{ $__fa($section->population_count) }} نفر جمعیت</span>
                    @endif
                </div>
            </div>

            @php
                $__secPhoto = ($section->cover_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($section->cover_image))
                    ? asset('storage/' . $section->cover_image)
                    : null;
            @endphp
            @include('partials.hero-art', [
                'order' => ['photo', 'symbol'],
                'photo' => $__secPhoto,
                'alt' => 'بخش ' . $section->name,
                'pin' => 'بخش ' . $section->name,
                'kicker' => 'پایگاه اطلاع‌رسانی بخشداری',
                'title' => $section->name,
                'subtitle' => 'شهرستان ' . ($section->county ?: 'چگنی') . ' · ' . ($section->province ?: 'لرستان'),
                'stats' => [
                    $__fa($__vCount) . ' روستا',
                    $__fa($__nCount) . ' خبر',
                ],
                'initial' => mb_substr($section->name, 0, 1),
            ])
        </section>

        @if($section->cover_image && !$__secPhoto && \Illuminate\Support\Facades\Storage::disk('public')->exists($section->cover_image))
            <div class="sh-cover reveal">
                <img src="{{ asset('storage/' . $section->cover_image) }}" alt="{{ $section->name }}" loading="lazy">
            </div>
        @endif

        {{-- ══════════ اخبار ══════════ --}}
        <section class="block" id="news" aria-labelledby="newsTitle">
            <header class="block-head reveal">
                <div>
                    <span class="kicker-line">تازه‌ترین‌ها</span>
                    <h2 id="newsTitle">اخبار بخش {{ $section->name }}</h2>
                    <p>به ترتیب تاریخ انتشار — همهٔ روستاهای بخش</p>
                </div>
                <a class="link-more" href="/news">همهٔ اخبار <svg><use href="#i-arrow"/></svg></a>
            </header>

            @if(isset($news) && $news->count() > 0)
                <div class="cols">
                    <div class="news-col">
                        <div class="news-slider per-2 reveal">
                            <div class="news-viewport">
                                <div class="cards news-track">
                                    @foreach($news->take(6) as $i => $item)
                                        @php
                                            $__tone = ['tone-a', 'tone-b', 'tone-c'][$i % 3];
                                            $__glyph = ['i-sprout', 'i-chart', 'i-pin', 'i-news'][$i % 4];
                                        @endphp
                                        <a class="card {{ $__tone }}" href="{{ route('section.news.show', $item->slug) }}">
                                            <div class="img">
                                                @if($item->image)
                                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" loading="lazy">
                                                @else
                                                    <span class="art"><span class="glyph"><svg><use href="#{{ $__glyph }}"/></svg></span></span>
                                                @endif
                                                @if($item->is_pinned)
                                                    <span class="cat"><span class="chip">مهم</span></span>
                                                @endif
                                            </div>
                                            <div class="body">
                                                <h3>{{ $item->title }}</h3>
                                                <p class="excerpt">{{ \Illuminate\Support\Str::limit($item->excerpt ?: strip_tags($item->content), 130) }}</p>
                                                <div class="meta">
                                                    <span><svg><use href="#i-calendar"/></svg> {{ $item->published_at ? jalali($item->published_at) : jalali($item->created_at) }}</span>
                                                    @if(($item->stat_views ?? 0) > 0)
                                                        <span><svg><use href="#i-eye"/></svg> {{ $__fa($item->stat_views) }} بازدید</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            <div class="news-nav">
                                <button type="button" class="news-arrow news-prev" aria-label="خبرهای پیشین">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
                                </button>
                                <div class="news-dots" aria-label="صفحه‌های اخبار"></div>
                                <button type="button" class="news-arrow news-next" aria-label="خبرهای بعدی">
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <aside>
                        @if($section->bakhshdar_name)
                            <div class="panel reveal sh-bakhshdar">
                                @if($section->bakhshdar_photo)
                                    <img class="sh-avatar" src="{{ asset('storage/' . $section->bakhshdar_photo) }}" alt="{{ $section->bakhshdar_name }}" loading="lazy">
                                @else
                                    <span class="sh-avatar sh-avatar-ph">{{ mb_substr($section->bakhshdar_name, 0, 1) }}</span>
                                @endif
                                <span class="sh-role">بخشدار {{ $section->name }}</span>
                                <h3>{{ $section->bakhshdar_name }}</h3>
                                @if($section->bakhshdar_phone)
                                    <div class="kv"><span>تماس</span><b dir="ltr">{{ $section->bakhshdar_phone }}</b></div>
                                @endif
                                <a class="link-more" style="margin-top:10px" href="/bakhshdar">مشاهدهٔ پروفایل <svg><use href="#i-arrow"/></svg></a>
                            </div>
                        @endif

                        @if(isset($news) && $news->where('is_pinned', true)->count() > 0)
                            <div class="panel reveal" style="margin-top:16px">
                                <h3>اطلاعیه‌های مهم</h3>
                                @foreach($news->where('is_pinned', true)->take(4) as $pin)
                                    <a class="notice hot" href="{{ route('section.news.show', $pin->slug) }}"><span class="dot"></span><div><b>{{ \Illuminate\Support\Str::limit($pin->title, 70) }}</b><small>{{ $pin->published_at ? jalali($pin->published_at) : jalali($pin->created_at) }}</small></div></a>
                                @endforeach
                            </div>
                        @endif

                        @if($__vCount > 0)
                            <div class="panel reveal" style="margin-top:16px">
                                <h3>روستاهای بخش</h3>
                                @foreach($villages->take(6) as $v)
                                    <a class="kv" href="{{ route('public.village.show', $v->slug) }}" style="text-decoration:none;color:inherit">
                                        <span>{{ $v->name }}</span>
                                        <b>ورود ←</b>
                                    </a>
                                @endforeach
                                @if($__vCount > 6)
                                    <a class="link-more" style="margin-top:10px" href="#villages">همهٔ {{ $__fa($__vCount) }} روستا <svg><use href="#i-arrow"/></svg></a>
                                @endif
                            </div>
                        @endif
                    </aside>
                </div>
            @else
                <div class="empty-note reveal">هنوز خبری منتشر نشده است.</div>
            @endif
        </section>

        {{-- ══════════ روستاها ══════════ --}}
        <section class="block" id="villages" aria-labelledby="villTitle">
            <header class="block-head reveal">
                <div>
                    <span class="kicker-line">فهرست</span>
                    <h2 id="villTitle">روستاهای بخش {{ $section->name }}</h2>
                    <p>برای دیدن صفحهٔ هر روستا روی نام آن بزنید</p>
                </div>
                @if($__vCount > 0)
                    <span class="link-more">همهٔ {{ $__fa($__vCount) }} روستا</span>
                @endif
            </header>

            @if($__vCount > 0)
                <div class="vgrid">
                    @foreach($villages as $i => $village)
                        <a class="vchip reveal" href="{{ route('public.village.show', $village->slug) }}" style="--d:{{ ($i % 4) * 60 }}ms">
                            <span>
                                <b>{{ $village->name }}</b>
                                <small>
                                    @if($village->population > 0)
                                        {{ $__fa($village->population) }} نفر
                                    @else
                                        روستای بخش {{ $section->name }}
                                    @endif
                                </small>
                            </span>
                            <svg><use href="#i-arrow"/></svg>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty-note reveal">هنوز روستایی ثبت نشده است.</div>
            @endif
        </section>

        {{-- ══════════ گالری ══════════ --}}
        @if(isset($gallery) && count($gallery) > 0)
            <section class="block" id="gallery" aria-labelledby="galTitle">
                <header class="block-head reveal">
                    <div>
                        <span class="kicker-line">تصاویر</span>
                        <h2 id="galTitle">گالری تصاویر {{ $section->name }}</h2>
                        <p>رویدادها، طرح‌های عمرانی و روستاهای بخش</p>
                    </div>
                </header>
                <div class="gallery">
                    @foreach($gallery as $i => $img)
                        @php $__tone = ['a', 'b', 'c', 'd'][$i % 4]; @endphp
                        <a class="gph {{ $__tone }} reveal" href="{{ asset('storage/' . $img->image_path) }}" target="_blank" rel="noopener"
                           style="--d:{{ ($i % 4) * 60 }}ms;background-image:url('{{ asset('storage/' . $img->image_path) }}');background-size:cover;background-position:center"
                           aria-label="{{ $img->caption ?: 'تصویر ' . ($i + 1) }}"></a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ══════════ معرفی بخش ══════════ --}}
        <section class="block" id="about" aria-labelledby="aboutTitle">
            <header class="block-head reveal">
                <div>
                    <span class="kicker-line">دربارهٔ بخش</span>
                    <h2 id="aboutTitle">معرفی بخش {{ $section->name }}</h2>
                    <p>موقعیت، اقتصاد و خدمات بخش</p>
                </div>
                <a class="link-more" href="/bakhshdar">معرفی بخشدار <svg><use href="#i-arrow"/></svg></a>
            </header>

            <div class="cols">
                <div class="panel reveal">
                    <h3>بخش {{ $section->name }}</h3>
                    <div class="sh-prose">
                        @if($section->description)
                            {!! nl2br(e($section->description)) !!}
                        @else
                            <span class="sh-muted">توضیحات این بخش در حال تکمیل است.</span>
                        @endif
                    </div>
                </div>

                <div class="panel reveal" style="--d:90ms">
                    <h3>تماس با بخشداری</h3>
                    @if($section->phone)
                        <div class="kv"><span>تلفن</span><b dir="ltr">{{ $section->phone }}</b></div>
                    @else
                        <div class="kv"><span>تلفن</span><b dir="ltr">۰۶۶-۳۳۱۳۳۲۲۵</b></div>
                    @endif
                    <div class="kv"><span>نشانی</span><b>{{ $section->address ?: 'بخشداری ' . $section->name }}</b></div>
                    @if($section->email)
                        <div class="kv"><span>رایانامه</span><b dir="ltr">{{ $section->email }}</b></div>
                    @endif
                    <div class="kv"><span>تعداد روستا</span><b>{{ $__fa($__vCount) }} روستا</b></div>
                    @if($section->population_count > 0)
                        <div class="kv"><span>جمعیت بخش</span><b>{{ $__fa($section->population_count) }} نفر</b></div>
                    @endif
                </div>
            </div>

            @if($section->facilities || $section->historical_sites)
                <div class="sh-two reveal" style="margin-top:18px">
                    <div class="panel">
                        <h3><span class="sh-ico"><svg><use href="#i-gov"/></svg></span> امکانات و خدمات</h3>
                        <div class="sh-prose">
                            @if($section->facilities)
                                {!! nl2br(e($section->facilities)) !!}
                            @else
                                <span class="sh-muted">در حال تکمیل…</span>
                            @endif
                        </div>
                    </div>
                    <div class="panel">
                        <h3><span class="sh-ico gold"><svg><use href="#i-star"/></svg></span> جاذبه‌های تاریخی و زیارتی</h3>
                        <div class="sh-prose">
                            @if($section->historical_sites)
                                {!! nl2br(e($section->historical_sites)) !!}
                            @else
                                <span class="sh-muted">در حال تکمیل…</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if($section->population_count > 0)
                <div class="sh-stat reveal">
                    <span class="sh-stat-label"><svg><use href="#i-users"/></svg> جمعیت کل بخش</span>
                    <span class="sh-stat-num counter-value" data-target="{{ (int) $section->population_count }}">۰</span>
                    <span class="sh-stat-unit">نفر</span>
                </div>
            @endif
        </section>

        {{-- ══════════ کارکنان ══════════ --}}
        @if($section->staffs && $section->staffs->count() > 0)
            <section class="block" id="staff" aria-labelledby="staffTitle">
                <header class="block-head reveal">
                    <div>
                        <span class="kicker-line">همکاران</span>
                        <h2 id="staffTitle">کارکنان بخشداری {{ $section->name }}</h2>
                        <p>مسئولان و کارشناسان بخشداری</p>
                    </div>
                </header>

                <div class="sh-staff">
                    @foreach($section->staffs as $i => $staff)
                        <div class="sh-staff-card reveal" style="--d:{{ ($i % 3) * 60 }}ms">
                            @if($staff->photo)
                                <img class="sh-staff-photo" src="{{ asset('storage/' . $staff->photo) }}" alt="{{ $staff->name }}" loading="lazy">
                            @else
                                <span class="sh-staff-photo sh-staff-ph">{{ mb_substr($staff->name, 0, 1) }}</span>
                            @endif
                            <b>{{ $staff->name }}</b>
                            <small>{{ $staff->position }}</small>
                            @if($staff->phone)
                                <a class="sh-staff-tel" href="tel:{{ $staff->phone }}"><svg><use href="#i-phone"/></svg> <span dir="ltr">{{ $staff->phone }}</span></a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ══════════ ورود کارشناسان ══════════ --}}
        <section class="block reveal">
            <div class="sh-expert">
                <div class="sh-expert-txt">
                    <b><svg><use href="#i-user"/></svg> کارشناسان فنی و مالی دهیاری‌ها</b>
                    <p>برای ثبت، ویرایش و مدیریت تعرفهٔ عوارض روستاهای این بخش وارد شوید.</p>
                </div>
                <a class="sh-expert-btn" href="{{ route('expert.login') }}">ورود کارشناسان <svg><use href="#i-arrow"/></svg></a>
            </div>
        </section>

    </div>
</main>

@include('partials.gov-footer')
@include('partials.back-to-top')
@include('partials.analytics')

<style>
    /* ═══ استایل‌های اختصاصی صفحهٔ بخش — پیشوند sh ═══ */
    .sh-ticker {
        display: flex; align-items: stretch; gap: 0;
        min-height: 40px;
        background: #fdf1dc;
        border-block: 1px solid #f3ddb8;
        font-family: 'Mikhak', Tahoma, sans-serif;
        margin-top: 6px;
    }
    .sh-ticker-label {
        display: inline-flex; align-items: center; gap: 7px; flex: none;
        background: #c94a2b; color: #fff7e9;
        padding: 0 16px; font-size: 12.5px; font-weight: 700;
        clip-path: polygon(0 0, 100% 0, calc(100% - 12px) 100%, 0 100%);
        padding-inline-end: 26px;
    }
    .sh-ticker-label svg { width: 14px; height: 14px; }
    .sh-ticker-track { flex: 1; overflow: hidden; display: flex; align-items: center; }
    .sh-ticker-move { display: inline-flex; align-items: center; gap: 36px; white-space: nowrap; animation: sh-scroll 55s linear infinite; }
    .sh-ticker-move a {
        font-size: 12.5px; color: #5c2313; text-decoration: none;
        display: inline-flex; align-items: center; gap: 8px;
    }
    .sh-ticker-move a::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: #f5b841; flex: none; }
    .sh-ticker-move a:hover { color: #c94a2b; }
    @keyframes sh-scroll { from { transform: translateX(0); } to { transform: translateX(-50%); } }
    .sh-ticker-more {
        display: inline-flex; align-items: center; gap: 5px; flex: none;
        padding: 0 16px; font-family: 'Tanha', Tahoma, sans-serif; font-size: 11.5px; font-weight: 700;
        color: #c94a2b; text-decoration: none; border-inline-start: 1px solid #f3ddb8;
    }
    .sh-ticker-more svg { width: 12px; height: 12px; }

    .sh-cover { margin-top: 22px; border-radius: 26px; overflow: hidden; box-shadow: 0 24px 50px -34px rgba(74,27,15,.5); }
    .sh-cover img { display: block; width: 100%; max-height: 400px; object-fit: cover; }

    .sh-bakhshdar { text-align: center; }
    .sh-bakhshdar .sh-avatar {
        width: 96px; height: 96px; border-radius: 50%; object-fit: cover;
        display: grid; place-items: center; margin: 0 auto 12px;
        background: #fdeee7; color: #c94a2b;
        font-family: 'Lalezar', Tahoma, sans-serif; font-size: 40px;
        box-shadow: inset 0 0 0 3px #fff, 0 10px 24px -16px rgba(201,74,43,.7);
    }
    .sh-bakhshdar .sh-role { font-family: 'Tanha', Tahoma, sans-serif; font-size: 11.5px; color: #8a7a6a; }
    .sh-bakhshdar h3 { margin: 4px 0 10px; font-size: 19px; }
    .sh-bakhshdar .kv { justify-content: center; border: 0; }

    .panel a.notice { text-decoration: none; color: inherit; }
    .panel a.notice:hover b { color: #c94a2b; }

    .sh-prose { font-size: 13.5px; line-height: 2.1; color: #5a4a3c; text-align: justify; }
    .sh-muted { color: #8a7a6a; font-style: italic; }
    .sh-two { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
    .sh-ico {
        display: inline-grid; place-items: center; width: 30px; height: 30px; border-radius: 10px;
        background: #fdeee7; color: #c94a2b; vertical-align: -8px; margin-inline-end: 8px;
    }
    .sh-ico.gold { background: #fdf1dc; color: #b8801a; }
    .sh-ico svg { width: 16px; height: 16px; }

    .sh-stat {
        margin-top: 18px; display: flex; align-items: center; justify-content: center; gap: 12px;
        background: linear-gradient(150deg, #fdf1dc, #fbe6cf);
        border-radius: 24px; padding: 22px; box-shadow: inset 0 0 0 1px #f3ddb8;
        flex-wrap: wrap;
    }
    .sh-stat-label { display: inline-flex; align-items: center; gap: 7px; font-size: 13px; color: #8a5a08; font-weight: 700; }
    .sh-stat-label svg { width: 16px; height: 16px; }
    .sh-stat-num { font-family: 'Lalezar', Tahoma, sans-serif; font-size: 46px; line-height: 1; color: #5c2313; }
    .sh-stat-unit { font-family: 'Tanha', Tahoma, sans-serif; font-size: 13px; color: #8a7a6a; }

    .sh-staff { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 16px; }
    .sh-staff-card {
        background: #fffdf9; border-radius: 22px; padding: 20px 16px; text-align: center;
        box-shadow: 0 12px 30px -26px rgba(74,27,15,.5);
    }
    .sh-staff-photo {
        width: 88px; height: 88px; border-radius: 50%; object-fit: cover; display: grid; place-items: center;
        margin: 0 auto 12px; background: #fdeee7; color: #c94a2b;
        font-family: 'Lalezar', Tahoma, sans-serif; font-size: 36px;
    }
    .sh-staff-card b { display: block; font-size: 14px; color: #5c2313; margin-bottom: 4px; }
    .sh-staff-card small { display: block; font-family: 'Tanha', Tahoma, sans-serif; font-size: 11.5px; color: #8a7a6a; }
    .sh-staff-tel {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        margin-top: 12px; width: 100%; padding: 8px 12px; border-radius: 999px;
        background: #fdeee7; color: #c94a2b; text-decoration: none;
        font-family: 'Tanha', Tahoma, sans-serif; font-size: 12px; font-weight: 700;
    }
    .sh-staff-tel svg { width: 13px; height: 13px; }
    .sh-staff-tel:hover { background: #fbdcd0; }

    .sh-expert {
        display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;
        background: linear-gradient(150deg, #fdeee7, #fbdcd0);
        border-radius: 26px; padding: 24px 26px;
        box-shadow: inset 0 0 0 1px rgba(201,74,43,.16);
    }
    .sh-expert b { display: inline-flex; align-items: center; gap: 8px; font-size: 15px; color: #5c2313; }
    .sh-expert b svg { width: 17px; height: 17px; color: #c94a2b; }
    .sh-expert p { margin: 6px 0 0; font-size: 13px; line-height: 2; color: #7a5842; }
    .sh-expert-btn {
        display: inline-flex; align-items: center; gap: 8px; flex: none;
        background: linear-gradient(150deg, #e2603c, #c94a2b); color: #fff7e9;
        text-decoration: none; padding: 12px 22px; border-radius: 999px;
        font-size: 13px; font-weight: 700; white-space: nowrap;
        box-shadow: 0 14px 26px -18px rgba(201,74,43,.9);
    }
    .sh-expert-btn svg { width: 14px; height: 14px; }
    .sh-expert-btn:hover { filter: brightness(1.05); }

    @media (max-width: 900px) { .sh-two { grid-template-columns: 1fr; } }
    @media (max-width: 720px) {
        .sh-ticker-label { font-size: 11.5px; padding-inline-end: 22px; }
        .sh-ticker-more { display: none; }
        .sh-expert { padding: 20px; }
        .sh-expert-btn { width: 100%; justify-content: center; }
    }
</style>

<script>
    (function () {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); }
            });
        }, { threshold: .1, rootMargin: '0px 0px -40px 0px' });
        document.querySelectorAll('.reveal').forEach(function (el) { io.observe(el); });

        var counter = document.querySelector('.counter-value');
        if (counter) {
            var target = +counter.getAttribute('data-target');
            if (target > 0) {
                var started = false;
                var obs = new IntersectionObserver(function (entries) {
                    if (!entries[0].isIntersecting || started) return;
                    started = true;
                    obs.disconnect();
                    var duration = 1600, start = null;
                    function step(ts) {
                        if (!start) start = ts;
                        var p = Math.min((ts - start) / duration, 1);
                        var val = Math.floor(target * p);
                        counter.textContent = val.toLocaleString('fa-IR');
                        if (p < 1) requestAnimationFrame(step);
                        else counter.textContent = target.toLocaleString('fa-IR');
                    }
                    requestAnimationFrame(step);
                }, { threshold: .5 });
                obs.observe(counter);
            }
        }
    })();
</script>
@include('partials.news-slider')
</body>
</html>

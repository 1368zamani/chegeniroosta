<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پایگاه اط‌رسانی روستای {{ $village->name }}</title>
    <meta name="description" content="{{ $village->description ? \Illuminate\Support\Str::limit(strip_tags($village->description), 155) : 'اطلاعات و اخبار روستای ' . $village->name }}">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#c94a2b">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="stylesheet" href="{{ asset('css/ui-v3.css') }}?v=8">
    @include('partials.pwa')
</head>
<body class="v3-body">

@php
    $__villageSection = null;
    if (isset($village) && $village->section_id) {
        $__villageSection = \App\Models\Section::find($village->section_id);
    }

    $__fa = function ($n) {
        return \App\Support\Number::fa(number_format((int) $n));
    };

    $__neighbors = collect();
    if ($village->section_id) {
        $__neighbors = \App\Models\Village::where('section_id', $village->section_id)
            ->where('is_active', true)
            ->where('id', '!=', $village->id)
            ->orderBy('name')
            ->limit(6)
            ->get();
    }

    $__resolutionCount = \App\Models\VillageCouncilResolution::where('village_id', $village->id)
        ->where('is_public', true)->count();
@endphp

@include('partials.gov-header')

<main>
    <div class="v3-wrap">

        <div class="crumbs">
            <a href="{{ request()->getScheme() }}://{{ config('app.base_domain') }}">پرتال چگنی</a>
            <span>/</span>
            @if($__villageSection)
                <a href="{{ request()->getScheme() }}://{{ $__villageSection->subdomain ?: 'visian' }}.{{ config('app.base_domain') }}">بخش {{ $__villageSection->name }}</a>
            @else
                <a href="{{ request()->getScheme() }}://{{ config('app.base_domain') }}/#sections">بخش‌ها</a>
            @endif
            <span>/</span>
            <b>روستای {{ $village->name }}</b>
        </div>

        {{-- ══════════ هیرو ══════════ --}}
        <section class="hero">
            <div class="hero-copy reveal">
                <span class="eyebrow"><i></i> روستای {{ $village->name }}@if($village->section) · بخش {{ $village->section }}@endif</span>
                <h1>روستای <em>{{ $village->name }}</em></h1>
                <p class="lede">
                    @if($village->welcome_text)
                        {{ \Illuminate\Support\Str::limit(strip_tags($village->welcome_text), 190) }}
                    @else
                        صفحهٔ رسمی روستای {{ $village->name }}؛ اینجا خبرهای روستا، اطلاعات تماس دهیاری و خدمات محلی را می‌بینید.
                    @endif
                </p>

                <div class="hero-tags">
                    @if($village->households > 0)
                        <span class="tag"><svg><use href="#i-homes"/></svg> {{ $__fa($village->households) }} خانوار</span>
                    @endif
                    @if($village->population > 0)
                        <span class="tag"><svg><use href="#i-users"/></svg> {{ $__fa($village->population) }} نفر</span>
                    @endif
                    @if($village->distance_to_center)
                        <span class="tag gold"><svg><use href="#i-pin"/></svg> {{ $village->distance_to_center }} کیلومتر تا مرکز بخش</span>
                    @endif
                    @if(isset($stats['active_projects']) && $stats['active_projects'] > 0)
                        <span class="tag"><svg><use href="#i-chart"/></svg> {{ $__fa($stats['active_projects']) }} طرح عمرانی</span>
                    @endif
                </div>
            </div>

            @php
                $__vCover = ($village->cover_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($village->cover_image))
                    ? asset('storage/' . $village->cover_image)
                    : null;
                $__vDehyar = ($village->dehyar_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($village->dehyar_photo))
                    ? asset('storage/' . $village->dehyar_photo)
                    : null;
                $__vVariant = $__vDehyar ? 'dehyar' : ($__vCover ? 'photo' : 'symbol');
            @endphp
            @include('partials.hero-art', [
                'order' => ['dehyar', 'photo', 'symbol'],
                'photo' => $__vCover,
                'dehyarPhoto' => $__vDehyar,
                'dehyar' => [
                    'name' => $village->dehyar_name,
                    'role' => 'دهیار روستا',
                    'org' => 'دهیاری ' . $village->name . ($village->section ? ' · بخش ' . $village->section : ''),
                    'phone' => $village->dehyar_phone,
                    'hours' => $village->dehyar_hours,
                    'url' => route('village.site.dehyar', $village->slug),
                    'cta' => 'پروفایل دهیار',
                ],
                'alt' => 'روستای ' . $village->name,
                'pin' => 'روستای ' . $village->name,
                'kicker' => 'پایگاه اطلاع‌رسانی روستا',
                'title' => $village->name,
                'subtitle' => 'روستای ' . $village->name . ($village->section ? ' · بخش ' . $village->section : '') . ' · ' . ($village->province ?: 'لرستان'),
                'stats' => array_filter([
                    $village->population > 0 ? $__fa($village->population) . ' نفر' : null,
                    $village->households > 0 ? $__fa($village->households) . ' خانوار' : null,
                    $village->distance_to_center ? $village->distance_to_center . ' کیلومتر تا مرکز بخش' : null,
                ]),
                'initial' => mb_substr($village->name, 0, 1),
            ])
        </section>

        @if($__vCover && $__vVariant !== 'photo')
            <div class="vg-cover reveal">
                <img src="{{ asset('storage/' . $village->cover_image) }}" alt="{{ $village->name }}" loading="lazy">
            </div>
        @endif

        {{-- ══════════ اعداد کلیدی ══════════ --}}
        <div class="facts" id="facts">
            @if($village->population > 0)
                <div class="fact reveal"><small>جمعیت</small><b>{{ $__fa($village->population) }}</b><span>نفر</span></div>
            @endif
            @if($village->households > 0)
                <div class="fact reveal" style="--d:60ms"><small>خانوار</small><b>{{ $__fa($village->households) }}</b><span>خانوار</span></div>
            @endif
            @if($village->distance_to_center)
                <div class="fact reveal" style="--d:120ms"><small>فاصله تا مرکز بخش</small><b>{{ $village->distance_to_center }}</b><span>کیلومتر</span></div>
            @endif
            @if($village->area)
                <div class="fact reveal" style="--d:180ms"><small>مساحت</small><b>{{ $__fa($village->area) }}</b><span>هکتار</span></div>
            @elseif($village->hectares)
                <div class="fact reveal" style="--d:180ms"><small>مساحت</small><b>{{ $__fa($village->hectares) }}</b><span>هکتار</span></div>
            @endif
        </div>

        <nav class="tabs" aria-label="بخش‌های صفحه">
            <a class="on" href="#vnewsTitle">اخبار</a>
            <a href="#vaboutTitle">معرفی</a>
            @if(isset($images) && $images->count() > 0)<a href="#vgalTitle">تصاویر</a>@endif
            @if(isset($facilities) && $facilities->count() > 0)<a href="#facTitle">امکانات</a>@endif
            @if(isset($featuredProducts) && $featuredProducts->count() > 0)<a href="#prodTitle">محصولات</a>@endif
            <a href="#reqTitle">خدمات</a>
            <a href="#mapTitle">نقشه</a>
        </nav>

        {{-- ══════════ دربارهٔ روستا ══════════ --}}
        <section class="block" style="margin-top:34px" aria-labelledby="vaboutTitle">
            <header class="block-head reveal">
                <div>
                    <span class="kicker-line">دربارهٔ روستا</span>
                    <h2 id="vaboutTitle">دربارهٔ {{ $village->name }}</h2>
                    <p>معرفی روستا و دهیاری</p>
                </div>
                @if($village->dehyar_name)
                    <a class="link-more" href="{{ route('village.site.dehyar', $village->slug) }}">معرفی دهیار <svg><use href="#i-arrow"/></svg></a>
                @endif
            </header>

            <div class="cols">
                <div class="panel reveal">
                    <h3>{{ $village->name }}</h3>
                    <div class="vg-prose">
                        @if($village->description)
                            {!! nl2br(e($village->description)) !!}
                        @elseif($village->history_text)
                            {!! nl2br(e($village->history_text)) !!}
                        @else
                            <span class="vg-muted">توضیحات این روستا در حال تکمیل است.</span>
                        @endif
                        @if($village->naming_story)
                            <p class="vg-note"><b>داستان نام‌گذاری:</b> {{ $village->naming_story }}</p>
                        @endif
                    </div>
                </div>

                <div class="panel reveal" style="--d:90ms">
                    <h3>دهیار روستا</h3>
                    @if($village->dehyar_name)
                        <div class="person" style="background:transparent;box-shadow:none;padding:0 0 12px;border-bottom:1px dashed #efe2d1">
                            @if($village->dehyar_photo)
                                <img class="vg-av" src="{{ asset('storage/' . $village->dehyar_photo) }}" alt="{{ $village->dehyar_name }}" loading="lazy">
                            @else
                                <span class="vg-av vg-av-ph">{{ mb_substr($village->dehyar_name, 0, 1) }}</span>
                            @endif
                            <span><b>{{ $village->dehyar_name }}</b><small>دهیار {{ $village->name }}</small></span>
                        </div>
                        @if($village->dehyar_phone)
                            <div class="kv"><span>تلفن دهیاری</span><b dir="ltr">{{ $village->dehyar_phone }}</b></div>
                        @elseif($village->phone)
                            <div class="kv"><span>تلفن دهیاری</span><b dir="ltr">{{ $village->phone }}</b></div>
                        @endif
                        @if($village->dehyar_email)
                            <div class="kv"><span>رایانامه</span><b dir="ltr">{{ $village->dehyar_email }}</b></div>
                        @endif
                        <div class="kv"><span>ساعات پاسخگویی</span><b>۸ تا ۱۴</b></div>
                        <a class="link-more" style="margin-top:12px" href="{{ route('village.site.dehyar', $village->slug) }}">پروفایل کامل دهیار <svg><use href="#i-arrow"/></svg></a>
                    @else
                        <p class="vg-muted">اطلاعات دهیار این روستا هنوز ثبت نشده است.</p>
                    @endif
                </div>
            </div>
        </section>

        {{-- ══════════ اخبار + اطلاعات روستا ══════════ --}}
        <div class="cols" style="margin-top:34px">
            <section aria-labelledby="vnewsTitle">
                <header class="block-head reveal" style="margin-bottom:14px">
                    <div>
                        <span class="kicker-line">آخرین‌ها</span>
                        <h2 id="vnewsTitle">خبرهای روستای {{ $village->name }}</h2>
                        <p>اخبار و اطلاعیه‌های منتشرشدهٔ دهیاری</p>
                    </div>
                    <a class="link-more" href="{{ route('village.site.news', $village->slug) }}">همهٔ اخبار <svg><use href="#i-arrow"/></svg></a>
                </header>

                @if(isset($news) && $news->count() > 0)
                    <div class="vnews">
                        @foreach($news as $i => $item)
                            @php $__tone = ['a', 'b', 'c'][$i % 3]; @endphp
                            <a class="vn reveal" href="{{ route('village.site.news.show', [$village->slug, $item->slug]) }}" style="--d:{{ ($i % 3) * 60 }}ms">
                                <span class="thumb {{ $__tone }}" @if($item->image) style="background-image:url('{{ asset('storage/' . $item->image) }}');background-size:cover;background-position:center" @endif></span>
                                <div>
                                    <h3>
                                        @if($item->is_pinned)<span class="vg-pin">مهم</span>@endif
                                        {{ $item->title }}
                                    </h3>
                                    <p>{{ \Illuminate\Support\Str::limit($item->excerpt ?: strip_tags($item->body), 130) }}</p>
                                    <div class="meta">
                                        <span><svg><use href="#i-calendar"/></svg> {{ jalali($item->published_at ?: $item->created_at) }}</span>
                                        @if(($item->stat_views ?? 0) > 0)
                                            <span><svg><use href="#i-eye"/></svg> {{ $__fa($item->stat_views) }} بازدید</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="empty-note reveal">هنوز خبری برای این روستا منتشر نشده است.</div>
                @endif
            </section>

            <aside>
                <div class="panel reveal">
                    <h3>اطلاعات روستا</h3>
                    @if($village->section)
                        <div class="kv"><span>بخش</span><b>{{ $village->section }}</b></div>
                    @endif
                    @if($village->district)
                        <div class="kv"><span>دهستان</span><b>{{ $village->district }}</b></div>
                    @endif
                    @if($village->county)
                        <div class="kv"><span>شهرستان</span><b>{{ $village->county }}</b></div>
                    @endif
                    @if($village->province)
                        <div class="kv"><span>استان</span><b>{{ $village->province }}</b></div>
                    @endif
                    @if($village->council_head_name)
                        <div class="kv"><span>رئیس شورا</span><b>{{ $village->council_head_name }}</b></div>
                    @endif
                    @if($village->address)
                        <div class="kv"><span>نشانی</span><b>{{ $village->address }}</b></div>
                    @endif
                    @if($village->email)
                        <div class="kv"><span>رایانامه</span><b dir="ltr">{{ $village->email }}</b></div>
                    @endif
                    <div class="chips">
                        @if($__villageSection)
                            <a href="{{ request()->getScheme() }}://{{ $__villageSection->subdomain ?: 'visian' }}.{{ config('app.base_domain') }}">سایت بخش</a>
                        @endif
                        <a href="#mapTitle">نقشهٔ روستا</a>
                        <a href="{{ route('village.site.request.create', $village->slug) }}">خدمات الکترونیک</a>
                    </div>
                </div>

                @if($__neighbors->count() > 0)
                    <div class="panel reveal" style="margin-top:14px;--d:90ms">
                        <h3>روستاهای هم‌جوار</h3>
                        <div class="chips">
                            @foreach($__neighbors as $nb)
                                <a href="{{ route('public.village.show', $nb->slug) }}">{{ $nb->name }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>
        </div>

        {{-- ══════════ گالری ══════════ --}}
        @if(isset($images) && $images->count() > 0)
            <section class="block sub-block" aria-labelledby="vgalTitle">
                <header class="block-head reveal">
                    <div>
                        <span class="kicker-line">تصاویر</span>
                        <h2 id="vgalTitle">گالری تصاویر {{ $village->name }}</h2>
                        <p>تصاویر روستا، مراسم‌ها و طرح‌های اجراشده</p>
                    </div>
                    <a class="link-more" href="{{ route('village.site.gallery', $village->slug) }}">همهٔ تصاویر <svg><use href="#i-arrow"/></svg></a>
                </header>
                <div class="gallery">
                    @foreach($images as $i => $image)
                        @php $__tone = ['a', 'b', 'c', 'd'][$i % 4]; @endphp
                        <div class="gph {{ $__tone }} v-gallery-item reveal"
                             style="--d:{{ ($i % 4) * 60 }}ms"
                             data-image-src="{{ asset('storage/' . $image->image) }}"
                             data-title="{{ $image->title }}"
                             data-description="{{ $image->description }}">
                            <img src="{{ asset('storage/' . $image->image) }}" alt="{{ $image->title ?: $village->name }}" loading="lazy">
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ══════════ پروژه‌های عمرانی ══════════ --}}
        @if(isset($projects) && $projects->count() > 0)
            <section class="block sub-block" aria-labelledby="projTitle">
                <header class="block-head reveal">
                    <div>
                        <span class="kicker-line">عمران</span>
                        <h2 id="projTitle">پروژه‌های عمرانی {{ $village->name }}</h2>
                        <p>طرح‌های در دست اجرا و تکمیل‌شده</p>
                    </div>
                    <a class="link-more" href="{{ route('village.site.projects', $village->slug) }}">همهٔ پروژه‌ها <svg><use href="#i-arrow"/></svg></a>
                </header>
                <div class="vg-projs">
                    @foreach($projects as $i => $project)
                        <div class="vg-proj reveal" style="--d:{{ ($i % 3) * 60 }}ms">
                            <div class="vg-proj-head">
                                <span class="vg-ico"><svg><use href="#i-chart"/></svg></span>
                                <b>{{ $project->title }}</b>
                            </div>
                            @if($project->description)
                                <p>{{ \Illuminate\Support\Str::limit($project->description, 110) }}</p>
                            @endif
                            <div class="vg-bar"><span style="width:{{ max(0, min(100, (int) $project->progress)) }}%"></span></div>
                            <small>{{ $__fa($project->progress) }}٪ پیشرفت</small>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ══════════ امکانات ══════════ --}}
        @if(isset($facilities) && $facilities->count() > 0)
            <section class="block sub-block" aria-labelledby="facTitle">
                <header class="block-head reveal">
                    <div>
                        <span class="kicker-line">زیرساخت</span>
                        <h2 id="facTitle">امکانات {{ $village->name }}</h2>
                        <p>خدمات و زیرساخت‌های موجود در روستا</p>
                    </div>
                </header>
                <div class="items">
                    @foreach($facilities as $i => $facility)
                        <div class="item reveal {{ $facility->is_available ? '' : 'vg-off' }}" style="--d:{{ ($i % 4) * 60 }}ms">
                            <span class="ic">{{ $facility->icon ?: '🏛' }}</span>
                            <span>
                                <b>{{ $facility->name }}</b>
                                <small>{{ $facility->is_available ? 'موجود در روستا' : 'در حال پیگیری' }}</small>
                            </span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ══════════ شورای اسلامی ══════════ --}}
        @if(isset($councilMembers) && $councilMembers->count() > 0)
            <section class="block sub-block" aria-labelledby="councilTitle">
                <header class="block-head reveal">
                    <div>
                        <span class="kicker-line">شورا</span>
                        <h2 id="councilTitle">شورای اسلامی روستا</h2>
                        <p>اعضای شورای اسلامی {{ $village->name }}</p>
                    </div>
                    <a class="link-more" href="{{ route('village.site.council', $village->slug) }}">صفحهٔ شورا <svg><use href="#i-arrow"/></svg></a>
                </header>
                <div class="people">
                    @foreach($councilMembers as $i => $member)
                        <div class="person reveal" style="--d:{{ ($i % 3) * 60 }}ms">
                            @if($member->photo)
                                <img class="vg-av" src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}" loading="lazy">
                            @else
                                <span class="av">{{ mb_substr($member->name, 0, 1) }}</span>
                            @endif
                            <span>
                                <b>{{ $member->name }}</b>
                                <small>{{ $member->position }}</small>
                                @if($member->phone)
                                    <a class="vg-tel" href="tel:{{ $member->phone }}" dir="ltr">{{ $member->phone }}</a>
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ══════════ جاذبه‌ها ══════════ --}}
        @if(isset($attractions) && $attractions->count() > 0)
            <section class="block sub-block" aria-labelledby="attrTitle">
                <header class="block-head reveal">
                    <div>
                        <span class="kicker-line">جاذبه‌ها</span>
                        <h2 id="attrTitle">جاذبه‌های {{ $village->name }}</h2>
                        <p>طبیعت، تاریخ و زیارتگاه‌های روستا</p>
                    </div>
                    <a class="link-more" href="{{ route('village.site.attractions', $village->slug) }}">همهٔ جاذبه‌ها <svg><use href="#i-arrow"/></svg></a>
                </header>
                <div class="prods">
                    @foreach($attractions as $i => $attr)
                        <div class="prod reveal" style="--d:{{ ($i % 4) * 60 }}ms">
                            <div class="ph" @if($attr->image) style="background-image:url('{{ asset('storage/' . $attr->image) }}');background-size:cover;background-position:center" @endif>
                                @unless($attr->image)<svg><use href="#i-pin"/></svg>@endunless
                            </div>
                            <b>{{ $attr->name }}</b>
                            <small>{{ $attr->category ?: \Illuminate\Support\Str::limit($attr->description, 40) }}</small>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ══════════ مصوبات شورا ══════════ --}}
        <section class="block sub-block" aria-labelledby="decTitle">
            <header class="block-head reveal">
                <div>
                    <span class="kicker-line">مصوبات</span>
                    <h2 id="decTitle">مصوبات شورای اسلامی</h2>
                    <p>مصوبات ثبت‌شده در سامانه</p>
                </div>
                <a class="link-more" href="{{ route('village.site.resolutions', $village->slug) }}">همهٔ مصوبات <svg><use href="#i-arrow"/></svg></a>
            </header>
            <div class="vg-cta reveal">
                <div>
                    <b><svg><use href="#i-doc"/></svg> {{ $__fa($__resolutionCount) }} مصوبه ثبت شده</b>
                    <p>
                        @if($__resolutionCount > 0)
                            مشاهده و پیگیری میزان پیشرفت مصوبات شورای اسلامی {{ $village->name }}
                        @else
                            به‌زودی مصوبات شورای اسلامی روستا در این بخش منتشر می‌شود.
                        @endif
                    </p>
                </div>
                <a class="vg-cta-btn" href="{{ route('village.site.resolutions', $village->slug) }}">مشاهدهٔ مصوبات <svg><use href="#i-arrow"/></svg></a>
            </div>
        </section>

        {{-- ══════════ محصولات روستا ══════════ --}}
        <section class="block sub-block" aria-labelledby="prodTitle">
            <header class="block-head reveal">
                <div>
                    <span class="kicker-line">اقتصاد روستا</span>
                    <h2 id="prodTitle">محصولات روستا</h2>
                    <p>
                        @if(($productCount ?? 0) > 0)
                            {{ $__fa($productCount) }} محصول محلی برای فروش — عسل، لبنیات، صنایع دستی و…
                        @else
                            محصولات محلی و قابل فروش روستا — به‌زودی اضافه می‌شود.
                        @endif
                    </p>
                </div>
                <a class="link-more" href="{{ route('village.site.products', $village->slug) }}">مشاهدهٔ محصولات <svg><use href="#i-arrow"/></svg></a>
            </header>

            @if(isset($featuredProducts) && $featuredProducts->count() > 0)
                <div class="prods">
                    @foreach($featuredProducts as $i => $fp)
                        @php $fpimg = $fp->images->first(); @endphp
                        <a class="prod reveal" href="{{ route('village.site.products.show', [$village->slug, $fp->slug]) }}" style="--d:{{ ($i % 4) * 60 }}ms">
                            <div class="ph" @if($fpimg) style="background-image:url('{{ asset('storage/' . $fpimg->image) }}');background-size:cover;background-position:center" @endif>
                                @unless($fpimg){{ $fp->categoryIcon() }}@endunless
                            </div>
                            <b>{{ \Illuminate\Support\Str::limit($fp->title, 34) }}</b>
                            <small class="vg-price">{{ $fp->priceLabel() }}</small>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- ══════════ فایل‌ها و فرم‌ها ══════════ --}}
        @if(isset($downloads) && $downloads->isNotEmpty())
            <section class="block sub-block" aria-labelledby="fileTitle">
                <header class="block-head reveal">
                    <div>
                        <span class="kicker-line">دریافت</span>
                        <h2 id="fileTitle">فایل‌ها و فرم‌های قابل دانلود</h2>
                        <p>فرم‌ها و بخشنامه‌های مورد نیاز اهالی</p>
                    </div>
                    <a class="link-more" href="{{ route('village.site.downloads', $village->slug) }}">همهٔ فایل‌ها <svg><use href="#i-arrow"/></svg></a>
                </header>
                <div class="files">
                    @foreach($downloads as $i => $d)
                        <a class="file reveal" href="{{ route('village.site.download', [$village->slug, $d->id]) }}" style="--d:{{ ($i % 4) * 60 }}ms">
                            <span class="ic"><svg><use href="#i-doc"/></svg></span>
                            <span>
                                <b>{{ $d->title }}</b>
                                <small>
                                    {{ $d->sizeLabel() }}
                                    @if($d->download_count > 0) · {{ $__fa($d->download_count) }} بار دانلود @endif
                                </small>
                            </span>
                            <span class="dl">دریافت</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ══════════ ثبت درخواست ══════════ --}}
        <section class="block sub-block" aria-labelledby="reqTitle">
            <header class="block-head reveal">
                <div>
                    <span class="kicker-line">خدمات</span>
                    <h2 id="reqTitle">درخواست خود را ثبت کنید</h2>
                    <p>درخواست شما مستقیماً برای دهیاری {{ $village->name }} ارسال می‌شود</p>
                </div>
            </header>

            <div class="vg-req reveal">
                <div class="vg-req-txt">
                    <b><svg><use href="#i-send"/></svg> سامانهٔ درخواست‌های مردمی</b>
                    <p>درخواست‌های خود در زمینهٔ آب، برق، گاز، راه و … را آنلاین ثبت کنید و با کد پیگیری دنبال کنید.</p>
                </div>
                <div class="vg-req-actions">
                    <a class="vg-btn-primary" href="{{ route('village.site.request.create', $village->slug) }}"><svg><use href="#i-send"/></svg> ثبت درخواست</a>
                    <a class="vg-btn-ghost" href="{{ route('village.site.request.track', $village->slug) }}"><svg><use href="#i-search"/></svg> پیگیری</a>
                    <div class="vg-tariff" id="vTariffDropdown">
                        <button type="button" class="vg-btn-ghost vg-btn-tariff">
                            <span><svg><use href="#i-doc"/></svg> تعرفهٔ عوارض</span>
                            <span class="vg-caret">▾</span>
                        </button>
                        <div class="vg-tariff-menu" role="menu">
                            <span class="vg-menu-label">مشاهده</span>
                            <a href="{{ route('village.tariff.index', $village->slug) }}" role="menuitem">
                                <svg><use href="#i-doc"/></svg> تعرفهٔ کامل سال جاری
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ══════════ نقشه ══════════ --}}
        <section class="block sub-block" aria-labelledby="mapTitle">
            <header class="block-head reveal">
                <div>
                    <span class="kicker-line">موقعیت</span>
                    <h2 id="mapTitle">موقعیت {{ $village->name }} روی نقشه</h2>
                    <p>بخش {{ $village->section ?: 'چگنی' }}، شهرستان {{ $village->county ?: 'چگنی' }}، استان {{ $village->province ?: 'لرستان' }}</p>
                </div>
            </header>
            @include('partials.village-map', ['village' => $village])
        </section>

    </div>
</main>

@include('partials.gov-footer')
@include('partials.back-to-top')
@include('partials.gallery-lightbox')

<style>
    /* ═══ استایل‌های اختصاصی صفحهٔ روستا — پیشوند vg ═══ */
    .vg-cover { margin-top: 22px; border-radius: 26px; overflow: hidden; box-shadow: 0 24px 50px -34px rgba(74,27,15,.5); }
    .vg-cover img { display: block; width: 100%; max-height: 380px; object-fit: cover; }
    .facts { grid-template-columns: repeat(auto-fit, minmax(168px, 1fr)); }

    .vg-prose { font-size: 13.5px; line-height: 2.1; color: #5a4a3c; text-align: justify; }
    .vg-muted { color: #8a7a6a; font-style: italic; }
    .vg-note { margin: 14px 0 0; padding: 12px 14px; background: #fdf1dc; border-radius: 14px; font-size: 12.5px; line-height: 2; color: #7a5842; }
    .vg-note b { color: #8a5a08; }

    .vg-av { width: 46px; height: 46px; border-radius: 50%; object-fit: cover; flex: none; display: grid; place-items: center; background: #fdeee7; color: #c94a2b; font-family: 'Lalezar', Tahoma, sans-serif; font-size: 21px; }
    .vg-tel { display: inline-block; margin-top: 4px; font-family: 'Tanha', Tahoma, sans-serif; font-size: 11.5px; color: #c94a2b; text-decoration: none; }
    .vg-pin { display: inline-block; font-family: 'Tanha', Tahoma, sans-serif; font-size: 10.5px; font-weight: 700; background: #fdf1dc; color: #8a5a08; padding: 1px 7px; border-radius: 999px; margin-inline-end: 6px; vertical-align: 2px; }

    .vg-projs { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
    .vg-proj { background: #fffdf9; border-radius: 20px; padding: 18px; box-shadow: 0 12px 30px -26px rgba(74,27,15,.5); }
    .vg-proj-head { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 8px; }
    .vg-proj-head b { font-size: 13.5px; line-height: 1.8; }
    .vg-ico { width: 32px; height: 32px; border-radius: 10px; flex: none; display: grid; place-items: center; background: #fdeee7; color: #c94a2b; }
    .vg-ico svg { width: 16px; height: 16px; }
    .vg-proj p { margin: 0 0 12px; font-size: 12.5px; line-height: 1.9; color: #8a7a6a; }
    .vg-bar { height: 7px; border-radius: 999px; background: #f3ead9; overflow: hidden; }
    .vg-bar span { display: block; height: 100%; border-radius: 999px; background: linear-gradient(90deg, #f5b841, #e2603c); }
    .vg-proj small { display: block; margin-top: 7px; font-family: 'Tanha', Tahoma, sans-serif; font-size: 11.5px; color: #8a7a6a; }

    .item.vg-off { opacity: .55; }
    .item .ic { font-size: 17px; }

    .prod { text-decoration: none; color: inherit; }
    .prod .ph { overflow: hidden; }
    .vg-price { color: #c94a2b !important; font-weight: 700; }

    .vg-cta {
        display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;
        background: linear-gradient(150deg, #fdf1dc, #fbe6cf);
        border-radius: 24px; padding: 22px 24px;
        box-shadow: inset 0 0 0 1px #f3ddb8;
    }
    .vg-cta b { display: inline-flex; align-items: center; gap: 8px; font-size: 15px; color: #5c2313; }
    .vg-cta b svg { width: 17px; height: 17px; color: #b8801a; }
    .vg-cta p { margin: 6px 0 0; font-size: 13px; line-height: 2; color: #7a5842; }

    .vg-req {
        display: flex; align-items: center; justify-content: space-between; gap: 22px; flex-wrap: wrap;
        background: linear-gradient(150deg, #fdeee7, #fbdcd0);
        border-radius: 26px; padding: 24px 26px;
        box-shadow: inset 0 0 0 1px rgba(201,74,43,.16);
    }
    .vg-req-txt b { display: inline-flex; align-items: center; gap: 8px; font-size: 15px; color: #5c2313; }
    .vg-req-txt b svg { width: 17px; height: 17px; color: #c94a2b; }
    .vg-req-txt p { margin: 6px 0 0; font-size: 13px; line-height: 2; color: #7a5842; max-width: 520px; }
    .vg-req-actions { display: flex; align-items: center; gap: 9px; flex-wrap: wrap; }
    .vg-btn-primary, .vg-btn-ghost {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 12px 20px; border-radius: 999px; font-size: 13px; font-weight: 700;
        text-decoration: none; white-space: nowrap; cursor: pointer; border: 0;
    }
    .vg-btn-primary { background: linear-gradient(150deg, #e2603c, #c94a2b); color: #fff7e9; box-shadow: 0 14px 26px -18px rgba(201,74,43,.9); }
    .vg-btn-ghost { background: #fffdf9; color: #8e2f18; box-shadow: inset 0 0 0 1px rgba(201,74,43,.22); font-family: 'Mikhak', Tahoma, sans-serif; }
    .vg-btn-primary svg, .vg-btn-ghost svg { width: 14px; height: 14px; }
    .vg-btn-primary:hover { filter: brightness(1.05); }
    .vg-btn-ghost:hover { background: #fff; }
    .vg-cta-btn {
        display: inline-flex; align-items: center; gap: 7px; flex: none;
        background: linear-gradient(150deg, #f5b841, #d99413); color: #4a2b00;
        padding: 12px 22px; border-radius: 999px; font-size: 13px; font-weight: 700;
        text-decoration: none; white-space: nowrap; box-shadow: 0 14px 26px -18px rgba(184,128,26,.9);
    }
    .vg-cta-btn svg { width: 14px; height: 14px; }

    .vg-tariff { position: relative; }
    .vg-tariff-menu {
        position: absolute; inset-inline-start: 0; top: calc(100% + 8px); z-index: 30;
        min-width: 240px; background: #fffdf9; border-radius: 16px; padding: 8px;
        box-shadow: 0 24px 50px -26px rgba(74,27,15,.5), inset 0 0 0 1px #f2e4cd;
        display: none;
    }
    .vg-tariff.open .vg-tariff-menu { display: block; }
    .vg-menu-label { display: block; padding: 6px 10px; font-family: 'Tanha', Tahoma, sans-serif; font-size: 11px; color: #8a7a6a; }
    .vg-tariff-menu a {
        display: flex; align-items: center; gap: 8px; padding: 10px 12px; border-radius: 11px;
        font-size: 12.5px; color: #5a4a3c; text-decoration: none;
    }
    .vg-tariff-menu a:hover { background: #fdeee7; color: #c94a2b; }
    .vg-tariff-menu a svg { width: 14px; height: 14px; }

    @media (max-width: 900px) {
        .vg-projs { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 720px) {
        .vg-projs { grid-template-columns: 1fr; }
        .vg-req, .vg-cta { padding: 20px; }
        .vg-req-actions { width: 100%; }
        .vg-req-actions > a, .vg-tariff, .vg-tariff .vg-btn-tariff { width: 100%; justify-content: center; }
        .vg-tariff-menu { width: 100%; }
        .vg-cta-btn { width: 100%; justify-content: center; }
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

        function initTariff() {
            var dd = document.getElementById('vTariffDropdown');
            if (!dd) return;
            var btn = dd.querySelector('.vg-btn-tariff');
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                dd.classList.toggle('open');
            });
            document.addEventListener('click', function (e) {
                if (!dd.contains(e.target)) dd.classList.remove('open');
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') dd.classList.remove('open');
            });
            dd.querySelectorAll('.vg-tariff-menu a').forEach(function (a) {
                a.addEventListener('click', function () { dd.classList.remove('open'); });
            });
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initTariff);
        } else {
            initTariff();
        }
    })();
</script>

</body>
</html>

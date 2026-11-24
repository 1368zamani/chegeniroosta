@php
    $__ftrIsVillage = request()->routeIs('public.village.show')
        || request()->routeIs('village.site.*')
        || request()->routeIs('village.tariff.*');

    $__ftrVillageSlug = null;
    if ($__ftrIsVillage) {
        $__ftrVillageSlug = request()->route('slug')
            ?: (isset($village) && is_object($village) ? ($village->slug ?? null) : null);
    }

    $__ftrIsSection = app()->bound('currentSection') || request()->routeIs('section.*');
    $__ftrBase = 'https://' . config('app.base_domain');
    $__ftrTitle = 'سامانهٔ جامع روستاهای چگنی';
    if ($__ftrVillageSlug && isset($village)) {
        $__ftrTitle = 'روستای ' . $village->name;
    } elseif ($__ftrIsSection && isset($section)) {
        $__ftrTitle = 'بخش ' . $section->name;
    }
@endphp

<footer class="v3f">
    <div class="v3f-inner v3f-wrap">
        <div class="v3f-cols">
            <div class="v3f-brand">
                <span class="v3f-mark">چ</span>
                <div>
                    <h4>دربارهٔ سامانه</h4>
                    <p>{{ setting('footer_about', 'سامانهٔ اطلاع‌رسانی روستاهای شهرستان چگنی، استان لرستان.') }}</p>
                </div>
                <div class="v3f-crests">
                    <span class="v3f-crest">
                        <svg viewBox="0 0 100 100"><path d="M50 15 75 25v30c0 17-13 27-25 32-12-5-25-15-25-32V25Z" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="50" cy="50" r="10" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
                        <b>وزارت کشور</b>
                    </span>
                    <span class="v3f-crest">
                        <svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="38" fill="none" stroke="currentColor" stroke-width="2"/><text x="50" y="61" text-anchor="middle" font-size="26" font-weight="bold" fill="currentColor" font-family="Tahoma">لر</text></svg>
                        <b>استانداری لرستان</b>
                    </span>
                </div>
            </div>

            <div>
                <h5>لینک‌های مفید</h5>
                <ul class="v3f-list">
                    <li><a href="https://www.moi.ir" target="_blank" rel="noopener"><svg><use href="#i-ext"/></svg> وزارت کشور</a></li>
                    <li><a href="https://lorestan.ostan-lr.ir" target="_blank" rel="noopener"><svg><use href="#i-ext"/></svg> استانداری لرستان</a></li>
                    <li><a href="http://chegeni.gov.ir/" target="_blank" rel="noopener"><svg><use href="#i-ext"/></svg> فرمانداری چگنی</a></li>
                    <li><a href="https://tek.mcls.gov.ir" target="_blank" rel="noopener"><svg><use href="#i-ext"/></svg> تسهیلات اشتغال کشور</a></li>
                    <li><a href="https://mojavez.ir/" target="_blank" rel="noopener"><svg><use href="#i-ext"/></svg> درگاه ملی مجوزها</a></li>
                    <li><a href="https://adliran.ir/" target="_blank" rel="noopener"><svg><use href="#i-ext"/></svg> خدمات الکترونیک قضایی</a></li>
                    <li><a href="https://www.dolat.ir" target="_blank" rel="noopener"><svg><use href="#i-ext"/></svg> پایگاه اطلاع‌رسانی دولت</a></li>
                </ul>
            </div>

            <div>
                <h5>دسترسی سریع</h5>
                <ul class="v3f-list">
                    <li><a href="/"><svg><use href="#i-arrow"/></svg> صفحهٔ اصلی</a></li>
                    @if($__ftrVillageSlug)
                        <li><a href="{{ route('village.site.news', $__ftrVillageSlug) }}"><svg><use href="#i-arrow"/></svg> اخبار و اطلاعیه‌ها</a></li>
                        <li><a href="{{ route('village.site.dehyar', $__ftrVillageSlug) }}"><svg><use href="#i-arrow"/></svg> معرفی دهیار</a></li>
                        <li><a href="{{ route('village.site.council', $__ftrVillageSlug) }}"><svg><use href="#i-arrow"/></svg> شورای اسلامی روستا</a></li>
                    @elseif($__ftrIsSection)
                        <li><a href="/news"><svg><use href="#i-arrow"/></svg> اخبار و اطلاعیه‌ها</a></li>
                        <li><a href="/#villages"><svg><use href="#i-arrow"/></svg> فهرست روستاها</a></li>
                        @if($section->bakhshdar_name ?? false)
                            <li><a href="/bakhshdar"><svg><use href="#i-arrow"/></svg> معرفی بخشدار</a></li>
                        @endif
                    @else
                        <li><a href="/#sections"><svg><use href="#i-arrow"/></svg> فهرست بخش‌ها</a></li>
                        <li><a href="/officials/governor"><svg><use href="#i-arrow"/></svg> مسئولین شهرستان</a></li>
                    @endif
                    <li><a href="{{ route('search.index') }}"><svg><use href="#i-arrow"/></svg> جستجو در سامانه</a></li>
                    <li><a href="/sitemap.xml"><svg><use href="#i-arrow"/></svg> نقشهٔ سایت</a></li>
                    <li><a href="/feed.xml"><svg><use href="#i-arrow"/></svg> خوراک خبری (RSS)</a></li>
                </ul>
            </div>

            <div>
                <h5>اطلاعات تماس</h5>
                <ul class="v3f-list">
                    <li>
                        <svg><use href="#i-pin"/></svg>
                        @if(isset($section) && $section->address){{ $section->address }}@else لرستان، شهرستان چگنی، بخشداری @endif
                    </li>
                    @if(isset($section) && $section->phone)
                        <li><a href="tel:{{ $section->phone }}"><svg><use href="#i-phone"/></svg> <span dir="ltr">{{ $section->phone }}</span></a></li>
                    @else
                        <li><a href="tel:06633133225"><svg><use href="#i-phone"/></svg> <span dir="ltr">۰۶۶-۳۳۱۳۳۲۲۵</span></a></li>
                    @endif
                    @if(isset($section) && $section->email)
                        <li><a href="mailto:{{ $section->email }}"><svg><use href="#i-mail"/></svg> {{ $section->email }}</a></li>
                    @endif
                    <li><a href="{{ $__ftrBase }}"><svg><use href="#i-globe"/></svg> چگنی روستا</a></li>
                </ul>
            </div>
        </div>

        <div class="v3f-bottom">
            <span>{{ setting('footer_copyright', '© ' . date('Y') . ' — سامانه اطلاع‌رسانی روستاهای چگنی') }} — تمامی حقوق محفوظ است.</span>
            <span>{{ $__ftrTitle }}</span>
        </div>
    </div>
</footer>

<style>
    /* ═══ فوتر نسخهٔ ۳ — پیشوند v3f برای جلوگیری از تعارض ═══ */
    .v3f {
        margin-top: 56px;
        background:
            radial-gradient(600px 320px at 82% 0%, rgba(245,184,65,.12), transparent 62%),
            linear-gradient(160deg, #5c2313, #4a1b0f 62%, #3a160a);
        color: #f0ddcb;
        border-radius: 34px 34px 0 0;
        font-family: 'Mikhak', Tahoma, sans-serif;
    }
    .v3f-wrap { width: min(1200px, 94%); margin: 0 auto; }
    .v3f-inner { position: relative; padding: 46px 0 26px; }
    .v3f-cols { display: grid; grid-template-columns: 1.7fr 1fr 1fr 1.2fr; gap: 34px; }
    .v3f-brand { display: flex; gap: 14px; align-items: flex-start; flex-wrap: wrap; }
    .v3f-mark {
        width: 50px; height: 50px; flex: none; border-radius: 16px; display: grid; place-items: center;
        background: linear-gradient(150deg, #e2603c, #a4451f);
        font-family: 'Lalezar', Tahoma, sans-serif; font-size: 27px; line-height: 1; color: #fff7e9;
    }
    .v3f-brand h4 { color: #fff; font-size: 15px; margin: 0 0 6px; font-weight: 700; }
    .v3f-brand p { margin: 0; font-size: 12.5px; line-height: 2; color: #e6cbb6; }
    .v3f-crests { flex-basis: 100%; display: flex; flex-wrap: wrap; gap: 10px; margin-top: 16px; }
    .v3f-crest {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 7px 12px 7px 10px; border-radius: 12px;
        background: rgba(255,255,255,.05); box-shadow: inset 0 0 0 1px rgba(245,184,65,.22);
        color: #f5b841;
    }
    .v3f-crest svg { width: 22px; height: 22px; flex: none; }
    .v3f-crest b { font-family: 'Tanha', Tahoma, sans-serif; font-size: 11.5px; font-weight: 700; color: #e6d6c8; }
    .v3f h5 { color: #f8d489; font-size: 12.5px; margin: 4px 0 14px; font-weight: 700; }
    .v3f-list { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 9px; }
    .v3f-list li { font-size: 12.5px; color: #f0ddcb; display: flex; align-items: flex-start; gap: 7px; }
    .v3f-list a { font-size: 12.5px; color: #f0ddcb; text-decoration: none; display: inline-flex; align-items: center; gap: 7px; transition: color .18s; }
    .v3f-list a:hover { color: #f5b841; }
    .v3f-list svg { width: 13px; height: 13px; opacity: .65; flex: none; margin-top: 3px; }
    .v3f-bottom {
        display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;
        margin-top: 34px; padding-top: 18px;
        border-top: 1px solid rgba(245,184,65,.18);
        font-family: 'Tanha', Tahoma, sans-serif; font-size: 11.5px; color: #cfb7a4;
    }
    @media (max-width: 980px) { .v3f-cols { grid-template-columns: 1fr 1fr; gap: 26px; } }
    @media (max-width: 620px) { .v3f-cols { grid-template-columns: 1fr; } .v3f { border-radius: 26px 26px 0 0; } }
</style>

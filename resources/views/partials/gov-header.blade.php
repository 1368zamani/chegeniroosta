@php
    if (!function_exists('daily_persian_verse')) {
        function daily_persian_verse() {
            $verses = setting('poetry_verses', null);

            if (!is_array($verses) || count($verses) === 0) {
                $verses = [
                    ['text' => 'بنی آدم اعضای یک پیکرند / که در آفرینش ز یک گوهرند', 'poet' => 'سعدی'],
                    ['text' => 'چو عضوی به درد آورد روزگار / دگر عضوها را نماند قرار', 'poet' => 'سعدی'],
                    ['text' => 'توانا بود هر که دانا بود / ز دانش دل پیر برنا بود', 'poet' => 'فردوسی'],
                    ['text' => 'دوش دیدم که ملائک در میخانه زدند / گِل آدم بسرشتند و به پیمانه زدند', 'poet' => 'حافظ'],
                ];
            }

            $dayOfYear = (int) now()->format('z');
            return $verses[$dayOfYear % count($verses)];
        }
    }

    if (!function_exists('gregorian_to_jalali_hdr')) {
        function gregorian_to_jalali_hdr($gy, $gm, $gd) {
            $g_d_m = [0,31,59,90,120,151,181,212,243,273,304,334];
            $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
            $days = 355666 + (365 * $gy) + ((int)(($gy2 + 3) / 4)) - ((int)(($gy2 + 99) / 100))
                  + ((int)(($gy2 + 399) / 400)) + $gd + $g_d_m[$gm - 1];
            $jy = -1595 + (33 * ((int)($days / 12053)));
            $days %= 12053;
            $jy += 4 * ((int)($days / 1461));
            $days %= 1461;
            if ($days > 365) {
                $jy += (int)(($days - 1) / 365);
                $days = ($days - 1) % 365;
            }
            if ($days < 186) {
                $jm = 1 + (int)($days / 31);
                $jd = 1 + ($days % 31);
            } else {
                $jm = 7 + (int)(($days - 186) / 30);
                $jd = 1 + (($days - 186) % 30);
            }
            return [$jy, $jm, $jd];
        }
    }
    if (!function_exists('today_jalali_hdr')) {
        function today_jalali_hdr() {
            $now = now();
            [$jy, $jm, $jd] = gregorian_to_jalali_hdr($now->year, $now->month, $now->day);
            $months = ['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'];
            $days = ['یکشنبه','دوشنبه','سه‌شنبه','چهارشنبه','پنجشنبه','جمعه','شنبه'];
            return $days[$now->dayOfWeek] . '، ' . $jd . ' ' . $months[$jm - 1] . ' ' . $jy;
        }
    }

    /* ── تشخیص زمینهٔ صفحه ── */
    $__hdrIsVillage = request()->routeIs('public.village.show')
        || request()->routeIs('village.site.*')
        || request()->routeIs('village.tariff.*');

    $__hdrVillageSlug = null;
    if ($__hdrIsVillage) {
        $__hdrVillageSlug = request()->route('slug')
            ?: (isset($village) && is_object($village) ? ($village->slug ?? null) : null);
    }

    $__hdrIsSection = app()->bound('currentSection') || request()->routeIs('section.*');

    $homeUrl = '/';
    $homeActive = request()->path() === '/';
    if ($__hdrIsVillage) {
        if ($__hdrVillageSlug) {
            $homeUrl = '/village/' . $__hdrVillageSlug;
        }
        $homeActive = false;
    }

    $brandName = 'چگنی';
    $brandSub = 'شهرستان چگنی · لرستان';
    $brandUrl = request()->getScheme() . '://' . config('app.base_domain');
    $brandLetter = 'چ';

    if (isset($village) && $__hdrIsVillage && !empty($village->name)) {
        $brandName = mb_substr($village->name, 0, 18);
        $brandSub = 'روستای بخش ' . ($section->name ?? 'چگنی');
        $brandLetter = mb_substr($village->name, 0, 1);
    } elseif (isset($section) && !empty($section->name)) {
        $brandName = mb_substr($section->name, 0, 18);
        $brandSub = 'پایگاه اطلاع‌رسانی بخش';
        $brandLetter = mb_substr($section->name, 0, 1);
    }

    $__hdrSections = \App\Models\Section::where('is_active', true)->orderBy('name')->get();
@endphp

{{-- ══════════ آیکون‌ها ══════════ --}}
<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
    <symbol id="i-home" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5"/><path d="M5.5 9.5V21h13V9.5"/></symbol>
    <symbol id="i-search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.2-3.2"/></symbol>
    <symbol id="i-user" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 20a8 8 0 0 1 16 0"/></symbol>
    <symbol id="i-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="m6 9 6 6 6-6"/></symbol>
    <symbol id="i-menu" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></symbol>
    <symbol id="i-calendar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18"/></symbol>
    <symbol id="i-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></symbol>
    <symbol id="i-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m11 6-6 6 6 6"/></symbol>
    <symbol id="i-up" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><path d="m6 11 6-6 6 6"/></symbol>
    <symbol id="i-homes" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5 9 4l6 5.5"/><path d="M5 8.5V19h8V8.5"/><path d="M15 10.5 20 6l4 4.5"/><path d="M17 9.5V19h7V9.5"/></symbol>
    <symbol id="i-news" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 8h6M7 12h10M7 16h10"/></symbol>
    <symbol id="i-pin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 11-8 11S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></symbol>
    <symbol id="i-chart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></symbol>
    <symbol id="i-sprout" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21v-7"/><path d="M12 14c0-4 3-7 7-7 0 4-3 7-7 7Z"/><path d="M12 14C12 10.5 9.5 8 6 8c0 3.5 2.5 6 6 6Z"/></symbol>
    <symbol id="i-phone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h4l2 5-2.5 1.5a12 12 0 0 0 5 5L16 12l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 4 5a2 2 0 0 1 2-2Z"/></symbol>
    <symbol id="i-image" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="3"/><circle cx="9" cy="10" r="2"/><path d="m4 18 5-5 4 4 3-3 4 4"/></symbol>
    <symbol id="i-users" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M17 5.5a3.5 3.5 0 0 1 0 6.6"/><path d="M18.5 20a6.4 6.4 0 0 0-2.2-4.8"/></symbol>
    <symbol id="i-map" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 4 6 2 6-2v14l-6 2-6-2-6 2V6l6-2Z"/><path d="M9 4v14M15 6v14"/></symbol>
    <symbol id="i-download" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"/><path d="m7 11 5 5 5-5"/><path d="M4 20h16"/></symbol>
    <symbol id="i-cart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h2.5l2.2 10.2a2 2 0 0 0 2 1.6h7.6a2 2 0 0 0 2-1.6L21 8H6"/><circle cx="9.5" cy="19.5" r="1.4"/><circle cx="17.5" cy="19.5" r="1.4"/></symbol>
    <symbol id="i-doc" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5Z"/><path d="M14 3v5h5"/><path d="M9 13h6M9 17h4"/></symbol>
    <symbol id="i-send" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3 10.5 13.5"/><path d="M21 3 14 21l-3.5-7.5L3 10l18-7Z"/></symbol>
    <symbol id="i-ext" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3h7v7"/><path d="M21 3 11 13"/><path d="M19 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h5"/></symbol>
    <symbol id="i-mail" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></symbol>
    <symbol id="i-globe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/></symbol>
    <symbol id="i-gov" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V10l7-5 7 5v11"/><path d="M10 21v-5h4v5"/></symbol>
    <symbol id="i-star" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 2.8 5.9 6.2.8-4.6 4.4 1.2 6.4L12 17.4 6.4 20.5l1.2-6.4L3 9.7l6.2-.8L12 3Z"/></symbol>
    <symbol id="i-water" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3s6 6.5 6 10.5a6 6 0 0 1-12 0C6 9.5 12 3 12 3Z"/></symbol>
    <symbol id="i-power" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h6l-1 8 9-12h-6l1-8Z"/></symbol>
    <symbol id="i-road" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 3 4 21M17 3l3 18"/><path d="M12 5v3M12 12v3M12 19v2"/></symbol>
    <symbol id="i-school" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 9 4.5-9 4.5L3 7.5 12 3Z"/><path d="M6 10v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5"/></symbol>
    <symbol id="i-health" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-8-4.6-8-10a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 11c0 5.4-8 10-8 10Z"/></symbol>
</svg>

{{-- ══════════ نوار بالای صفحه ══════════ --}}
<div class="v3h-strip">
    <div class="v3h-wrap v3h-strip-inner">
        <div class="v3h-strip-side">
            <span class="v3h-strip-date"><svg width="13" height="13"><use href="#i-calendar"/></svg> {{ today_jalali_hdr() }}</span>
            <a class="v3h-strip-link" href="{{ request()->getScheme() }}://{{ config('app.base_domain') }}">پرتال مرکزی چگنی</a>
        </div>
        @php $verse = daily_persian_verse(); @endphp
        <div class="v3h-strip-poem">
            <span>{{ $verse['text'] }}</span>
            <b>— {{ $verse['poet'] }}</b>
        </div>
    </div>
</div>

{{-- ══════════ هدر شناور ══════════ --}}
<div class="v3h-pill-wrap">
    <div class="v3h-pill" id="navPill">
        <a class="v3h-brand" href="{{ $brandUrl }}">
            <span class="v3h-brand-mark">{{ $brandLetter }}</span>
            <span class="v3h-brand-txt">
                <b>{{ $brandName }}</b>
                <small>{{ $brandSub }}</small>
            </span>
        </a>

        <nav class="v3h-links" aria-label="منوی اصلی">
            <a class="{{ $homeActive ? 'on' : '' }}" href="{{ $homeUrl }}">
                <svg width="15" height="15"><use href="#i-home"/></svg> خانه
            </a>

            @if($__hdrIsVillage)
                <a href="/news" class="{{ str_contains(request()->path(), 'news') ? 'on' : '' }}">اخبار</a>
                <div class="v3h-item">
                    <a href="#vaboutTitle">معرفی روستا <svg><use href="#i-chevron"/></svg></a>
                    <div class="v3h-menu">
                        <a href="#vaboutTitle">دربارهٔ {{ $village->name ?? 'روستا' }}</a>
                        <a href="#facTitle">امکانات روستا</a>
                        <a href="#councilTitle">شورای اسلامی</a>
                    </div>
                </div>
                <a href="#reqTitle">خدمات</a>
                <a href="#vgalTitle">تصاویر</a>
                @if($__hdrVillageSlug)
                    <div class="v3h-item">
                        <a href="#">بخش {{ $section->name ?? 'چگنی' }} <svg><use href="#i-chevron"/></svg></a>
                        <div class="v3h-menu">
                            <a href="{{ request()->getScheme() }}://{{ config('app.base_domain') }}">پرتال چگنی</a>
                            <a href="{{ request()->getScheme() }}://{{ $section->subdomain ?? 'visian' }}.{{ config('app.base_domain') }}">صفحهٔ بخش</a>
                            <a href="{{ route('village.site.dehyar', $__hdrVillageSlug) }}">معرفی دهیار</a>
                            <a href="{{ route('village.site.council', $__hdrVillageSlug) }}">شورای روستا</a>
                        </div>
                    </div>
                @endif
            @elseif(isset($section))
                <a href="/#villages">روستاها</a>
                <a href="/news" class="{{ str_contains(request()->path(), 'news') ? 'on' : '' }}">اخبار</a>
                <a href="/bakhshdar" class="{{ str_contains(request()->path(), 'bakhshdar') ? 'on' : '' }}">دربارهٔ بخش</a>
                <a href="/council" class="{{ str_contains(request()->path(), 'council') ? 'on' : '' }}">شورای بخش</a>
            @else
                <div class="v3h-item">
                    <a href="#sections">بخش‌ها <svg><use href="#i-chevron"/></svg></a>
                    <div class="v3h-menu">
                        @forelse($__hdrSections as $s)
                            @php
                                $__sub = $s->subdomain;
                                $__url = $__sub ? "https://{$__sub}.chegeniroosta.ir" : '/#sections';
                            @endphp
                            <a href="{{ $__url }}">بخشداری {{ $s->name }}</a>
                        @empty
                            <a href="/#sections">فهرست بخش‌ها</a>
                        @endforelse
                    </div>
                </div>
                <div class="v3h-item">
                    <a href="#officials">مسئولین <svg><use href="#i-chevron"/></svg></a>
                    <div class="v3h-menu">
                        <a href="/officials/governor">فرماندار</a>
                        <a href="/officials/deputies">معاونین فرماندار</a>
                        <a href="/officials/bakhshdars">بخشداران</a>
                    </div>
                </div>
                <a href="/#news">اخبار</a>
                <a href="/#sections">روستاها</a>
            @endif
        </nav>

        <div class="v3h-actions">
            <a class="v3h-round" href="{{ route('search.index') }}" aria-label="جستجو">
                <svg><use href="#i-search"/></svg>
            </a>
            @auth
                <a class="v3h-btn" href="{{ route('admin.dashboard') }}">
                    <svg><use href="#i-user"/></svg> پنل
                </a>
            @else
                <a class="v3h-btn" href="{{ route('login') }}">
                    <svg><use href="#i-user"/></svg> ورود
                </a>
            @endauth
            <button class="v3h-burger" id="burger" aria-label="منو" aria-expanded="false">
                <svg><use href="#i-menu"/></svg>
            </button>
        </div>
    </div>
</div>

{{-- ══════════ نوار دولت وفاق ملی ══════════ --}}
<div class="v3h-consensus">
    <div class="v3h-wrap v3h-consensus-inner">
        <span>{{ setting('banner_highlight_1', 'دولت چهاردهم') }}</span>،
        <span>{{ setting('banner_highlight_2', 'دولت وفاق ملی') }}</span>
    </div>
</div>

<style>
    /* ═══ فونت‌های خودمیزبان (یک‌بار در هر صفحه) ═══ */
    @font-face{font-family:'Lalezar';src:url('/fonts/lalezar-arabic.woff2') format('woff2');font-weight:400;font-style:normal;font-display:swap;unicode-range:U+0600-06FF,U+0750-077F,U+200C-200E,U+2010-2011,U+FB50-FDFF,U+FE70-FEFC}
    @font-face{font-family:'Mikhak';src:url('/fonts/mikhak-regular.woff2') format('woff2');font-weight:400;font-style:normal;font-display:swap}
    @font-face{font-family:'Mikhak';src:url('/fonts/mikhak-medium.woff2') format('woff2');font-weight:500;font-style:normal;font-display:swap}
    @font-face{font-family:'Mikhak';src:url('/fonts/mikhak-bold.woff2') format('woff2');font-weight:700;font-style:normal;font-display:swap}
    @font-face{font-family:'Tanha';src:url('/fonts/tanha-regular.woff') format('woff');font-weight:400;font-style:normal;font-display:swap}
    /* ═══ هدر نسخهٔ ۳ — کلاس‌ها با پیشوند v3h تا با استایل صفحات قدیمی تعارض نداشته باشند ═══ */
    .v3h-wrap { width: min(1200px, 94%); margin: 0 auto; }
    .v3h-strip {
        background: #4a1b0f; color: #f1ddc9;
        font-family: 'Tanha', 'Mikhak', Tahoma, sans-serif; font-size: 12.5px;
    }
    .v3h-strip-inner { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 8px 0; flex-wrap: wrap; }
    .v3h-strip-side { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
    .v3h-strip-date { display: inline-flex; align-items: center; gap: 6px; opacity: .92; }
    .v3h-strip-date svg { width: 13px; height: 13px; }
    .v3h-strip-link {
        color: #f5b841; text-decoration: none; font-weight: 700;
        padding-inline-start: 14px; border-inline-start: 1px solid rgba(245,184,65,.35);
    }
    .v3h-strip-link:hover { color: #ffd98a; }
    .v3h-strip-poem { display: inline-flex; align-items: center; gap: 7px; opacity: .9; }
    .v3h-strip-poem b { font-weight: 400; color: #f5b841; }

    .v3h-pill-wrap {
        position: sticky; top: 10px; z-index: 60;
        padding: 14px 0 4px;
    }
    .v3h-pill {
        width: min(1200px, 94%); margin: 0 auto;
        display: flex; align-items: center; gap: 14px;
        background: rgba(255, 253, 249, .92);
        -webkit-backdrop-filter: blur(14px); backdrop-filter: blur(14px);
        border-radius: 999px; padding: 9px 12px 9px 18px;
        box-shadow: 0 14px 34px -22px rgba(74, 27, 15, .45), inset 0 0 0 1px rgba(201, 74, 43, .1);
        font-family: 'Mikhak', Tahoma, sans-serif;
    }
    .v3h-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: #2b2119; min-width: 0; }
    .v3h-brand-mark {
        width: 42px; height: 42px; flex: none; border-radius: 14px;
        display: grid; place-items: center;
        background: linear-gradient(150deg, #e2603c, #a4451f);
        color: #fff7e9; font-family: 'Lalezar', Tahoma, sans-serif; font-size: 24px; line-height: 1;
        box-shadow: 0 8px 18px -10px rgba(201,74,43,.8);
    }
    .v3h-brand-txt { display: flex; flex-direction: column; line-height: 1.3; min-width: 0; }
    .v3h-brand-txt b { font-family: 'Lalezar', Tahoma, sans-serif; font-weight: 400; font-size: 21px; color: #5c2313; white-space: nowrap; }
    .v3h-brand-txt small { font-family: 'Tanha', Tahoma, sans-serif; font-size: 11.5px; color: #8a7a6a; white-space: nowrap; }

    .v3h-links { display: flex; align-items: center; gap: 2px; margin-inline-start: auto; }
    .v3h-links > a, .v3h-item > a {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 13px; border-radius: 999px;
        font-size: 13.5px; font-weight: 600; color: #5a4a3c;
        text-decoration: none; white-space: nowrap; transition: background .18s, color .18s;
    }
    .v3h-links > a:hover, .v3h-item > a:hover { background: #fdeee7; color: #c94a2b; }
    .v3h-links > a.on { background: #c94a2b; color: #fff7e9; }
    .v3h-links svg { width: 14px; height: 14px; }
    .v3h-item { position: relative; }
    .v3h-item > a svg { transition: transform .2s; }
    .v3h-item:hover > a svg { transform: rotate(180deg); }
    .v3h-menu {
        position: absolute; inset-inline-start: 0; top: calc(100% + 10px);
        min-width: 218px; background: #fffdf9; border-radius: 18px; padding: 8px;
        box-shadow: 0 24px 50px -26px rgba(74,27,15,.5), inset 0 0 0 1px rgba(242,228,205,.9);
        display: flex; flex-direction: column; gap: 2px;
        opacity: 0; visibility: hidden; transform: translateY(-6px);
        transition: opacity .16s, transform .16s, visibility .16s;
    }
    .v3h-item:hover .v3h-menu, .v3h-item:focus-within .v3h-menu { opacity: 1; visibility: visible; transform: none; }
    .v3h-menu a {
        padding: 9px 12px; border-radius: 11px; font-size: 13px;
        color: #5a4a3c; text-decoration: none; display: flex; align-items: center; justify-content: space-between; gap: 8px;
    }
    .v3h-menu a:hover { background: #fdeee7; color: #c94a2b; }

    .v3h-actions { display: flex; align-items: center; gap: 7px; flex: none; }
    .v3h-round {
        width: 38px; height: 38px; border-radius: 50%; display: grid; place-items: center;
        background: #fdeee7; color: #c94a2b; text-decoration: none; transition: background .18s;
    }
    .v3h-round:hover { background: #fbdcd0; }
    .v3h-round svg { width: 17px; height: 17px; }
    .v3h-btn {
        display: inline-flex; align-items: center; gap: 6px;
        background: linear-gradient(150deg, #e2603c, #c94a2b);
        color: #fff7e9; text-decoration: none;
        padding: 10px 16px; border-radius: 999px; font-size: 13px; font-weight: 700;
        box-shadow: 0 12px 22px -16px rgba(201,74,43,.9);
    }
    .v3h-btn svg { width: 15px; height: 15px; }
    .v3h-btn:hover { filter: brightness(1.05); }
    .v3h-burger {
        display: none; width: 38px; height: 38px; border-radius: 12px; border: 0;
        background: #fdeee7; color: #c94a2b; cursor: pointer; place-items: center;
    }
    .v3h-burger svg { width: 18px; height: 18px; }

    .v3h-consensus {
        background: linear-gradient(90deg, rgba(245,184,65,.16), rgba(245,184,65,.05));
        border-block: 1px solid rgba(245,184,65,.22);
        font-family: 'Tanha', Tahoma, sans-serif; font-size: 11.5px; color: #8a5a08;
    }
    .v3h-consensus-inner { padding: 7px 0; text-align: center; letter-spacing: .2px; }

    @media (max-width: 1080px) {
        .v3h-links { display: none; }
        .v3h-pill { padding: 8px 10px 8px 14px; }
        .v3h-burger { display: grid; }
        .v3h-pill.open .v3h-links {
            display: flex; flex-direction: column; align-items: stretch;
            position: absolute; inset-inline: 3vw; top: calc(100% + 8px);
            background: #fffdf9; border-radius: 22px; padding: 10px;
            box-shadow: 0 26px 60px -30px rgba(74,27,15,.55), inset 0 0 0 1px rgba(242,228,205,.9);
        }
        .v3h-pill { position: relative; }
        .v3h-pill.open .v3h-links > a, .v3h-pill.open .v3h-item > a { width: 100%; justify-content: space-between; }
        .v3h-pill.open .v3h-menu {
            position: static; opacity: 1; visibility: visible; transform: none;
            box-shadow: none; padding: 4px 8px; display: none; background: transparent;
        }
        .v3h-pill.open .v3h-item.open .v3h-menu { display: flex; }
        .v3h-strip-poem { display: none; }
    }
    @media (max-width: 560px) {
        .v3h-brand-txt small { display: none; }
        .v3h-brand-txt b { font-size: 18px; }
        .v3h-btn { padding: 9px 12px; font-size: 12.5px; }
    }
</style>

<script>
    (function () {
        var pill = document.getElementById('navPill');
        var burger = document.getElementById('burger');
        if (pill && burger) {
            burger.addEventListener('click', function (e) {
                e.stopPropagation();
                var open = pill.classList.toggle('open');
                burger.setAttribute('aria-expanded', String(open));
            });
        }
        document.querySelectorAll('.v3h-item > a').forEach(function (a) {
            a.addEventListener('click', function (e) {
                if (window.innerWidth > 1080) return;
                if (a.getAttribute('href') === '#' || a.getAttribute('href').charAt(0) === '#') {
                    e.preventDefault();
                    a.parentElement.classList.toggle('open');
                }
            });
        });
        document.addEventListener('click', function (e) {
            if (!pill || !pill.classList.contains('open')) return;
            if (pill.contains(e.target)) return;
            pill.classList.remove('open');
            if (burger) burger.setAttribute('aria-expanded', 'false');
        });
    })();
</script>

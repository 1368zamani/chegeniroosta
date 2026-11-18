{{--
    تصویر هیرو — نسخهٔ ۳.۱
    یک پارسیال برای هر سه صفحه (پرتال، بخش، روستا) با انتخاب خودکار طرح:

        ترتیب اولویت را با $order بدهید:
            ['photo', 'symbol']          → پست‌کارت، و اگر عکسی نبود نماد تصویری
            ['dehyar', 'photo', 'symbol'] → پرترهٔ دهیار، بعد پست‌کارت، بعد نماد

    ورودی‌ها:
        $photo        آدرس کامل عکس شاخص (یا null)
        $dehyarPhoto  آدرس کامل عکس دهیار (یا null)
        $dehyar       ['name','role','org','phone','hours','url','cta']
        $title        نام کوتاه برای نمایش بزرگ
        $subtitle     زیرعنوان
        $kicker       برچسب کوچک بالای نام (فقط پست‌کارت)
        $pin          چیپ موقعیت بالای کارت (فقط پست‌کارت)
        $stats        آرایهٔ رشته‌ها برای چیپ‌های آماری (فقط پست‌کارت)
        $initial      حرف ابتدای نام برای نماد تصویری
--}}
@php
    $__order   = $order ?? ['photo', 'symbol'];
    $__photo   = $photo ?? null;
    $__dehyarP = $dehyarPhoto ?? null;
    $__d       = $dehyar ?? [];
    $__title   = $title ?? '';
    $__sub     = $subtitle ?? '';
    $__kicker  = $kicker ?? null;
    $__pin     = $pin ?? null;
    $__stats   = array_values(array_filter($stats ?? []));

    $__variant = 'symbol';
    foreach ($__order as $__p) {
        if ($__p === 'dehyar' && $__dehyarP) { $__variant = 'dehyar'; break; }
        if ($__p === 'photo'  && $__photo)   { $__variant = 'photo';  break; }
        if ($__p === 'symbol')               { $__variant = 'symbol'; break; }
    }
@endphp

<div class="hero-art reveal" style="--d:120ms">

    @if($__variant === 'photo')
        {{-- ── پست‌کارت ── --}}
        <div class="hcard">
            <img src="{{ $__photo }}" alt="{{ $alt ?? $__title }}" loading="eager" decoding="async">
            @if($__pin)
                <span class="hcard-pin">
                    <svg><use href="#i-pin"/></svg>
                    {{ $__pin }}
                </span>
            @endif
            <div class="hcard-copy">
                @if($__kicker)<small>{{ $__kicker }}</small>@endif
                <b>{{ $__title }}</b>
                @if($__sub)<p>{{ $__sub }}</p>@endif
                @if(count($__stats))
                    <div class="hcard-stats">
                        @foreach($__stats as $__st)<i>{{ $__st }}</i>@endforeach
                    </div>
                @endif
            </div>
        </div>

    @elseif($__variant === 'dehyar')
        {{-- ── پرترهٔ دهیار ── --}}
        <div class="hdehyar">
            <img class="hdehyar-photo" src="{{ $__dehyarP }}" alt="{{ $__d['name'] ?? $__title }}" loading="eager" decoding="async">
            <span class="hdehyar-role">{{ $__d['role'] ?? 'دهیار روستا' }}</span>
            <h3>{{ ($__d['name'] ?? '') ?: ('دهیاری ' . $__title) }}</h3>
            @if(!empty($__d['org']))<small>{{ $__d['org'] }}</small>@endif

            @if(!empty($__d['phone']))
                <div class="hdehyar-row"><span>تلفن دهیاری</span><b>{{ $__d['phone'] }}</b></div>
            @endif
            @if(!empty($__d['hours']))
                <div class="hdehyar-row"><span>ساعات پاسخگویی</span><b>{{ $__d['hours'] }}</b></div>
            @endif

            @if(!empty($__d['url']))
                <a class="hdehyar-btn" href="{{ $__d['url'] }}">
                    <svg><use href="#i-arrow"/></svg>
                    {{ $__d['cta'] ?? 'پروفایل دهیار' }}
                </a>
            @endif
        </div>

    @else
        {{-- ── نماد تصویری (بدون نیاز به عکس) ── --}}
        <div class="hsym">
            <svg class="hsym-scene" viewBox="0 0 390 420" fill="none" aria-hidden="true" preserveAspectRatio="xMidYMax meet">
                <circle cx="300" cy="86" r="46" fill="#f5b841" opacity=".5"/>
                <circle cx="300" cy="86" r="30" fill="#f5b841"/>
                <g fill="#27689f" opacity=".16">
                    <circle cx="70" cy="96" r="16"/><circle cx="96" cy="96" r="22"/><circle cx="126" cy="96" r="14"/>
                </g>
                <path d="M0 300 L96 176 L170 262 L232 196 L330 300 Z" fill="#e2603c" opacity=".85"/>
                <path d="M120 300 L228 168 L330 300 Z" fill="#c94a2b"/>
                <path d="M228 168 L262 209 L228 235 L194 209 Z" fill="#fff7e9" opacity=".85"/>
                <path d="M0 330 H390 V420 H0 Z" fill="#fdf1dc"/>
                <path d="M0 330 Q 60 300 120 330 T 240 330 T 390 322 V420 H0 Z" fill="#f7d9cc"/>
                <g stroke="#b8801a" stroke-width="3" stroke-linecap="round">
                    <path d="M60 420 V 356"/><path d="M52 366 L 60 352 L 68 366"/><path d="M52 382 L 60 368 L 68 382"/>
                    <path d="M96 420 V 350"/><path d="M88 360 L 96 346 L 104 360"/><path d="M88 376 L 96 362 L 104 376"/>
                    <path d="M132 420 V 356"/><path d="M124 366 L 132 352 L 140 366"/><path d="M124 382 L 132 368 L 140 382"/>
                </g>
                <g>
                    <rect x="268" y="318" width="76" height="52" rx="6" fill="#fffdf9" stroke="#efe2d1"/>
                    <path d="M260 320 L306 284 L352 320 Z" fill="#8e2f18"/>
                    <rect x="296" y="342" width="20" height="28" rx="3" fill="#c94a2b"/>
                    <rect x="276" y="330" width="12" height="12" rx="2" fill="#f5b841"/>
                </g>
            </svg>
            <div class="hsym-plate">
                <b>{{ $__title }}</b>
                @if($__sub)<small>{{ $__sub }}</small>@endif
            </div>
        </div>
    @endif

</div>

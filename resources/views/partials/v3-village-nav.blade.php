{{--
    نان‌ریزه + عنوان + ناوبری فرعی صفحات روستا (نسخهٔ ۳)
    ورودی: $village
    ورودی اختیاری: $label  (عنوان صفحه)، $heading (نمایش h1، پیش‌فرض false)
--}}
@php
    $__slug = $village->slug;
    $__label = $label ?? $village->name;
    $__showHeading = $heading ?? false;
    $__tabs = [
        ['label' => 'خانه', 'url' => route('public.village.show', $__slug), 'on' => false],
        ['label' => 'اخبار', 'url' => route('village.site.news', $__slug), 'on' => request()->routeIs('village.site.news*')],
        ['label' => 'درباره', 'url' => route('village.site.about', $__slug), 'on' => request()->routeIs('village.site.about')],
        ['label' => 'شورا', 'url' => route('village.site.council', $__slug), 'on' => request()->routeIs('village.site.council')],
        ['label' => 'دهیاری', 'url' => route('village.site.dehyar', $__slug), 'on' => request()->routeIs('village.site.dehyar')],
        ['label' => 'امکانات', 'url' => route('village.site.facilities', $__slug), 'on' => request()->routeIs('village.site.facilities')],
        ['label' => 'پروژه‌ها', 'url' => route('village.site.projects', $__slug), 'on' => request()->routeIs('village.site.projects')],
        ['label' => 'مصوبات', 'url' => route('village.site.resolutions', $__slug), 'on' => request()->routeIs('village.site.resolutions')],
        ['label' => 'محصولات', 'url' => route('village.site.products', $__slug), 'on' => request()->routeIs('village.site.products*')],
        ['label' => 'فایل‌ها', 'url' => route('village.site.downloads', $__slug), 'on' => request()->routeIs('village.site.downloads')],
        ['label' => 'عوارض', 'url' => route('village.tariff.index', $__slug), 'on' => request()->routeIs('village.tariff.*')],
        ['label' => 'ثبت درخواست', 'url' => route('village.site.request.create', $__slug), 'on' => request()->routeIs('village.site.request.*')],
    ];
@endphp
<div class="v3i-crumbs">
    <div class="crumbs">
        <a href="{{ request()->getScheme() }}://{{ config('app.base_domain') }}">پرتال چگنی</a>
        <span>/</span>
        <a href="{{ route('public.village.show', $__slug) }}">روستای {{ $village->name }}</a>
        <span>/</span>
        <b>{{ $__label }}</b>
    </div>
</div>
@if($__showHeading)
    <h1 class="page-title">{{ $__label }}</h1>
@endif
<nav class="v3i-subnav" aria-label="بخش‌های سایت روستا">
    @foreach($__tabs as $__t)
        <a href="{{ $__t['url'] }}" class="{{ $__t['on'] ? 'on' : '' }}">{{ $__t['label'] }}</a>
    @endforeach
</nav>

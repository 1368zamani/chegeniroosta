<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'پایگاه اط‌رسانی روستای ' . ($village->name ?? 'روستا'))</title>
    <meta name="theme-color" content="#c94a2b">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    {{-- Tailwind محلی: بدنهٔ صفحات قدیمی روستا بر پایهٔ کلاس‌های کاربردی نوشته شده‌اند --}}
    <script src="{{ asset('vendor/tailwind/tailwind.js') }}"></script>
    @include('partials.tailwind-v3-config')
    <link rel="stylesheet" href="{{ asset('css/ui-v3.css') }}?v=8">
    @yield('head')
    @include('partials.pwa')
    <link rel="canonical" href="{{ url()->current() }}">
</head>
<body class="v3-body">

@include('partials.gov-header', ['headerTitle' => 'روستای ' . ($village->name ?? '')])

<div class="v3-wrap">
    @include('partials.v3-village-nav', [
        'label' => trim($__env->yieldContent('title')) ?: ($village->name ?? ''),
        'heading' => true,
    ])

    @yield('content')
</div>

@include('partials.gov-footer')
@include('partials.back-to-top')

@yield('scripts')
</body>
</html>

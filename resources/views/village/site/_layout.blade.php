<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $village->name ?? 'روستا')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
    @yield('head')
</head>
<body class="bg-gray-50 font-sans">

<nav class="bg-green-700 text-white shadow">
    <div class="container mx-auto px-4 py-3">
        <div class="flex items-center justify-between mb-2">
            <h1 class="text-lg font-bold">{{ $village->name ?? 'روستا' }}</h1>
            <a href="{{ url('/') }}" class="text-sm hover:underline">صفحه اصلی</a>
        </div>
        <div class="flex flex-wrap gap-1 text-sm">
            <a href="{{ route('public.village.show', $village->slug) }}" class="px-3 py-1 hover:bg-green-600 rounded">خانه</a>
            <a href="{{ route('village.site.about', $village->slug) }}" class="px-3 py-1 hover:bg-green-600 rounded">درباره</a>
            <a href="{{ route('village.site.council', $village->slug) }}" class="px-3 py-1 hover:bg-green-600 rounded">شورا</a>
            <a href="{{ route('village.site.dehyar', $village->slug) }}" class="px-3 py-1 hover:bg-green-600 rounded">دهیاری</a>
            <a href="{{ route('village.site.news', $village->slug) }}" class="px-3 py-1 hover:bg-green-600 rounded">اخبار</a>
            <a href="{{ route('village.site.events', $village->slug) }}" class="px-3 py-1 hover:bg-green-600 rounded">رویدادها</a>
            <a href="{{ route('village.site.facilities', $village->slug) }}" class="px-3 py-1 hover:bg-green-600 rounded">امکانات</a>
            <a href="{{ route('village.site.projects', $village->slug) }}" class="px-3 py-1 hover:bg-green-600 rounded">پروژه‌ها</a>
            <a href="{{ route('village.site.resolutions', $village->slug) }}" class="px-3 py-1 hover:bg-green-600 rounded">مصوبات</a>
            <a href="{{ route('village.site.request.create', $village->slug) }}" class="px-3 py-1 hover:bg-green-600 rounded">ثبت درخواست</a>
            <a href="{{ route('tariff.public', $village->slug) }}" class="px-3 py-1 hover:bg-green-600 rounded">عوارض</a>
        </div>
    </div>
</nav>

<main class="min-h-screen">@yield('content')</main>

<footer class="bg-gray-800 text-white py-6 mt-10">
    <div class="container mx-auto px-4 text-center text-sm">
        <p>© {{ date('Y') }} — روستای {{ $village->name ?? '' }} — تمامی حقوق محفوظ است.</p>
        <p class="mt-2 text-gray-400">
            <a href="{{ url('/') }}" class="hover:underline">بازگشت به صفحه اصلی</a>
        </p>
    </div>
</footer>

@yield('scripts')
</body>
</html>

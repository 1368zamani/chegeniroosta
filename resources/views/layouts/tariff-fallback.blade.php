<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'تعرفه عوارض و بهای خدمات')</title>
    {{-- Tailwind محلی (قبلاً از CDN) --}}
    <script src="{{ asset('vendor/tailwind/tailwind.js') }}"></script>
    <style>body{font-family:Tahoma,sans-serif}@media print{.no-print{display:none!important}}</style>
<link rel="stylesheet" href="{{ asset('css/mobile-admin.css') }}?v=1">
</head>
<body class="bg-gray-50 min-h-screen text-gray-800">
    <div class="max-w-6xl mx-auto py-6 px-4">@yield('content')</div>
</body>
</html>

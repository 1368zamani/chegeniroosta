@extends('village.site._layout')
@section('title', 'تعرفه عوارض — ' . $village->name)

@section('content')
<div class="container mx-auto px-4 py-6" dir="rtl">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-emerald-800">📋 تعرفه عوارض و بهای خدمات</h1>
        <p class="text-gray-600 mt-1">
            روستای <strong>{{ $village->name }}</strong>
        </p>
    </div>

    <div class="bg-amber-50 border-r-4 border-amber-500 p-6 rounded-lg text-amber-900">
        <div class="text-lg font-bold mb-3">⏳ تعرفه‌ای برای این روستا منتشر نشده است</div>
        <p class="text-sm leading-7">
            هنوز تعرفه عوارض و بهای خدمات برای روستای <strong>{{ $village->name }}</strong> در سامانه منتشر نشده است.
            پس از تصویب شورای اسلامی روستا و تأیید شورای بخش، تعرفه‌ها در همین صفحه برای مشاهده و صدور فیش قرار می‌گیرد.
        </p>
    </div>

    <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ route('public.village.show', $village->slug) }}"
           class="px-5 py-3 bg-emerald-700 text-white rounded-lg">← بازگشت به صفحه روستا</a>
        <a href="{{ route('village.site.request.create', $village->slug) }}"
           class="px-5 py-3 border border-emerald-700 text-emerald-800 rounded-lg">ثبت درخواست / پیام به دهیاری</a>
    </div>

</div>
@endsection

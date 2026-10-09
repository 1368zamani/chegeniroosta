@extends('village.site._layout')
@section('title', 'پیگیری فیش — ' . $village->name)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-lg" dir="rtl">
    <div class="mb-6">
        <a href="{{ route('tariff.public', $village->slug) }}" class="text-emerald-700 text-sm">← بازگشت</a>
        <h1 class="text-2xl font-bold text-emerald-800 mt-2">🔍 پیگیری فیش عوارض</h1>
        <p class="text-gray-600 text-sm mt-1">روستای {{ $village->name }}</p>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border-r-4 border-red-500 p-3 rounded mb-4 text-red-800 text-sm">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('tariff.invoice.track', $village->slug) }}" class="bg-white rounded-lg shadow p-5 space-y-4">
        @csrf
        <label class="block">
            <span class="text-sm font-medium">کد پیگیری *</span>
            <input name="tracking_code" value="{{ old('tracking_code') }}" required dir="ltr" maxlength="20" class="w-full border rounded p-3 mt-1 font-mono text-lg text-center tracking-widest uppercase">
        </label>
        <label class="block">
            <span class="text-sm font-medium">شماره موبایل ثبت‌شده *</span>
            <input name="mobile" value="{{ old('mobile') }}" required inputmode="numeric" maxlength="11" dir="ltr" class="w-full border rounded p-3 mt-1 font-mono text-center">
        </label>
        <button class="w-full bg-emerald-700 text-white px-5 py-3 rounded-lg font-medium">🔍 مشاهده فیش</button>
    </form>

    <div class="mt-4 text-center">
        <a href="{{ route('tariff.invoice.create', $village->slug) }}" class="text-emerald-700 text-sm underline">صدور فیش جدید</a>
    </div>
</div>
@endsection

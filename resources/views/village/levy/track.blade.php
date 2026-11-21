@extends('village.site._layout')
@section('title', 'پیگیری درخواست عوارض — ' . $village->name)

@section('content')
<div class="container mx-auto px-4 py-8" dir="rtl">
    <div class="max-w-xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-200 p-6 md:p-8">
        <h1 class="text-xl font-bold text-emerald-800 mb-2">پیگیری درخواست عوارض</h1>
        <p class="text-sm text-gray-500 mb-6">
            کد پیگیری و شماره تلفنی را که هنگام ثبت درخواست وارد کردهاید، وارد نمایید.
        </p>

        @if(session('error'))
            <div class="mb-5 p-3 rounded-lg text-sm" style="background:#fdeceb;color:#8e2f18;border-right:4px solid #b3401f;">
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-5 p-3 rounded-lg text-sm" style="background:#fdeceb;color:#8e2f18;border-right:4px solid #b3401f;">
                @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('village.tariff.track', $village->slug) }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">کد پیگیری</label>
                    <input type="text" name="tracking_code" required dir="ltr"
                           value="{{ old('tracking_code') }}"
                           placeholder="LV-261101-XXXXXX"
                           class="w-full border-2 border-gray-300 rounded-lg p-2.5 text-left focus:border-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">شماره تلفن ثبتشده</label>
                    <input type="text" name="payer_mobile" required dir="ltr" inputmode="tel"
                           value="{{ old('payer_mobile') }}"
                           placeholder="09xxxxxxxxx"
                           class="w-full border-2 border-gray-300 rounded-lg p-2.5 text-left focus:border-emerald-500 focus:outline-none">
                </div>
            </div>
            <button type="submit" class="mt-6 w-full py-3 rounded-lg bg-emerald-700 text-white font-bold hover:bg-emerald-800">
                نمایش وضعیت درخواست
            </button>
        </form>
    </div>
</div>
@endsection

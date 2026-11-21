@extends('village.site._layout')
@section('title', 'درخواست عوارض — ' . $requestRow->tracking_code)

@php
    use App\Support\Number;
@endphp

@section('content')
<div class="container mx-auto px-4 py-6" dir="rtl">
    <div class="max-w-3xl mx-auto">

        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl md:text-2xl font-bold text-emerald-800">جزئیات درخواست عوارض</h1>
                <p class="text-sm text-gray-500 mt-1">
                    کد پیگیری: <strong dir="ltr" class="text-gray-700">{{ $requestRow->tracking_code }}</strong>
                </p>
            </div>
            <span class="px-3 py-1.5 rounded-full text-xs font-bold {{ $requestRow->statusColor() }}">
                {{ $requestRow->statusLabel() }}
            </span>
        </div>

        @if(session('success'))
            <div class="mb-5 p-3 rounded-lg text-sm" style="background:#e8f5e9;color:#1b5e20;border-right:4px solid #2e7d32;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('info'))
            <div class="mb-5 p-3 rounded-lg text-sm" style="background:#e3f2fd;color:#0d47a1;border-right:4px solid #1565c0;">
                {{ session('info') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-5 p-3 rounded-lg text-sm" style="background:#fdeceb;color:#8e2f18;border-right:4px solid #b3401f;">
                @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
            </div>
        @endif

        {{-- مبلغ و حساب --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                <div class="text-xs text-gray-500 mb-1">مبلغ قابل پرداخت</div>
                <div class="text-2xl font-bold text-emerald-700">{{ Number::money($requestRow->amount) }} ریال</div>
                <div class="text-xs text-gray-500 mt-2">نوع: {{ $requestRow->typeLabel() }}</div>
            </div>
            <div class="rounded-2xl p-5 border" style="background:#eef6fb;border-color:#b9d9ec;">
                <h3 class="text-sm font-bold mb-2" style="color:#1565c0;">💳 حساب واریز</h3>
                <div class="text-xs space-y-1" style="color:#0d47a1;">
                    <div>دارنده: <strong>{{ $requestRow->account_holder }}</strong></div>
                    @if($requestRow->account_no)<div dir="ltr">شماره حساب: <strong>{{ $requestRow->account_no }}</strong></div>@endif
                    @if($requestRow->iban)<div dir="ltr">شبا: <strong>{{ $requestRow->iban }}</strong></div>@endif
                </div>
            </div>
        </div>

        {{-- آپلود رسید --}}
        @if($requestRow->status !== 'approved')
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 md:p-6 mb-5">
            <h2 class="text-base font-bold text-gray-800 mb-3">📤 آپلود رسید پرداخت</h2>
            <p class="text-xs text-gray-500 mb-4">
                تصویر رسید واریز (JPG، PNG یا PDF — حداکثر ۵ مگابایت) را بارگذاری نمایید تا توسط کارشناس بررسی شود.
            </p>
            <form method="POST" enctype="multipart/form-data"
                  action="{{ route('village.tariff.invoice.receipt', $requestRow->tracking_code) }}">
                @csrf
                <div class="flex flex-wrap items-center gap-3">
                    <input type="file" name="receipt" required accept=".jpg,.jpeg,.png,.webp,.pdf"
                           class="text-sm border border-gray-300 rounded-lg p-2 bg-white">
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-700 text-white text-sm font-bold hover:bg-blue-800">
                        ارسال رسید
                    </button>
                </div>
            </form>
            @if($requestRow->receipt_path)
                <div class="mt-4 text-xs text-gray-600">
                    رسید ارسالشده:
                    <a class="text-blue-700 underline" target="_blank"
                       href="{{ asset('storage/' . $requestRow->receipt_path) }}">مشاهده رسید</a>
                </div>
            @endif
        </div>
        @endif

        {{-- جزئیات محاسبه / اقلام --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 md:p-6 mb-5">
            <h2 class="text-base font-bold text-gray-800 mb-4">جزئیات درخواست</h2>
            @if($requestRow->type === 'construction')
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-100">
                        <tr><td class="py-2 text-gray-600">مساحت ساخت (Q)</td><td class="py-2 font-bold" dir="ltr">{{ Number::fa($requestRow->construction_area) }} متر مربع</td></tr>
                        <tr><td class="py-2 text-gray-600">مساحت ملک (R)</td><td class="py-2 font-bold" dir="ltr">{{ Number::fa($requestRow->property_area ?: '—') }} متر مربع</td></tr>
                        <tr><td class="py-2 text-gray-600">ارزش معاملاتی (M)</td><td class="py-2 font-bold">{{ Number::money($requestRow->transaction_value) }}</td></tr>
                        <tr><td class="py-2 text-gray-600">ضریب نهایی (N = M × 0.14)</td><td class="py-2 font-bold">{{ Number::money($requestRow->final_coefficient) }}</td></tr>
                        <tr><td class="py-2 text-gray-600">عوارض مسکونی (S)</td><td class="py-2 font-bold">{{ Number::money($requestRow->residential_levy) }}</td></tr>
                        <tr><td class="py-2 text-gray-600">عوارض تراکم (T)</td><td class="py-2 font-bold">{{ Number::money($requestRow->density_levy) }}</td></tr>
                        <tr class="bg-emerald-50"><td class="py-2 font-bold text-emerald-800">مبلغ نهایی = MAX(S, T)</td><td class="py-2 font-bold text-emerald-800">{{ Number::money($requestRow->final_levy) }} ریال</td></tr>
                    </tbody>
                </table>
            @else
                <table class="w-full text-sm">
                    <thead><tr class="text-right text-xs text-gray-500 border-b"><th class="py-2">قلم</th><th class="py-2">تعداد</th><th class="py-2">مبلغ</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($requestRow->items as $item)
                        <tr>
                            <td class="py-2">{{ $item->title_snapshot }}</td>
                            <td class="py-2" dir="ltr">{{ Number::fa($item->quantity) }}</td>
                            <td class="py-2 font-bold">{{ Number::money($item->amount) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- وضعیت --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 md:p-6">
            <h2 class="text-base font-bold text-gray-800 mb-4">وضعیت درخواست</h2>
            <ol class="space-y-3">
                @foreach($requestRow->events as $event)
                <li class="flex items-start gap-3 text-sm">
                    <span class="mt-1 w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                    <div>
                        <div class="font-bold text-gray-700">{{ $event->action === 'created' ? 'ثبت درخواست' : ($event->action === 'receipt_uploaded' ? 'ارسال رسید پرداخت' : $event->action) }}</div>
                        @if($event->note)<div class="text-xs text-gray-500">{{ $event->note }}</div>@endif
                        <div class="text-[11px] text-gray-400" dir="ltr">{{ $event->created_at?->format('Y-m-d H:i') }}</div>
                    </div>
                </li>
                @endforeach
            </ol>
            @if($requestRow->review_note)
                <div class="mt-4 p-3 rounded-lg bg-rose-50 text-rose-800 text-xs">دلیل: {{ $requestRow->review_note }}</div>
            @endif
        </div>

        <div class="mt-5 text-center">
            <a href="{{ route('village.tariff.index', $village->slug) }}" class="text-sm text-emerald-700 hover:underline">← بازگشت به صفحهٔ عوارض</a>
        </div>
    </div>
</div>
@endsection

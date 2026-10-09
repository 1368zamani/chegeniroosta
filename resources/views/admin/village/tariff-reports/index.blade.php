@extends('admin.village.layout')
@section('title', 'گزارش فیش‌های عوارض')

@php
    use App\Support\Number;
    use Morilog\Jalali\Jalalian;
    $labels = [
        'pending_review'   => 'در انتظار بررسی',
        'issued'           => 'صادر شده',
        'receipt_uploaded' => 'رسید بارگذاری',
        'paid'             => 'پرداخت‌شده',
        'cancelled'        => 'ابطال‌شده',
    ];
@endphp

@section('content')
<div dir="rtl">

    <div class="flex flex-wrap justify-between items-center mb-4 gap-2">
        <div>
            <h1 class="text-2xl font-bold">📊 گزارش فیش‌های عوارض</h1>
            <p class="text-sm text-gray-600 mt-1">روستای {{ $village->name }}</p>
        </div>
        <div>
            <a href="{{ route('admin.village.tariff-reports.summary', $village->id) }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                📈 گزارش خلاصه
            </a>
        </div>
    </div>

    {{-- ═══ کارت‌های آمار ═══ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-gray-500 text-xs mb-1">کل فیش‌ها</div>
            <div class="text-2xl font-bold text-gray-800">{{ Number::fa($stats['total']) }}</div>
        </div>
        <div class="bg-emerald-50 rounded-lg shadow p-4">
            <div class="text-emerald-700 text-xs mb-1">پرداخت‌شده</div>
            <div class="text-2xl font-bold text-emerald-800">{{ Number::fa($stats['paid']) }}</div>
        </div>
        <div class="bg-amber-50 rounded-lg shadow p-4">
            <div class="text-amber-700 text-xs mb-1">در انتظار</div>
            <div class="text-2xl font-bold text-amber-800">
                {{ Number::fa($stats['issued'] + $stats['receipt_uploaded'] + $stats['pending_review']) }}
            </div>
        </div>
        <div class="bg-gray-100 rounded-lg shadow p-4">
            <div class="text-gray-600 text-xs mb-1">ابطال‌شده</div>
            <div class="text-2xl font-bold text-gray-700">{{ Number::fa($stats['cancelled']) }}</div>
        </div>
    </div>

    {{-- ═══ کارت‌های درآمد ═══ --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">
        <div class="bg-gradient-to-l from-emerald-500 to-emerald-700 text-white rounded-lg shadow p-5">
            <div class="text-xs opacity-90 mb-2">💰 جمع دریافتی (ریال)</div>
            <div class="text-2xl font-bold font-mono">{{ Number::fa(number_format($income['paid_amount'])) }}</div>
        </div>
        <div class="bg-gradient-to-l from-amber-500 to-amber-700 text-white rounded-lg shadow p-5">
            <div class="text-xs opacity-90 mb-2">⏳ در انتظار پرداخت (ریال)</div>
            <div class="text-2xl font-bold font-mono">{{ Number::fa(number_format($income['unpaid_amount'])) }}</div>
        </div>
        <div class="bg-gradient-to-l from-blue-500 to-blue-700 text-white rounded-lg shadow p-5">
            <div class="text-xs opacity-90 mb-2">📊 جمع کل صادرشده (ریال)</div>
            <div class="text-2xl font-bold font-mono">{{ Number::fa(number_format($income['total_amount'])) }}</div>
        </div>
    </div>

    {{-- ═══ فیلترها ═══ --}}
    <div class="bg-white rounded-lg shadow p-4 mb-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-600 mb-1">وضعیت</label>
                <select name="status" class="border rounded p-2 text-sm">
                    <option value="">همه</option>
                    @foreach($labels as $k => $t)
                        <option value="{{ $k }}" @selected(request('status') === $k)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">سال</label>
                <select name="year" class="border rounded p-2 text-sm">
                    <option value="">همه</option>
                    @foreach($years as $y)
                        <option value="{{ $y }}" @selected(request('year') == $y)>{{ Number::fa($y) }}</option>
                    @endforeach
                </select>
            </div>
            <button class="bg-emerald-600 text-white px-4 py-2 rounded text-sm">فیلتر</button>
            @if(request('status') || request('year'))
                <a href="{{ route('admin.village.tariff-reports.index', $village->id) }}"
                   class="text-gray-600 text-sm underline">پاک‌کردن</a>
            @endif
        </form>
    </div>

    {{-- ═══ جدول ═══ --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-right text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3">کد پیگیری</th>
                    <th class="p-3">پرداخت‌کننده</th>
                    <th class="p-3">مبلغ (ریال)</th>
                    <th class="p-3">وضعیت</th>
                    <th class="p-3">تاریخ</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr class="border-t hover:bg-gray-50">
                    <td class="p-3 font-mono text-xs">{{ $inv->tracking_code }}</td>
                    <td class="p-3">
                        <div>{{ $inv->payer_name }}</div>
                        <div class="text-xs text-gray-500 font-mono">{{ $inv->payer_mobile }}</div>
                    </td>
                    <td class="p-3 font-mono">{{ Number::fa(number_format($inv->total)) }}</td>
                    <td class="p-3">
                        <span class="inline-block px-2 py-1 rounded text-xs {{ $inv->statusColor() }}">
                            {{ $inv->statusLabel() }}
                        </span>
                    </td>
                    <td class="p-3 text-xs text-gray-600">
                        {{ $inv->issued_at ? Number::fa(Jalalian::fromCarbon($inv->issued_at)->format('Y/m/d')) : '-' }}
                    </td>
                    <td class="p-3">
                        <a href="{{ route('admin.village.tariff-reports.show', [$village->id, $inv->id]) }}"
                           class="text-emerald-700 underline text-sm">مشاهده</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-500">
                        📭 هیچ فیشی برای این روستا ثبت نشده است.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($invoices->hasPages())
    <div class="mt-4">{{ $invoices->links() }}</div>
    @endif

</div>
@endsection

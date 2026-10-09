@extends('admin.sections.layout')
@section('title', 'فیش‌های عوارض')

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

    {{-- هدر --}}
    <div class="flex flex-wrap justify-between items-center mb-4 gap-2">
        <h1 class="text-2xl font-bold">📋 فیش‌های عوارض و بهای خدمات</h1>
        <div class="flex gap-2 text-sm">
            <a href="{{ route('admin.tariff-invoices.account') }}" class="px-3 py-2 border rounded bg-white hover:bg-gray-50">🏦 حساب بانکی</a>
            <a href="{{ route('admin.tariffs.index') }}" class="px-3 py-2 border rounded bg-white hover:bg-gray-50">📄 تعرفه‌ها</a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border-r-4 border-emerald-500 p-3 rounded mb-4 text-emerald-900 text-sm">✅ {{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border-r-4 border-red-500 p-3 rounded mb-4 text-red-800 text-sm">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    {{-- تب‌های وضعیت --}}
    <div class="flex flex-wrap gap-2 mb-4 text-sm">
        <a href="{{ route('admin.tariff-invoices.index') }}"
           class="px-3 py-1 rounded border {{ !request('status') ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white' }}">
            همه ({{ Number::fa($counts['all']) }})
        </a>
        @foreach($labels as $k => $t)
        <a href="{{ route('admin.tariff-invoices.index', ['status' => $k]) }}"
           class="px-3 py-1 rounded border {{ request('status') === $k ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white' }}">
            {{ $t }} ({{ Number::fa($counts[$k] ?? 0) }})
        </a>
        @endforeach
    </div>

    {{-- جستجو --}}
    <form method="GET" class="mb-4 flex gap-2">
        <input name="q" value="{{ request('q') }}" placeholder="🔍 کد پیگیری / نام / موبایل / کد ملی"
               class="flex-1 border rounded p-2">
        @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
        <button class="bg-emerald-700 text-white px-4 py-2 rounded">جستجو</button>
        @if(request('q') || request('status'))
        <a href="{{ route('admin.tariff-invoices.index') }}" class="border px-4 py-2 rounded bg-white">پاک‌کردن</a>
        @endif
    </form>

    {{-- جدول --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-right text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3">کد پیگیری</th>
                    <th class="p-3">روستا</th>
                    <th class="p-3">پرداخت‌کننده</th>
                    <th class="p-3">مبلغ (ریال)</th>
                    <th class="p-3">وضعیت</th>
                    <th class="p-3">تاریخ</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr class="border-t hover:bg-gray-50
                    {{ in_array($inv->status, ['pending_review', 'receipt_uploaded']) ? 'bg-amber-50' : '' }}
                    {{ $inv->status === 'paid' ? 'bg-emerald-50' : '' }}
                    {{ $inv->status === 'cancelled' ? 'bg-gray-100 opacity-60' : '' }}">
                    <td class="p-3 font-mono" dir="ltr">
                        <a href="{{ route('admin.tariff-invoices.show', $inv->id) }}" class="text-emerald-700 font-bold hover:underline">
                            {{ $inv->tracking_code }}
                        </a>
                    </td>
                    <td class="p-3">{{ $inv->village->name ?? '-' }}</td>
                    <td class="p-3">
                        <div>{{ $inv->payer_name }}</div>
                        <div class="text-xs text-gray-500 font-mono" dir="ltr">{{ $inv->payer_mobile }}</div>
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
                        <a class="text-emerald-700 underline text-sm" href="{{ route('admin.tariff-invoices.show', $inv->id) }}">بررسی</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="p-8 text-center text-gray-500">
                    📭 هیچ فیشی یافت نشد.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- صفحه‌بندی --}}
    @if($invoices->hasPages())
    <div class="mt-4">{{ $invoices->links() }}</div>
    @endif

</div>
@endsection

@extends('admin.village.layout')
@section('title', "فیش {$invoice->tracking_code}")

@php
    use App\Support\Number;
    use Morilog\Jalali\Jalalian;
    $fa = fn ($n) => Number::fa(number_format((int) $n));
@endphp

@section('content')
<div dir="rtl" class="max-w-4xl mx-auto">

    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('admin.village.tariff-reports.index', $village->id) }}"
           class="text-emerald-700 text-sm">← بازگشت به لیست</a>
        <a href="{{ route('tariff.invoice.show', $invoice->public_token) }}" target="_blank"
           class="text-sm border px-3 py-2 rounded bg-white">👁️ نمایش عمومی</a>
    </div>

    <div class="bg-blue-50 border-r-4 border-blue-500 p-3 rounded mb-4 text-sm text-blue-800">
        ℹ️ این صفحه فقط برای گزارش است. برای هرگونه تغییر با کارشناسان مالی تماس بگیرید.
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-wrap justify-between gap-4 border-b pb-4 mb-4">
            <div>
                <h1 class="text-xl font-bold text-emerald-800">📄 فیش {{ $invoice->tracking_code }}</h1>
                <p class="text-sm text-gray-600 mt-1">
                    روستای {{ $village->name }}
                    · سال {{ Number::fa($invoice->set->jalali_year ?? '') }}
                </p>
            </div>
            <div>
                <span class="inline-block px-3 py-1 rounded text-sm {{ $invoice->statusColor() }}">
                    {{ $invoice->statusLabel() }}
                </span>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-x-6 gap-y-3 text-sm mb-4">
            <div>پرداخت‌کننده: <b>{{ $invoice->payer_name }}</b></div>
            @if($invoice->payer_national_id)
            <div>کد ملی: <b class="font-mono">{{ Number::fa($invoice->payer_national_id) }}</b></div>
            @endif
            <div>موبایل: <b class="font-mono">{{ Number::fa($invoice->payer_mobile) }}</b></div>
            @if($invoice->issued_at)
            <div>تاریخ صدور: <b>{{ Number::fa(Jalalian::fromCarbon($invoice->issued_at)->format('Y/m/d')) }}</b></div>
            @endif
            @if($invoice->due_date)
            <div>مهلت: <b>{{ Number::fa(Jalalian::fromCarbon($invoice->due_date)->format('Y/m/d')) }}</b></div>
            @endif
        </div>

        <table class="w-full text-right text-sm border mb-4">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 text-center">#</th>
                    <th class="p-2">عنوان</th>
                    <th class="p-2 text-end">مبلغ قبل</th>
                    <th class="p-2 text-center">تخفیف</th>
                    <th class="p-2 text-end">قابل پرداخت</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->lines as $i => $l)
                <tr class="border-t">
                    <td class="p-2 text-center">{{ Number::fa($i + 1) }}</td>
                    <td class="p-2">{{ $l->title_name }}</td>
                    <td class="p-2 font-mono text-end">{{ $fa($l->amount_before) }}</td>
                    <td class="p-2 text-center">{{ (float) $l->discount_percent > 0 ? Number::fa((int) $l->discount_percent) . '٪' : '—' }}</td>
                    <td class="p-2 font-mono text-end font-bold">{{ $fa($l->amount) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-emerald-50">
                <tr>
                    <th colspan="4" class="p-3 text-end">جمع کل (ریال):</th>
                    <th class="p-3 font-mono text-end text-lg text-emerald-800">{{ $fa($invoice->total) }}</th>
                </tr>
            </tfoot>
        </table>

        @if($invoice->receipts->isNotEmpty())
        <h3 class="font-bold text-sm mb-2 mt-4">📎 رسیدها</h3>
        <table class="w-full text-right text-sm border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2">تاریخ پرداخت</th>
                    <th class="p-2">مبلغ</th>
                    <th class="p-2">وضعیت</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->receipts as $r)
                <tr class="border-t">
                    <td class="p-2 text-xs">{{ Number::fa($r->paid_at_jalali ?? '-') }}</td>
                    <td class="p-2 font-mono">{{ $fa($r->paid_amount) }}</td>
                    <td class="p-2">
                        <span class="px-2 py-1 rounded text-xs {{ $r->statusColor() }}">
                            {{ $r->statusLabel() }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

    </div>

</div>
@endsection

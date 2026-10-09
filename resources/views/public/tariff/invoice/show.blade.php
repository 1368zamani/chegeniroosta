@extends('village.site._layout')
@section('title', "فیش {$invoice->tracking_code}")

@php
    use App\Support\Number;
    use Morilog\Jalali\Jalalian;
    $fa = fn ($n) => Number::fa(number_format((int) $n));
    $plan = $invoice->installment_plan;
@endphp

@section('content')
<style>@media print{.no-print{display:none!important}.sheet{box-shadow:none!important}}</style>

<div class="container mx-auto px-4 py-6" dir="rtl">

    @if(session('success'))
    <div class="no-print bg-emerald-50 border-r-4 border-emerald-500 p-3 rounded mb-4 text-emerald-900 text-sm">✅ {{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="no-print bg-red-50 border-r-4 border-red-500 p-3 rounded mb-4 text-red-800 text-sm">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    <div class="sheet bg-white rounded-lg shadow p-6 mb-4">
        <div class="flex flex-wrap justify-between gap-4 border-b pb-4 mb-4">
            <div>
                <h1 class="text-xl font-bold text-emerald-800">📄 برگه عوارض و بهای خدمات</h1>
                <div class="text-sm text-gray-600 mt-1">دهیاری روستای {{ $invoice->village->name }} · تعرفه سال {{ Number::fa($invoice->set->jalali_year ?? '') }}</div>
            </div>
            <div class="text-left">
                <div class="text-xs text-gray-500">کد پیگیری</div>
                <div class="font-mono text-2xl font-bold tracking-wider text-emerald-700" dir="ltr">{{ $invoice->tracking_code }}</div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-x-6 gap-y-2 text-sm mb-4">
            <div>پرداخت‌کننده: <b>{{ $invoice->payer_name }}</b></div>
            @if($invoice->payer_national_id)
            <div>کد ملی: <b class="font-mono">{{ Number::fa($invoice->payer_national_id) }}</b></div>
            @endif
            <div>موبایل: <b class="font-mono" dir="ltr">{{ Number::fa($invoice->payer_mobile) }}</b></div>
            @if($invoice->issued_at)
            <div>تاریخ صدور: <b>{{ Number::fa(Jalalian::fromCarbon($invoice->issued_at)->format('Y/m/d')) }}</b></div>
            @endif
            @if($invoice->due_date)
            <div>مهلت پرداخت: <b class="{{ $invoice->isOverdue() ? 'text-red-700' : '' }}">{{ Number::fa(Jalalian::fromCarbon($invoice->due_date)->format('Y/m/d')) }}</b></div>
            @endif
            @if($invoice->subject)
            <div>موضوع: <b>{{ $invoice->subject }}</b></div>
            @endif
        </div>

        <div class="mb-4">
            <span class="inline-block px-3 py-1 rounded text-sm {{ $invoice->statusColor() }}">وضعیت: {{ $invoice->statusLabel() }}</span>
            @if($invoice->review_note)
            <span class="text-xs text-gray-600 mr-2">توضیح کارشناس: {{ $invoice->review_note }}</span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 w-12">ردیف</th>
                        <th class="p-2">عنوان</th>
                        <th class="p-2 w-32">مبلغ قبل</th>
                        <th class="p-2 w-20">تخفیف</th>
                        <th class="p-2 w-32">قابل پرداخت</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->lines as $i => $l)
                    <tr class="border-t">
                        <td class="p-2 text-center">{{ Number::fa($i + 1) }}</td>
                        <td class="p-2">{{ $l->title_name }}</td>
                        <td class="p-2 font-mono">{{ $fa($l->amount_before) }}</td>
                        <td class="p-2 text-center">{{ (float) $l->discount_percent > 0 ? Number::fa((int) $l->discount_percent) . '٪' : '—' }}</td>
                        <td class="p-2 font-mono font-bold">{{ $fa($l->amount) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    @if($invoice->discount_total > 0)
                    <tr class="border-t">
                        <td colspan="4" class="p-2 text-left text-gray-600">جمع تخفیف:</td>
                        <td class="p-2 font-mono text-gray-600">{{ $fa($invoice->discount_total) }}</td>
                    </tr>
                    @endif
                    <tr class="border-t font-bold bg-gray-50">
                        <td colspan="4" class="p-3 text-left">جمع قابل پرداخت (ریال):</td>
                        <td class="p-3 font-mono text-lg text-emerald-800">{{ $fa($invoice->total) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="p-2 text-xs text-gray-600 text-left">معادل {{ $fa($invoice->tomanAmount()) }} تومان</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($invoice->status === 'pending_review')
        <div class="bg-amber-50 border-r-4 border-amber-500 p-3 rounded text-sm text-amber-900 mt-4">
            ⏳ درخواست معافیت یا تقسیط شما در حال بررسی است. لطفاً تا تأیید، پرداخت نکنید.
        </div>
        @elseif(!in_array($invoice->status, ['cancelled', 'paid']))
            @if($plan)
            <div class="mt-4 border rounded p-3 bg-blue-50">
                <h3 class="font-bold mb-2 text-blue-800">📅 برنامه‌ی تقسیط</h3>
                <p class="text-sm">پیش‌پرداخت: <b>{{ $fa($plan['down_payment'] ?? 0) }}</b> ریال · هر قسط: <b>{{ $fa($plan['per_installment'] ?? 0) }}</b> ریال × {{ Number::fa($plan['installments_count'] ?? 0) }}</p>
                <p class="text-xs text-gray-600 mt-2">تحویل چک‌های اقساط به دهیاری الزامی است.</p>
            </div>
            @endif

            <div class="border rounded p-4 bg-emerald-50 text-sm mt-4">
                <h3 class="font-bold mb-3 text-emerald-800">💳 محل واریز (حساب دهیاری)</h3>
                @if($account)
                <div class="space-y-1">
                    <div>بانک: <b>{{ $account->bankLabel() }}</b></div>
                    <div>صاحب حساب: <b>{{ $account->account_holder }}</b></div>
                    @if($account->iban)<div>شبا: <b class="font-mono" dir="ltr">{{ $account->iban }}</b></div>@endif
                    @if($account->card_number)<div>شماره کارت: <b class="font-mono" dir="ltr">{{ $account->card_number }}</b></div>@endif
                    @if($account->account_number)<div>شماره حساب: <b class="font-mono" dir="ltr">{{ $account->account_number }}</b></div>@endif
                    @if($account->note)<div class="text-xs text-gray-600 mt-2">{{ $account->note }}</div>@endif
                </div>
                <div class="mt-3 p-2 bg-amber-100 rounded text-xs text-amber-900">
                    ⚠️ در توضیحات واریز، کد پیگیری <b class="font-mono">{{ $invoice->tracking_code }}</b> را بنویسید.
                    <br>⚠️ دریافت وجه نقد توسط دهیار یا کارکنان <b>ممنوع</b> است.
                </div>
                @else
                <div class="text-red-700 text-sm">❌ حساب بانکی برای این روستا ثبت نشده است؛ لطفاً به دهیاری مراجعه کنید.</div>
                @endif
            </div>
        @endif

        <div class="no-print mt-4 flex gap-2">
            <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded">🖨️ چاپ برگه</button>
            <a href="{{ route('tariff.invoice.track.form', $invoice->village->slug) }}" class="border px-4 py-2 rounded">پیگیری فیش دیگر</a>
        </div>
    </div>

    @if($invoice->receipts->isNotEmpty())
    <div class="no-print bg-white rounded-lg shadow p-5 mb-4">
        <h2 class="font-bold mb-3">📎 رسیدهای بارگذاری‌شده</h2>
        <table class="w-full text-right text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2">تاریخ پرداخت</th>
                    <th class="p-2">مبلغ (ریال)</th>
                    <th class="p-2">شماره پیگیری بانک</th>
                    <th class="p-2">وضعیت</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->receipts as $r)
                <tr class="border-t">
                    <td class="p-2">{{ Number::fa($r->paid_at_jalali ?? '-') }}</td>
                    <td class="p-2 font-mono">{{ $fa($r->paid_amount) }}</td>
                    <td class="p-2 font-mono">{{ $r->bank_ref_no ? Number::fa($r->bank_ref_no) : '—' }}</td>
                    <td class="p-2">
                        <span class="px-2 py-1 rounded text-xs {{ $r->statusColor() }}">{{ $r->statusLabel() }}</span>
                        @if($r->status === 'rejected' && $r->reject_reason)
                        <div class="text-xs text-red-700 mt-1">{{ $r->reject_reason }}</div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($invoice->canUploadReceipt())
    <form method="POST" action="{{ route('village.tariff.invoice.receipt', $invoice->public_token) }}" enctype="multipart/form-data" class="no-print bg-white rounded-lg shadow p-5">
        @csrf
        <h2 class="font-bold text-emerald-800 mb-4">📤 بارگذاری رسید پرداخت</h2>
        <div class="grid md:grid-cols-2 gap-4">
            <label class="block">
                <span class="text-sm">تصویر یا PDF رسید (حداکثر ۵ مگابایت) *</span>
                <input type="file" name="receipt_file" required accept=".jpg,.jpeg,.png,.pdf" class="w-full border rounded p-2 mt-1">
            </label>
            <label class="block">
                <span class="text-sm">مبلغ پرداخت‌شده (ریال) *</span>
                <input name="paid_amount" value="{{ old('paid_amount') }}" required inputmode="numeric" class="w-full border rounded p-2 mt-1 font-mono">
            </label>
            <label class="block">
                <span class="text-sm">تاریخ پرداخت (مثلاً ۱۴۰۵/۰۷/۱۶) *</span>
                <input name="paid_at" value="{{ old('paid_at') }}" required placeholder="1405/07/16" class="w-full border rounded p-2 mt-1 font-mono">
            </label>
            <label class="block">
                <span class="text-sm">شماره پیگیری / مرجع بانکی</span>
                <input name="bank_ref_no" value="{{ old('bank_ref_no') }}" class="w-full border rounded p-2 mt-1 font-mono">
            </label>
            @if($plan)
            <label class="block">
                <span class="text-sm">شماره قسط (۰ = پیش‌پرداخت)</span>
                <input name="installment_no" value="{{ old('installment_no') }}" type="number" min="0" max="36" class="w-full border rounded p-2 mt-1">
            </label>
            @endif
        </div>
        <button class="mt-4 bg-emerald-700 text-white px-6 py-3 rounded-lg">📤 ارسال رسید</button>
    </form>
    @endif
</div>
@endsection

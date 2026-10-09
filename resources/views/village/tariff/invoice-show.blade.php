@extends('village.site._layout')
@section('title', 'فیش ' . $invoice->tracking_code)

@php
    use App\Support\Number;
    use Morilog\Jalali\Jalalian;
@endphp

@section('content')
<div class="container mx-auto px-4 py-6" dir="rtl">

    @if(session('success'))
    <div class="bg-emerald-50 border-r-4 border-emerald-500 p-3 rounded mb-4">✅ {{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border-r-4 border-red-500 p-3 rounded mb-4 text-red-800">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    {{-- فیش --}}
    <div class="bg-white rounded-lg shadow p-6 mb-4">
        <div class="flex justify-between border-b pb-4 mb-4">
            <div>
                <h1 class="text-xl font-bold text-emerald-800">📄 فیش پرداخت</h1>
                <p class="text-sm text-gray-600">دهیاری روستای {{ $invoice->village->name }}</p>
            </div>
            <div class="text-left">
                <div class="text-xs text-gray-500">کد پیگیری</div>
                <div class="font-mono text-2xl font-bold text-emerald-700" dir="ltr">{{ $invoice->tracking_code }}</div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-x-6 gap-y-2 text-sm mb-4">
            <div>پرداخت‌کننده: <b>{{ $invoice->payer_name }}</b></div>
            <div>موبایل: <b class="font-mono" dir="ltr">{{ Number::fa($invoice->payer_mobile) }}</b></div>
            <div>وضعیت: <span class="px-2 py-1 rounded text-xs {{ $invoice->statusColor() }}">{{ $invoice->statusLabel() }}</span></div>
            @if($invoice->due_date)
            <div>مهلت: <b>{{ Number::fa(Jalalian::fromCarbon($invoice->due_date)->format('Y/m/d')) }}</b></div>
            @endif
        </div>

        <table class="w-full text-right text-sm border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2">#</th>
                    <th class="p-2">عنوان</th>
                    <th class="p-2">مبلغ (ریال)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->lines as $i => $l)
                <tr class="border-t">
                    <td class="p-2 text-center">{{ Number::fa($i + 1) }}</td>
                    <td class="p-2">{{ $l->title_name }}</td>
                    <td class="p-2 font-mono text-left">{{ Number::fa(number_format($l->amount)) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-emerald-50 font-bold">
                <tr>
                    <td colspan="2" class="p-3 text-left">جمع کل:</td>
                    <td class="p-3 font-mono text-left text-emerald-800">{{ Number::fa(number_format($invoice->total)) }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- اطلاعات حساب --}}
        @if($account && !in_array($invoice->status, ['paid', 'cancelled']))
        <div class="mt-6 border rounded p-4 bg-emerald-50">
            <h3 class="font-bold mb-3">💳 حساب بانکی دهیاری</h3>
            <div class="space-y-1 text-sm">
                <div>بانک: <b>{{ $account->bankLabel() }}</b></div>
                <div>صاحب حساب: <b>{{ $account->account_holder }}</b></div>
                @if($account->iban)<div>شبا: <b class="font-mono" dir="ltr">{{ $account->iban }}</b></div>@endif
                @if($account->account_number)<div>شماره حساب: <b class="font-mono" dir="ltr">{{ $account->account_number }}</b></div>@endif
            </div>
            <div class="mt-3 p-2 bg-amber-100 rounded text-xs">
                ⚠️ در توضیحات واریز کد <b class="font-mono">{{ $invoice->tracking_code }}</b> را بنویسید.
            </div>
        </div>
        @endif

        <div class="mt-4">
            <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded">🖨️ چاپ</button>
        </div>
    </div>

    {{-- رسیدها --}}
    @if($invoice->receipts->isNotEmpty())
    <div class="bg-white rounded-lg shadow p-6 mb-4">
        <h2 class="font-bold mb-3">📎 رسیدهای بارگذاری‌شده</h2>
        <table class="w-full text-right text-sm">
            <thead class="bg-gray-100">
                <tr><th class="p-2">تاریخ</th><th class="p-2">مبلغ</th><th class="p-2">وضعیت</th></tr>
            </thead>
            <tbody>
                @foreach($invoice->receipts as $r)
                <tr class="border-t">
                    <td class="p-2">{{ Number::fa($r->paid_at_jalali ?? '-') }}</td>
                    <td class="p-2 font-mono">{{ Number::fa(number_format($r->paid_amount)) }}</td>
                    <td class="p-2">
                        <span class="px-2 py-1 rounded text-xs {{ $r->statusColor() }}">{{ $r->statusLabel() }}</span>
                        @if($r->status === 'rejected' && $r->reject_reason)
                        <div class="text-xs text-red-700">{{ $r->reject_reason }}</div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- آپلود رسید --}}
    @if($invoice->canUploadReceipt())
    <form method="POST" action="{{ route('village.tariff.invoice.receipt', $invoice->public_token) }}"
          enctype="multipart/form-data"
          class="bg-white rounded-lg shadow p-6">
        @csrf
        <h2 class="font-bold mb-4 text-emerald-800">📤 بارگذاری رسید پرداخت</h2>

        <div class="grid md:grid-cols-2 gap-4">
            <label class="block">
                <span class="text-sm">تصویر یا PDF رسید *</span>
                <input type="file" name="receipt_file" required accept=".jpg,.jpeg,.png,.pdf" class="w-full border rounded p-2 mt-1">
            </label>
            <label class="block">
                <span class="text-sm">مبلغ پرداخت‌شده (ریال) *</span>
                <input name="paid_amount" value="{{ old('paid_amount', $invoice->total) }}" required inputmode="numeric" class="w-full border rounded p-2 mt-1 font-mono">
            </label>
            <label class="block">
                <span class="text-sm">تاریخ پرداخت (۱۴۰۵/۰۷/۱۶) *</span>
                <input name="paid_at" value="{{ old('paid_at') }}" required placeholder="1405/07/16" class="w-full border rounded p-2 mt-1 font-mono">
            </label>
            <label class="block">
                <span class="text-sm">شماره پیگیری بانکی</span>
                <input name="bank_ref_no" value="{{ old('bank_ref_no') }}" class="w-full border rounded p-2 mt-1 font-mono">
            </label>
        </div>

        <button class="mt-4 bg-emerald-700 text-white px-6 py-3 rounded-lg">📤 ارسال رسید</button>
    </form>
    @endif
</div>
@endsection

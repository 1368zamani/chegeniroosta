<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>قبض عوارض {{ $bill->bill_number }}</title>
<style>
@font-face {
    font-family: 'Vazirmatn';
    src: url('{{ public_path("fonts/Vazirmatn-Regular.ttf") }}') format('truetype');
    font-weight: 400;
}
@font-face {
    font-family: 'Vazirmatn';
    src: url('{{ public_path("fonts/Vazirmatn-Bold.ttf") }}') format('truetype');
    font-weight: 700;
}

* { box-sizing: border-box; }
body {
    font-family: 'Vazirmatn', 'DejaVu Sans', sans-serif;
    direction: rtl;
    background: #ffffff;
    color: #1e3a5f;
    margin: 0;
    padding: 30px;
    font-size: 13px;
    line-height: 1.9;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 3px solid #1565c0;
    padding-bottom: 15px;
    margin-bottom: 20px;
}

.header-title h1 {
    font-size: 22px;
    color: #0d47a1;
    margin: 0 0 6px;
    font-weight: 700;
}

.header-title p {
    margin: 0;
    color: #546e7a;
    font-size: 12px;
}

.header-logo {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #1565c0;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
}

.bill-info {
    background: #f5f9ff;
    border: 1px solid #e3f2fd;
    border-radius: 10px;
    padding: 15px 20px;
    margin-bottom: 20px;
}

.bill-info table {
    width: 100%;
    border-collapse: collapse;
}

.bill-info td {
    padding: 6px 4px;
    font-size: 13px;
}

.bill-info td:first-child {
    font-weight: 700;
    color: #0d47a1;
    width: 130px;
}

.info-grid {
    display: table;
    width: 100%;
    margin-bottom: 20px;
}

.info-row {
    display: table-row;
}

.info-cell {
    display: table-cell;
    padding: 6px 0;
    width: 50%;
}

.info-label {
    color: #546e7a;
    font-size: 12px;
}

.info-value {
    font-weight: 700;
    color: #1e3a5f;
    direction: ltr;
    display: inline-block;
}

.section-title {
    font-size: 15px;
    color: #0d47a1;
    font-weight: 700;
    border-right: 4px solid #1565c0;
    padding-right: 10px;
    margin: 25px 0 12px;
}

table.items {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

table.items thead th {
    background: #1565c0;
    color: #ffffff;
    padding: 10px 8px;
    text-align: right;
    font-weight: 700;
    font-size: 12.5px;
    border: 1px solid #0d47a1;
}

table.items tbody td {
    padding: 10px 8px;
    border: 1px solid #e3f2fd;
    font-size: 12.5px;
}

table.items tbody tr:nth-child(even) {
    background: #f5f9ff;
}

table.items tbody tr.total-row {
    background: #e8f5e9;
    font-weight: 700;
}

table.items tbody tr.total-row td {
    border-top: 2px solid #2e7d32;
    color: #1b5e20;
    font-size: 14px;
}

.amount {
    direction: ltr;
    display: inline-block;
    font-family: 'Courier New', monospace;
    font-weight: 700;
}

.bank-info {
    background: #fff8e1;
    border: 1px solid #ffe082;
    border-right: 4px solid #f9a825;
    border-radius: 8px;
    padding: 15px 18px;
    margin: 20px 0;
}

.bank-info h3 {
    margin: 0 0 10px;
    color: #5d4037;
    font-size: 14px;
}

.bank-info table {
    width: 100%;
}

.bank-info td {
    padding: 5px 0;
    font-size: 13px;
}

.bank-info td:first-child {
    color: #5d4037;
    width: 130px;
    font-weight: 700;
}

.bank-info .num {
    direction: ltr;
    display: inline-block;
    font-family: 'Courier New', monospace;
    font-weight: 700;
    color: #0d47a1;
    font-size: 14px;
    letter-spacing: 1px;
}

.notice {
    background: #e3f2fd;
    border-right: 4px solid #1565c0;
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 12px;
    color: #0d47a1;
    margin: 20px 0;
}

.footer {
    margin-top: 40px;
    padding-top: 15px;
    border-top: 2px solid #e3f2fd;
    text-align: center;
    font-size: 11px;
    color: #90a4ae;
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    color: #fff;
    background: #c62828;
}
.status-badge.unpaid { background: #c62828; }
.status-badge.pending_review { background: #ef6c00; }
.status-badge.paid { background: #2e7d32; }
.status-badge.rejected { background: #546e7a; }
</style>
</head>
<body>

<!-- ═══ هدر ═══ -->
<div class="header">
    <div class="header-title">
        <h1>قبض عوارض و بهای خدمات</h1>
        <p>پایگاه اطلاع‌رسانی بخشداری {{ $bill->dehyari?->village?->section?->name ?? '' }} — شهرستان چگنی، استان لرستان</p>
    </div>
    <div class="header-logo">🏛</div>
</div>

<!-- ═══ اطلاعات قبض ═══ -->
<div class="bill-info">
    <table>
        <tr>
            <td>کد پیگیری:</td>
            <td><span class="amount">{{ $bill->bill_number }}</span></td>
            <td>وضعیت:</td>
            <td>
                <span class="status-badge {{ $bill->status }}">
                    @switch($bill->status)
                        @case('unpaid') پرداخت‌نشده @break
                        @case('pending_review') در انتظار تأیید @break
                        @case('paid') پرداخت‌شده @break
                        @case('rejected') رد‌شده @break
                        @default {{ $bill->status }}
                    @endswitch
                </span>
            </td>
        </tr>
        <tr>
            <td>تاریخ صدور:</td>
            <td>{{ \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($bill->issue_date))->format('Y/m/d') }}</td>
            <td>مهلت پرداخت:</td>
            <td>
                @if($bill->due_date)
                    {{ \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($bill->due_date))->format('Y/m/d') }}
                @else
                    —
                @endif
            </td>
        </tr>
    </table>
</div>

<!-- ═══ اطلاعات مؤدی ═══ -->
<div class="section-title">مشخصات مؤدی</div>

<div class="info-grid">
    <div class="info-row">
        <div class="info-cell">
            <span class="info-label">نام و نام خانوادگی:</span>
            <span class="info-value">{{ $bill->taxpayer_name }}</span>
        </div>
        <div class="info-cell">
            <span class="info-label">شماره موبایل:</span>
            <span class="info-value">{{ $bill->mobile ?? '—' }}</span>
        </div>
    </div>
    <div class="info-row">
        <div class="info-cell">
            <span class="info-label">کد ملی:</span>
            <span class="info-value">{{ $bill->national_code ?: '—' }}</span>
        </div>
        <div class="info-cell">
            <span class="info-label">دهیاری:</span>
            <span class="info-value">{{ $bill->dehyari?->name ?? '—' }}</span>
        </div>
    </div>
    <div class="info-row">
        <div class="info-cell">
            <span class="info-label">نوع عوارض:</span>
            <span class="info-value">{{ $bill->fee_type_name }}</span>
        </div>
        <div class="info-cell">
            <span class="info-label">سال:</span>
            <span class="info-value">{{ $bill->year }}</span>
        </div>
    </div>
</div>

<!-- ═══ تفکیک محاسبه ═══ -->
<div class="section-title">تفکیک محاسبه</div>

<table class="items">
    <thead>
        <tr>
            <th style="width: 40px;">ردیف</th>
            <th>شرح</th>
            <th>جزئیات</th>
            <th style="width: 130px;">مبلغ (ریال)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bill->items as $i => $item)
            <tr>
                <td style="text-align: center;">{{ $i + 1 }}</td>
                <td>{{ $item->title }}</td>
                <td style="color: #546e7a; font-size: 11.5px;">{{ $item->description }}</td>
                <td style="text-align: left;"><span class="amount">{{ number_format($item->amount_rial) }}</span></td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="3" style="text-align: left;">مبلغ قابل پرداخت:</td>
            <td style="text-align: left;">
                <span class="amount" style="font-size: 15px;">{{ number_format($bill->amount_rial) }}</span> ریال
            </td>
        </tr>
    </tbody>
</table>

<!-- ═══ اطلاعات پرداخت ═══ -->
@if($bill->status === 'unpaid')
    <div class="section-title">اطلاعات پرداخت</div>

    @php
        $accounts = \App\Models\PaymentAccount::where('dehyari_id', $bill->dehyari_id)
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->get();
    @endphp

    @if($accounts->count() > 0)
        @foreach($accounts as $acc)
            <div class="bank-info">
                <h3>🏦 اطلاعات حساب بانکی</h3>
                <table>
                    @if($acc->bank_name)
                        <tr><td>بانک:</td><td>{{ $acc->bank_name }}</td></tr>
                    @endif
                    @if($acc->account_number)
                        <tr><td>شماره حساب:</td><td><span class="num">{{ $acc->account_number }}</span></td></tr>
                    @endif
                    @if($acc->card_number)
                        <tr><td>شماره کارت:</td><td><span class="num">{{ $acc->card_number }}</span></td></tr>
                    @endif
                    @if($acc->sheba)
                        <tr><td>شبا:</td><td><span class="num">IR{{ ltrim($acc->sheba, 'IRir') }}</span></td></tr>
                    @endif
                    @if($acc->account_holder)
                        <tr><td>صاحب حساب:</td><td>{{ $acc->account_holder }}</td></tr>
                    @endif
                </table>
            </div>
        @endforeach
    @else
        <div class="notice">
            ⚠️ هنوز حساب بانکی برای این دهیاری ثبت نشده است. لطفاً با دهیاری تماس بگیرید.
        </div>
    @endif

    <div class="notice">
        📌 پس از واریز، با مراجعه به سایت و ثبت شماره پیگیری بانکی، تاریخ واریز و عکس فیش، پرداخت خود را ثبت کنید.
        <br>
        🌐 آدرس سایت: <span style="direction: ltr; display: inline-block;">{{ url('/fees/tracking') }}</span>
    </div>
@endif

@if($bill->status === 'paid')
    <div class="notice" style="background: #e8f5e9; border-color: #2e7d32; color: #1b5e20;">
        ✅ این قبض پرداخت شده است. تاریخ پرداخت:
        <span class="amount">
            {{ $bill->paid_at ? \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($bill->paid_at))->format('Y/m/d') : '—' }}
        </span>
    </div>
@endif

<!-- ═══ فوتر ═══ -->
<div class="footer">
    این قبض به صورت خودکار صادر شده و فاقد مهر و امضا دستی است. برای استعلام، کد پیگیری را در سایت وارد کنید.
    <br>
    تاریخ چاپ: {{ \Morilog\Jalali\Jalalian::now()->format('Y/m/d H:i') }}
</div>

</body>
</html>

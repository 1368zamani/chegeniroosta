<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>قبض عوارض {{ $bill->bill_number }} | روستا نما</title>
<style>
    *{box-sizing:border-box}
    body{font-family:Tahoma,sans-serif;background:#f4f7fb;margin:0;padding:16px;color:#1e3a5f;line-height:1.8}
    .card{max-width:720px;margin:0 auto 16px;background:#fff;border-radius:14px;padding:22px;box-shadow:0 6px 24px rgba(30,58,95,.08)}
    h1{font-size:19px;color:#1565c0;margin:0 0 6px;text-align:center}
    .sub{text-align:center;color:#607d8b;font-size:13px;margin-bottom:16px}
    .kv{display:grid;grid-template-columns:auto 1fr;gap:8px 14px;font-size:14px;margin:12px 0}
    .kv b{color:#37474f}
    table{width:100%;border-collapse:collapse;margin-top:10px;font-size:13.5px}
    th,td{padding:9px 8px;border-bottom:1px solid #eceff1;text-align:right}
    th{background:#f5f9ff;color:#1565c0;font-weight:bold}
    .total{background:#e8f5e9;font-weight:bold;font-size:15px}
    .acct{background:#f1f8ff;border:1px solid #bbdefb;border-radius:10px;padding:12px 14px;margin:10px 0;font-size:14px}
    .acct .num{font-family:monospace;font-size:16px;letter-spacing:1px;color:#0d47a1;font-weight:bold;direction:ltr;display:inline-block;user-select:all}
    .card label{display:block;margin:12px 0 6px;font-size:13.5px;font-weight:bold;color:#37474f}
.card input,.card textarea{width:100%;padding:10px 12px;border:1px solid #cfd8dc;border-radius:9px;font-family:inherit;font-size:14px;background:#fbfdff}
.note{background:#fff8e1;border-right:4px solid #f9a825;padding:10px 14px;border-radius:8px;font-size:13px;color:#5d4037;margin-top:12px}
    .status{display:inline-block;padding:4px 11px;border-radius:20px;font-size:12.5px;font-weight:bold}
    .st-unpaid{background:#ffebee;color:#c62828}
    .st-pending_review{background:#fff8e1;color:#ef6c00}
    .st-paid{background:#e8f5e9;color:#2e7d32}
    .st-rejected{background:#eceff1;color:#455a64}
    .print-btn{display:block;text-align:center;margin-top:18px}
    .print-btn a,.print-btn button{display:inline-block;padding:10px 20px;background:#1565c0;color:#fff;border:none;border-radius:9px;text-decoration:none;font-family:inherit;font-size:14px;cursor:pointer;margin:4px}
    @media print{.print-btn{display:none}body{background:#fff}.card{box-shadow:none}}
</style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
</head>
<body>

<div class="card">
    <h1>🧾 قبض عوارض</h1>
    <div class="sub">
        کد پیگیری: <b style="font-family:monospace;direction:ltr;display:inline-block">{{ $bill->bill_number }}</b>
        &nbsp; | &nbsp;
        <span class="status st-{{ $bill->status }}">{{ \App\Models\Bill::statusLabel($bill->status) ?? $bill->status }}</span>
    </div>

    <div class="kv">
        <b>مؤدی:</b><span>{{ $bill->taxpayer_name }}</span>
        <b>موبایل:</b><span style="direction:ltr;display:inline-block">{{ $bill->mobile ?? '—' }}</span>
        <b>کد ملی:</b><span style="direction:ltr;display:inline-block">{{ $bill->national_code ?: '—' }}</span>
        <b>نوع عوارض:</b><span>{{ $bill->fee_category_name }} — {{ $bill->fee_type_name }}</span>
        <b>سال:</b><span>{{ $bill->year }}</span>
        <b>تاریخ صدور:</b><span>{{ \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($bill->issue_date))->format('Y/m/d') }}</span>
        @if($bill->due_date)
        <b>مهلت پرداخت:</b><span>{{ \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($bill->due_date))->format('Y/m/d') }}</span>
        @endif
    </div>

    <h3 style="font-size:15px;color:#1565c0;margin-top:18px">تفکیک محاسبه</h3>
    <table>
        <thead><tr><th>شرح</th><th>جزئیات</th><th style="width:120px">مبلغ (ریال)</th></tr></thead>
        <tbody>
        @foreach($items as $it)
            <tr>
                <td>{{ $it->title }}</td>
                <td style="color:#607d8b;font-size:12.5px">{{ $it->description }}</td>
                <td style="font-family:monospace;direction:ltr;text-align:left">{{ number_format($it->amount_rial) }}</td>
            </tr>
        @endforeach
        <tr class="total">
            <td colspan="2">مبلغ قابل پرداخت</td>
            <td style="font-family:monospace;direction:ltr;text-align:left">{{ number_format($bill->amount_rial) }} ریال</td>
        </tr>
        </tbody>
    </table>
</div>

@if($bill->status === 'unpaid')
<div class="card">
    <h3 style="font-size:15px;color:#1565c0;margin:0 0 10px">💳 اطلاعات واریز</h3>
    <p style="font-size:13.5px;color:#37474f;margin:0 0 12px">
        مبلغ <b>{{ number_format($bill->amount_rial) }} ریال</b> را به یکی از حساب‌های زیر واریز کنید و سپس
        <b>عکس فیش واریزی</b> را در همین صفحه بارگذاری نمایید.
    </p>

    @forelse($accounts as $acc)
        <div class="acct">
            <div><b>بانک:</b> {{ $acc->bank_name ?: '—' }}</div>
            @if($acc->account_number)
                <div><b>شماره حساب:</b> <span class="num">{{ $acc->account_number }}</span></div>
            @endif
            @if($acc->card_number)
                <div><b>شماره کارت:</b> <span class="num">{{ $acc->card_number }}</span></div>
            @endif
            @if($acc->sheba)
                <div><b>شبا:</b> <span class="num">IR{{ ltrim($acc->sheba, 'IRir') }}</span></div>
            @endif
            <div><b>صاحب حساب:</b> {{ $acc->account_holder ?: '—' }}</div>
        </div>
    @empty
        <div class="note">⚠️ هنوز حساب بانکی برای این دهیاری ثبت نشده است. لطفاً با دهیاری تماس بگیرید.</div>
    @endforelse

    <div class="note">
        پس از واریز، در فرم زیر: شماره پیگیری بانکی، تاریخ واریز، شماره ارجاع (در صورت وجود) و عکس فیش را وارد کنید.
        پس از تأیید توسط دهیاری، وضعیت قبض به «پرداخت‌شده» تغییر می‌کند.
    </div>

    <form method="POST" action="{{ route('fees.bill.upload', $bill->bill_number) }}" enctype="multipart/form-data" style="margin-top:18px;border-top:1px dashed #cfd8dc;padding-top:16px">
        @csrf
        <h4 style="font-size:14px;color:#1565c0;margin:0 0 10px">📤 ثبت فیش واریزی</h4>

        @if(session('success'))
            <div style="background:#e8f5e9;color:#2e7d32;padding:10px 14px;border-radius:9px;margin-bottom:12px">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div style="background:#ffebee;color:#c62828;padding:10px 14px;border-radius:9px;margin-bottom:12px">
                @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
            </div>
        @endif

        <label>شماره پیگیری بانکی *</label>
        <input type="text" name="bank_reference" value="{{ old('bank_reference') }}" required dir="ltr">

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div>
                <label>تاریخ واریز * (مثال: 1405/07/08)</label>
                <input type="text" name="paid_at" value="{{ old('paid_at') }}" required dir="ltr" placeholder="1405/07/08">
            </div>
            <div>
                <label>شماره ارجاع</label>
                <input type="text" name="reference_number" value="{{ old('reference_number') }}" dir="ltr">
            </div>
        </div>

        <label>مبلغ واریزی (ریال) *</label>
        <input type="number" name="amount_paid" value="{{ old('amount_paid', $bill->amount_rial) }}" required min="1000" dir="ltr">

        <label>عکس فیش * (JPG/PNG/WEBP/PDF، حداکثر ۲ مگابایت)</label>
        <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.webp,.pdf" required>

        <label>توضیحات اختیاری</label>
        <textarea name="note" rows="2" maxlength="500">{{ old('note') }}</textarea>

        <button class="print-btn" type="submit" style="margin-top:14px;display:block;width:100%;padding:12px;background:#2e7d32;color:#fff;border:none;border-radius:9px;font-family:inherit;font-size:15px;font-weight:bold;cursor:pointer">📤 ارسال فیش برای بررسی</button>
    </form>
</div>
@endif

<div class="print-btn">
    <button onclick="window.print()">🖨 چاپ / ذخیره PDF</button>
    <a href="{{ route('fees.calculator') }}">محاسبه جدید</a>
</div>

</body>
</html>

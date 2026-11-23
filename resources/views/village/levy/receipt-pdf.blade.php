@php use App\Support\Number; @endphp
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>رسید پرداخت عوارض — {{ $requestRow->tracking_code }}</title>
    <style>
        @font-face {
            font-family: 'Lalezar';
            src: url('file://{{ public_path('fonts/Lalezar.ttf') }}') format('truetype');
        }
        @font-face {
            font-family: 'BTitr';
            src: url('file://{{ public_path('fonts/BTitrBold.ttf') }}') format('truetype');
        }
        @page { size: A4 portrait; margin: 14mm; }
        * { box-sizing: border-box; }
        body {
            direction: rtl;
            font-family: 'Lalezar', sans-serif;
            font-size: 11pt;
            color: #161616;
            margin: 0;
            padding: 0;
        }
        .top {
            border-bottom: 2.5px solid #1565c0;
            padding-bottom: 7px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .top .right { font-size: 10pt; line-height: 1.7; }
        .top .left { text-align: left; font-size: 9pt; color: #555; }
        h1.title {
            font-family: 'BTitr';
            font-size: 21pt;
            text-align: center;
            color: #0d47a1;
            margin: 4px 0 2px;
        }
        .subtitle { text-align: center; font-size: 10.5pt; color: #444; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; }
        .meta td { border: 1px solid #b9d9ec; background: #eef6fb; padding: 6px 8px; font-size: 10pt; }
        .meta .k { color: #0d47a1; width: 18%; }
        .meta .v { font-weight: bold; width: 32%; }
        .box { border: 1px solid #c9c9c9; padding: 9px 11px; margin-top: 12px; border-radius: 3px; }
        .box-title { font-weight: bold; color: #2e7d32; margin-bottom: 5px; font-size: 11pt; }
        .kv td { padding: 3px 4px; font-size: 10.5pt; }
        .kv .k { color: #555; width: 22%; }
        .kv .v { font-weight: bold; }
        .detail { margin-top: 12px; }
        .detail th, .detail td { border: 1px solid #bbb; padding: 5px 7px; text-align: right; font-size: 10pt; }
        .detail th { background: #f1f5f9; font-weight: bold; }
        .detail .num { text-align: center; width: 14%; }
        .detail .amount { text-align: left; direction: ltr; width: 26%; }
        .detail tr.total td { background: #e8f5e9; font-weight: bold; color: #1b5e20; }
        .payable {
            margin-top: 14px;
            border: 2px solid #2e7d32;
            background: #f2fbf3;
            padding: 10px 12px;
            text-align: center;
        }
        .payable .label { font-size: 10.5pt; color: #1b5e20; }
        .payable .amount { font-family: 'BTitr'; font-size: 19pt; color: #145a1e; margin: 3px 0; }
        .payable .words { font-size: 10pt; color: #333; }
        .account { border: 1px solid #b9d9ec; background: #f4faff; }
        .account .box-title { color: #1565c0; }
        .ltr { direction: ltr; text-align: right; }
        .note {
            margin-top: 13px;
            background: #fff8e1;
            border: 1px solid #e0b354;
            padding: 8px 10px;
            font-size: 9.5pt;
            line-height: 1.7;
        }
        .sign { margin-top: 26px; display: flex; justify-content: space-between; font-size: 10pt; }
        .sign div { width: 30%; text-align: center; border-top: 1px solid #555; padding-top: 5px; }
        .footer { margin-top: 18px; border-top: 1px solid #ccc; padding-top: 6px; font-size: 8.5pt; color: #666; text-align: center; }
    </style>
</head>
<body>

<div class="top">
    <div class="right">
        جمهوری اسلامی ایران<br>
        استانداری لرستان — دهیاری روستای {{ $village->name }}
    </div>
    <div class="left">
        کد پیگیری:<br><strong dir="ltr">{{ $requestRow->tracking_code }}</strong>
    </div>
</div>

<h1 class="title">رسید پرداخت عوارض</h1>
<div class="subtitle">فیش واریز به حساب دهیاری — قابل ارائه به شعبه بانک</div>

<table class="meta">
    <tr>
        <td class="k">کد پیگیری</td>
        <td class="v" dir="ltr">{{ $requestRow->tracking_code }}</td>
        <td class="k">تاریخ صدور</td>
        <td class="v">{{ Number::fa($issuedAt) }}</td>
    </tr>
    <tr>
        <td class="k">نوع عوارض</td>
        <td class="v">{{ $requestRow->typeLabel() }}</td>
        <td class="k">وضعیت درخواست</td>
        <td class="v">{{ $requestRow->statusLabel() }}</td>
    </tr>
</table>

<div class="box">
    <div class="box-title">مشخصات متقاضی</div>
    <table class="kv">
        <tr>
            <td class="k">نام و نام خانوادگی</td><td class="v">{{ $requestRow->payer_name }}</td>
            <td class="k">شماره تلفن</td><td class="v ltr">{{ $requestRow->payer_mobile }}</td>
        </tr>
        @if($requestRow->national_id)
        <tr>
            <td class="k">کد ملی</td><td class="v ltr">{{ $requestRow->national_id }}</td>
            <td class="k">روستا</td><td class="v">{{ $village->name }}</td>
        </tr>
        @endif
    </table>
</div>

<div class="box detail">
    <div class="box-title">جزئیات مبلغ</div>
    @if($requestRow->type === 'construction')
        <table class="detail">
            <tr><th>شرح</th><th class="num">مقدار</th><th class="amount">مبلغ (ریال)</th></tr>
            <tr><td>مساحت ساخت (Q)</td><td class="num">{{ Number::fa($requestRow->construction_area) }} مترمربع</td><td class="amount">—</td></tr>
            <tr><td>مساحت ملک (R)</td><td class="num">{{ Number::fa($requestRow->property_area ?: '—') }} مترمربع</td><td class="amount">—</td></tr>
            <tr><td>ارزش معاملاتی (M)</td><td class="num">—</td><td class="amount">{{ Number::money($requestRow->transaction_value) }}</td></tr>
            <tr><td>ضریب نهایی (N = M × ۰.۱۴)</td><td class="num">—</td><td class="amount">{{ Number::money($requestRow->final_coefficient) }}</td></tr>
            <tr><td>عوارض مسکونی (S = Q × N × O)</td><td class="num">—</td><td class="amount">{{ Number::money($requestRow->residential_levy) }}</td></tr>
            <tr><td>عوارض تراکم (T = N × مساحت × P)</td><td class="num">—</td><td class="amount">{{ Number::money($requestRow->density_levy) }}</td></tr>
            <tr class="total"><td>جمع نهایی = MAX(S, T)</td><td class="num">—</td><td class="amount">{{ Number::money($requestRow->final_levy) }}</td></tr>
        </table>
    @else
        <table class="detail">
            <tr><th class="num">ردیف</th><th>قلم پسماند</th><th class="num">تعداد</th><th class="amount">مبلغ (ریال)</th></tr>
            @foreach($requestRow->items as $i => $item)
            <tr>
                <td class="num">{{ Number::fa($i + 1) }}</td>
                <td>{{ $item->title_snapshot }} @if($item->unit)<span style="color:#666;font-size:9pt">({{ $item->unit }})</span>@endif</td>
                <td class="num">{{ Number::fa($item->quantity) }}</td>
                <td class="amount">{{ Number::money($item->amount) }}</td>
            </tr>
            @endforeach
            <tr class="total"><td colspan="3">جمع کل قابل پرداخت</td><td class="amount">{{ Number::money($requestRow->amount) }}</td></tr>
        </table>
    @endif
</div>

<div class="payable">
    <div class="label">مبلغ قابل پرداخت به حساب دهیاری</div>
    <div class="amount">{{ Number::money($requestRow->amount) }} ریال</div>
    <div class="words">به حروف: {{ $amountWords }}</div>
</div>

<div class="box account">
    <div class="box-title">💳 اطلاعات حساب واریز</div>
    <table class="kv">
        <tr>
            <td class="k">دارنده حساب</td><td class="v">{{ $account['holder'] }}</td>
            <td class="k">بانک</td><td class="v">{{ $account['bank'] }}</td>
        </tr>
        @if($account['number'])
        <tr>
            <td class="k">شماره حساب</td><td class="v ltr" dir="ltr">{{ $account['number'] }}</td>
            <td class="k">شبا</td><td class="v ltr" dir="ltr">{{ $account['iban'] ?: '—' }}</td>
        </tr>
        @elseif($account['iban'])
        <tr>
            <td class="k">شبا</td><td class="v ltr" dir="ltr" colspan="3">{{ $account['iban'] }}</td>
        </tr>
        @endif
    </table>
</div>

<div class="note">
    <strong>توجه:</strong> وجوه عوارض صرفاً باید به حساب اعلامشدهٔ دهیاری واریز گردد و دریافت وجه نقد توسط دهیار یا کارکنان دهیاری ممنوع است.
    پس از پرداخت در بانک، لطفاً تصویر فیش/رسید بانکی را در صفحهٔ پیگیری درخواست (کد پیگیری فوق) بارگذاری نمایید تا توسط کارشناس مربوطه بررسی و تأیید شود.
</div>

<div class="sign">
    <div>امضای دهیار</div>
    <div>مهر دهیاری {{ $village->name }}</div>
    <div>تأیید کارشناس مالی</div>
</div>

<div class="footer">
    این رسید صرفاً جهت معرفی مبلغ و حساب واریز صادر شده و سند پرداخت محسوب نمیشود — کد پیگیری: {{ $requestRow->tracking_code }}
</div>

</body>
</html>

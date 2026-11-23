@php use App\Support\Number; @endphp
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>رسید پرداخت عوارض — {{ $requestRow->tracking_code }}</title>
    <style>
        body {
            direction: rtl;
            font-family: dejavusans;
            font-size: 10.5pt;
            color: #161616;
            margin: 0;
            padding: 0;
        }
        .top { width: 100%; border-bottom: 2px solid #1565c0; padding-bottom: 6px; margin-bottom: 10px; }
        .top td { vertical-align: bottom; font-size: 9.5pt; }
        .top .left { text-align: left; direction: ltr; font-family: dejavusans; }
        h1.title { font-family: btitr; font-size: 19pt; text-align: center; color: #0d47a1; margin: 2px 0; font-weight: bold; }
        .subtitle { text-align: center; font-size: 9.5pt; color: #444; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        .meta td { border: 1px solid #b9d9ec; background: #eef6fb; padding: 5px 7px; font-size: 9.5pt; }
        .meta .k { color: #0d47a1; width: 18%; }
        .meta .v { font-weight: bold; width: 32%; }
        .box { border: 1px solid #c9c9c9; padding: 7px 10px; margin-top: 10px; }
        .box-title { font-weight: bold; color: #2e7d32; margin-bottom: 4px; font-size: 10.5pt; }
        .kv td { padding: 3px 5px; font-size: 10pt; }
        .kv .k { color: #555; width: 22%; }
        .kv .v { font-weight: bold; }
        .ltr { direction: ltr; text-align: right; font-family: dejavusans; }
        .detail { margin-top: 10px; }
        .detail th, .detail td { border: 1px solid #bbb; padding: 4px 6px; text-align: right; font-size: 9.5pt; }
        .detail th { background: #f1f5f9; font-weight: bold; }
        .detail .num { text-align: center; width: 14%; }
        .detail .amount { text-align: left; direction: ltr; width: 26%; font-family: dejavusans; }
        .detail tr.total td { background: #e8f5e9; font-weight: bold; color: #1b5e20; }
        .payable {
            margin-top: 12px;
            border: 2px solid #2e7d32;
            background: #f2fbf3;
            padding: 9px 12px;
            text-align: center;
        }
        .payable .label { font-size: 10pt; color: #1b5e20; }
        .payable .amount { font-family: btitr; font-size: 17pt; color: #145a1e; margin: 2px 0; font-weight: bold; }
        .payable .words { font-size: 10pt; color: #333; }
        .account { border: 1px solid #b9d9ec; background: #f4faff; }
        .account .box-title { color: #1565c0; }
        .note {
            margin-top: 12px;
            background: #fff8e1;
            border: 1px solid #e0b354;
            padding: 7px 10px;
            font-size: 9.5pt;
            line-height: 1.6;
        }
        .sign { margin-top: 22px; width: 100%; font-size: 9.5pt; }
        .sign td { width: 33.33%; text-align: center; border-top: 1px solid #555; padding-top: 4px; }
        .footer { margin-top: 14px; border-top: 1px solid #ccc; padding-top: 5px; font-size: 8.5pt; color: #666; text-align: center; }
    </style>
</head>
<body>

<table class="top">
    <tr>
        <td>
            جمهوری اسلامی ایران<br>
            استانداری لرستان — دهیاری روستای {{ $village->name }}
        </td>
        <td class="left">
            کد پیگیری: <strong>{{ $requestRow->tracking_code }}</strong>
        </td>
    </tr>
</table>

<h1 class="title">رسید پرداخت عوارض</h1>
<div class="subtitle">فیش واریز به حساب دهیاری — قابل ارائه به شعبه بانک</div>

<table class="meta">
    <tr>
        <td class="k">کد پیگیری</td>
        <td class="v ltr" dir="ltr">{{ $requestRow->tracking_code }}</td>
        <td class="k">تاریخ صدور</td>
        <td class="v ltr" dir="ltr">{{ $issuedAt }}</td>
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
            <td class="k">شماره تلفن</td><td class="v ltr" dir="ltr">{{ $requestRow->payer_mobile }}</td>
        </tr>
        <tr>
            <td class="k">کد ملی</td><td class="v ltr" dir="ltr">{{ $requestRow->national_id ?: '—' }}</td>
            <td class="k">روستا</td><td class="v">{{ $village->name }}</td>
        </tr>
    </table>
</div>

<div class="box detail">
    <div class="box-title">جزئیات مبلغ</div>
    @if($requestRow->type === 'construction')
        <table class="detail">
            <tr><th>شرح</th><th class="num">مقدار</th><th class="amount" dir="ltr">مبلغ (ریال)</th></tr>
            <tr><td>مساحت ساخت (Q)</td><td class="num">{{ $requestRow->construction_area }} مترمربع</td><td class="amount" dir="ltr">—</td></tr>
            <tr><td>مساحت ملک (R)</td><td class="num">{{ $requestRow->property_area ?: '—' }} مترمربع</td><td class="amount" dir="ltr">—</td></tr>
            <tr><td>ارزش معاملاتی (M)</td><td class="num">—</td><td class="amount" dir="ltr">{{ Number::money($requestRow->transaction_value, false) }}</td></tr>
            <tr><td>ضریب نهایی (N = M × 0.14)</td><td class="num">—</td><td class="amount" dir="ltr">{{ Number::money($requestRow->final_coefficient, false) }}</td></tr>
            <tr><td>عوارض مسکونی (S = Q × N × O)</td><td class="num">—</td><td class="amount" dir="ltr">{{ Number::money($requestRow->residential_levy, false) }}</td></tr>
            <tr><td>عوارض تراکم (T = N × مساحت × P)</td><td class="num">—</td><td class="amount" dir="ltr">{{ Number::money($requestRow->density_levy, false) }}</td></tr>
            <tr class="total"><td>جمع نهایی = MAX(S, T)</td><td class="num">—</td><td class="amount" dir="ltr">{{ Number::money($requestRow->final_levy, false) }}</td></tr>
        </table>
    @else
        <table class="detail">
            <tr><th class="num">ردیف</th><th>قلم پسماند</th><th class="num">تعداد</th><th class="amount" dir="ltr">مبلغ (ریال)</th></tr>
            @foreach($requestRow->items as $i => $item)
            <tr>
                <td class="num">{{ $i + 1 }}</td>
                <td>{{ $item->title_snapshot }} @if($item->unit)<span style="color:#666;font-size:8.5pt">({{ $item->unit }})</span>@endif</td>
                <td class="num">{{ $item->quantity }}</td>
                <td class="amount" dir="ltr">{{ Number::money($item->amount, false) }}</td>
            </tr>
            @endforeach
            <tr class="total"><td colspan="3">جمع کل قابل پرداخت</td><td class="amount" dir="ltr">{{ Number::money($requestRow->amount, false) }}</td></tr>
        </table>
    @endif
</div>

<div class="payable">
    <div class="label">مبلغ قابل پرداخت به حساب دهیاری</div>
    <div class="amount" dir="ltr">{{ Number::money($requestRow->amount, false) }} ریال</div>
    <div class="words">به حروف: {{ $amountWords }}</div>
</div>

<div class="box account">
    <div class="box-title">اطلاعات حساب واریز</div>
    <table class="kv">
        <tr>
            <td class="k">دارنده حساب</td><td class="v">{{ $account['holder'] ?: '—' }}</td>
            <td class="k">بانک</td><td class="v">{{ $account['bank'] ?: '—' }}</td>
        </tr>
        <tr>
            <td class="k">شماره حساب</td><td class="v ltr" dir="ltr">{{ $account['number'] ?: '—' }}</td>
            <td class="k">شبا</td><td class="v ltr" dir="ltr">{{ $account['iban'] ?: '—' }}</td>
        </tr>
    </table>
</div>

<div class="note">
    <strong>توجه:</strong> وجوه عوارض صرفاً باید به حساب اعلامشدهٔ دهیاری واریز گردد و دریافت وجه نقد توسط دهیار یا کارکنان دهیاری ممنوع است.
    پس از پرداخت در بانک، لطفاً تصویر فیش/رسید بانکی را در صفحهٔ پیگیری درخواست (کد پیگیری فوق) بارگذاری نمایید تا توسط کارشناس مربوطه بررسی و تأیید شود.
</div>

<table class="sign">
    <tr>
        <td>امضای دهیار</td>
        <td>مهر دهیاری {{ $village->name }}</td>
        <td>تأیید کارشناس مالی</td>
    </tr>
</table>

<div class="footer">
    این رسید صرفاً جهت معرفی مبلغ و حساب واریز صادر شده و سند پرداخت محسوب نمیشود — کد پیگیری: {{ $requestRow->tracking_code }}
</div>

</body>
</html>

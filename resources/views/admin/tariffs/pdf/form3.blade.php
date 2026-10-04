<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>فرم شماره ۳ — بهای خدمات</title>
<style>
* { box-sizing: border-box; font-family: 'Vazirmatn', 'DejaVu Sans', sans-serif; }
body { padding: 20px; direction: rtl; color: #1e3a5f; font-size: 11px; }
h1 { text-align: center; color: #0d47a1; font-size: 16px; margin: 0 0 4px; }
h2 { text-align: center; font-size: 13px; margin: 0 0 16px; color: #546e7a; }
table { width: 100%; border-collapse: collapse; }
th, td { border: 1px solid #a5d6a7; padding: 6px 4px; text-align: center; font-size: 10px; }
th { background: #10b981; color: #fff; }
tr:nth-child(even) td { background: #ecfdf5; }
.amount { font-family: monospace; direction: ltr; }
.footer { margin-top: 20px; font-size: 10px; color: #546e7a; }
.sign { display: flex; justify-content: space-between; margin-top: 40px; }
</style>
</head>
<body>

<h1>فرم شماره ۳ — بهای خدمات دهیاری</h1>
<h2>روستای {{ $set->village?->name }} — سال {{ $set->jalali_year }} — نرخ تورم: {{ number_format((float)$set->inflation_rate, 2) }}%</h2>

<table>
    <thead>
        <tr>
            <th style="width:30px">ردیف</th>
            <th>طبقه موضوعی</th>
            <th>عنوان سال جاری</th>
            <th style="width:60px">نرخ تورم</th>
            <th>عنوان پیشنهادی</th>
            <th>فرمول</th>
            <th style="width:80px">درصد افزایش</th>
            <th style="width:90px">مبلغ نهایی (ریال)</th>
        </tr>
    </thead>
    <tbody>
    @foreach($items as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item->title?->formula_key ?? '-' }}</td>
            <td style="text-align:right">{{ $item->title?->name_fa }}</td>
            <td>{{ number_format((float)$set->inflation_rate, 2) }}%</td>
            <td style="text-align:right">{{ $item->title?->name_fa }}</td>
            <td style="font-size:9px">{{ $item->title?->formula_key }}</td>
            <td class="amount">{{ number_format((float)$item->increase_percent, 2) }}%</td>
            <td class="amount">{{ number_format((int)$item->final_amount) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="footer">
    ⚠️ تمامی مبالغ به ریال است. نرخ رسمی چگنی نیست تا تأیید نهایی مراجع.
</div>

<div class="sign">
    <div>دهیار: _______________</div>
    <div>شورای اسلامی روستا: _______________</div>
    <div>شورای بخش: _______________</div>
</div>

</body>
</html>

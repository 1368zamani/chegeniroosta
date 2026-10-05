@extends('admin.tariffs.print._layout')

@section('title', 'فرم شماره ۲ — عوارض محلی')

@section('content')
<div class="header">
    <h2>بسمه تعالی</h2>
    <h1>فرم شماره ۲ — تعرفه‌ی عوارض محلی دهیاری</h1>
    <h2>روستای {{ $tariffSet->village?->name }} — سال {{ \App\Support\Number::fa($tariffSet->jalali_year) }}</h2>
</div>

<div class="meta">
    <div>
        <div>وضعیت: <strong>{{ $tariffSet->statusLabel() }}</strong></div>
        <div>نرخ تورم اعلامی: <strong>{{ \App\Support\Number::fa($tariffSet->inflation_rate) }}٪</strong></div>
    </div>
    <div>
        <div>تاریخ تصویب: {{ $tariffSet->approved_at ? \Morilog\Jalali\Jalalian::fromCarbon($tariffSet->approved_at)->format('Y/m/d') : '—' }}</div>
        <div>تاریخ اعلام: {{ $tariffSet->announced_at ? \Morilog\Jalali\Jalalian::fromCarbon($tariffSet->announced_at)->format('Y/m/d') : '—' }}</div>
    </div>
    <div>
        <div>کد سند: T-{{ $tariffSet->id }}-{{ $tariffSet->jalali_year }}</div>
        <div>مبنا: تبصره ۱ ماده ۲ قانون درآمد پایدار</div>
    </div>
</div>

<div class="warning">
    ⚠️ دریافت وجه نقد توسط دهیار یا کارکنان ممنوع است. وجوه باید به حساب بانکی دهیاری واریز شود.
</div>

@php
    $items = $tariffSet->items
        ->filter(fn($i) => $i->title && $i->title->type === 'levy')
        ->sortBy(fn($i) => $i->title->sort_order);
@endphp

<table>
    <thead>
        <tr>
            <th style="width:40px" class="center">ردیف</th>
            <th style="width:60px" class="center">کد</th>
            <th>عنوان موضوع</th>
            <th style="width:90px">عنوان سال قبل</th>
            <th style="width:70px" class="center">نرخ تورم</th>
            <th style="width:110px">عنوان پیشنهادی</th>
            <th>فرمول / مبنا</th>
            <th style="width:70px" class="center">٪ افزایش</th>
            <th style="width:100px" class="center">مبلغ مصوب (ریال)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $i => $item)
        <tr>
            <td class="center">{{ \App\Support\Number::fa($i + 1) }}</td>
            <td class="center ltr">{{ $item->title->code }}</td>
            <td>{{ $item->title->name_fa }}
                @if($item->title->statutory_note)
                    <div style="font-size:8.5pt;color:#555">{{ $item->title->statutory_note }}</div>
                @endif
            </td>
            <td class="ltr">{{ \App\Support\Number::fa(number_format($item->previous_amount)) }}</td>
            <td class="center">{{ \App\Support\Number::fa($tariffSet->inflation_rate) }}٪</td>
            <td>{{ $item->title->name_fa }}</td>
            <td style="font-size:9pt">{{ $item->formula_display ?: '—' }}</td>
            <td class="center">{{ \App\Support\Number::fa($item->increase_percent) }}٪</td>
            <td class="ltr">{{ $item->final_amount ? \App\Support\Number::fa(number_format($item->final_amount)) : '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="9" class="center">موردی ثبت نشده است.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="signatures">
    <div class="sig-box">دهیار<br>{{ $tariffSet->village?->name }}</div>
    <div class="sig-box">شورای اسلامی روستا</div>
    <div class="sig-box">شورای بخش</div>
    <div class="sig-box">استانداری / هیئت تطبیق</div>
</div>

<div class="footer">
    <p>مهلت تصویب: {{ $tariffSet->deadline_approval ?? '۱۵ بهمن' }} — مهلت اعلام عمومی: {{ $tariffSet->deadline_announcement ?? 'پایان بهمن' }}</p>
    <p style="font-size:8.5pt;color:#666">این فرم به استناد دستورالعمل وزارت کشور موضوع تبصره ۱ ماده ۲ قانون درآمد پایدار و هزینه شهرداری‌ها و دهیاری‌ها تنظیم شده است.</p>
</div>
@endsection

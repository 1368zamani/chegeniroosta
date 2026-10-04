@extends('admin.tariffs.layout')
@section('title', 'نمایش مجموعه تعرفه')

@section('content')

@if(!$validation['ok'])
    <div class="alert alert-error">
        <strong>⚠️ هشدار قانونی:</strong> موارد زیر از نرخ تورم بیشتر افزایش یافته‌اند:
        @foreach($validation['violations'] as $v)
            <div>• «{{ $v['title'] }}» — {{ $v['increase'] }}٪ (حداکثر {{ $v['max'] }}٪)</div>
        @endforeach
        <div style="margin-top:6px">مطابق ماده ۸ قانون درآمد پایدار، امکان تصویب وجود ندارد تا اصلاح شود.</div>
    </div>
@endif

<div class="card">
    <div style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px">
        <div>
            <h3 style="margin:0 0 6px; border:none; padding:0">
                📋 تعرفه {{ $set->village?->name }} — سال {{ $set->jalali_year }}
            </h3>
            <div style="font-size:13px; color:#546e7a">
                بخش @if(is_object($set->village?->section)) {{ $set->village->section->name }} @elseif(is_string($set->village?->section)) {{ $set->village->section }} @else — @endif — نرخ تورم: <b class="amount">{{ number_format((float)$set->inflation_rate, 2) }}%</b>
                — وضعیت: <span class="pill pill-{{ $set->status }}">{{ $set->statusLabel() }}</span>
            </div>
        </div>
        <div>
            @if(!$set->isLocked())
                <a href="{{ route('admin.tariffs.edit', $set->id) }}" class="btn btn-yellow">✏️ ویرایش آیتم‌ها</a>
                <a href="{{ route('admin.tariffs.form2', $set->id) }}" class="btn btn-blue">📄 فرم ۲ (عوارض)</a>
                <a href="{{ route('admin.tariffs.form3', $set->id) }}" class="btn btn-green">📄 فرم ۳ (بهای خدمات)</a>
            @endif
        </div>
    </div>
</div>

{{-- workflow --}}
<div class="card">
    <h3>🔄 گردش کار تصویب</h3>
    @php
        $stages = [
            'draft' => 'پیش‌نویس',
            'proposed' => 'پیشنهادی دهیار',
            'village_council_approved' => 'تصویب شورای روستا',
            'district_council_approved' => 'تأیید شورای بخش',
            'finalized' => 'قطعی‌شده',
            'published' => 'اعلام عمومی',
        ];
        $current = $set->status;
        $found = false;
    @endphp

    <div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:14px">
        @foreach($stages as $k => $label)
            @if($k === $current)
                @php $found = true; @endphp
                <span style="padding:8px 14px; background:#1565c0; color:#fff; border-radius:8px; font-weight:800; font-size:12.5px">▶ {{ $label }}</span>
            @elseif(!$found)
                <span style="padding:8px 14px; background:#e8f5e9; color:#2e7d32; border-radius:8px; font-weight:700; font-size:12.5px">✓ {{ $label }}</span>
            @else
                <span style="padding:8px 14px; background:#eceff1; color:#90a4ae; border-radius:8px; font-weight:700; font-size:12.5px">{{ $label }}</span>
            @endif
        @endforeach
    </div>

    @if(!$set->isLocked())
        <div style="display:flex; gap:8px; flex-wrap:wrap">
            @php
                $nextMap = [
                    'draft' => 'proposed',
                    'proposed' => 'village_council_approved',
                    'village_council_approved' => 'district_council_approved',
                    'district_council_approved' => 'finalized',
                    'finalized' => 'published',
                ];
                $next = $nextMap[$set->status] ?? null;
            @endphp
            @if($next)
                <form method="POST" action="{{ route('admin.tariffs.transition', $set->id) }}">
                    @csrf
                    <input type="hidden" name="to" value="{{ $next }}">
                    <button class="btn btn-green" onclick="return confirm('انتقال به مرحله بعد؟')">▶ انتقال به مرحله بعد</button>
                </form>
            @endif
        </div>
    @else
        <div class="alert alert-info">🔒 این مجموعه قفل شده است.</div>
    @endif
</div>

{{-- PDF مصوبه --}}
<div class="card">
    <h3>📎 فایل مصوبه شورای اسلامی</h3>
    @if($set->approval_document_path)
        <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap">
            <a href="{{ route('admin.tariffs.approval.download', $set->id) }}" class="btn btn-blue">⬇️ دانلود PDF</a>
            @if(!$set->isLocked())
                <form method="POST" action="{{ route('admin.tariffs.approval.delete', $set->id) }}" onsubmit="return confirm('حذف فایل؟')">
                    @csrf @method('DELETE')
                    <button class="btn btn-red">🗑 حذف</button>
                </form>
            @endif
        </div>
    @else
        @if(!$set->isLocked())
            <form method="POST" action="{{ route('admin.tariffs.approval.upload', $set->id) }}" enctype="multipart/form-data">
                @csrf
                <input type="file" name="approval_document" accept=".pdf" required>
                <button class="btn btn-blue" style="margin-top:8px">📤 آپلود PDF</button>
            </form>
        @else
            <div style="color:#90a4ae">فایلی آپلود نشده است.</div>
        @endif
    @endif
</div>

{{-- عوارض --}}
<div class="card">
    <h3>💰 عوارض محلی ({{ $levyItems->count() }} عنوان)</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>عنوان</th>
                <th>مبلغ سال قبل</th>
                <th>درصد افزایش</th>
                <th>مبلغ نهایی</th>
                <th>وضعیت</th>
            </tr>
        </thead>
        <tbody>
            @foreach($levyItems as $i => $item)
                <tr>
                    <td class="amount">{{ $i + 1 }}</td>
                    <td>
                        {{ $item->title?->name_fa }}
                        @if($item->title?->is_optional) <span style="color:#f9a825; font-size:11px">(اختیاری)</span> @endif
                    </td>
                    <td class="amount">{{ number_format($item->previous_amount) }}</td>
                    <td class="amount">{{ number_format((float)$item->increase_percent, 2) }}%</td>
                    <td class="amount" style="font-weight:800">{{ number_format($item->final_amount) }}</td>
                    <td>
                        @if($item->is_active)
                            <span style="color:#2e7d32">✓ فعال</span>
                        @else
                            <span style="color:#90a4ae">— غیرفعال</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- بهای خدمات --}}
<div class="card">
    <h3>🏛 بهای خدمات ({{ $serviceItems->count() }} عنوان)</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>عنوان</th>
                <th>مبلغ سال قبل</th>
                <th>درصد افزایش</th>
                <th>مبلغ نهایی</th>
                <th>وضعیت</th>
            </tr>
        </thead>
        <tbody>
            @foreach($serviceItems as $i => $item)
                <tr>
                    <td class="amount">{{ $i + 1 }}</td>
                    <td>
                        {{ $item->title?->name_fa }}
                        @if($item->title?->is_optional) <span style="color:#f9a825; font-size:11px">(اختیاری)</span> @endif
                    </td>
                    <td class="amount">{{ number_format($item->previous_amount) }}</td>
                    <td class="amount">{{ number_format((float)$item->increase_percent, 2) }}%</td>
                    <td class="amount" style="font-weight:800">{{ number_format($item->final_amount) }}</td>
                    <td>
                        @if($item->is_active)
                            <span style="color:#2e7d32">✓ فعال</span>
                        @else
                            <span style="color:#90a4ae">— غیرفعال</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- جریمه --}}
@if($penaltyItems->count() > 0)
    <div class="card">
        <h3>⚠️ جریمه‌ها ({{ $penaltyItems->count() }})</h3>
        <table>
            <thead><tr><th>عنوان</th><th>مبلغ</th><th>توضیح</th></tr></thead>
            <tbody>
                @foreach($penaltyItems as $item)
                    <tr>
                        <td>{{ $item->title?->name_fa }}</td>
                        <td class="amount">{{ number_format($item->final_amount) }}</td>
                        <td style="color:#546e7a; font-size:12px">{{ $item->title?->statutory_note }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>تعرفه عوارض {{ $village->name }} — سال {{ $year }}</title>
<link rel="icon" type="image/png" href="{{ url('/favicon.png') }}">
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
<style>
* { box-sizing: border-box; }
body { margin: 0; font-family: 'Vazirmatn', Tahoma, sans-serif; background: #f5f9ff; color: #1565c0; line-height: 1.9; }
.container { width: min(1100px, 96%); margin: 20px auto; padding: 0 15px; }
.header { background: linear-gradient(135deg, #0d3b2e 0%, #065f46 100%); color: #fff; padding: 24px; border-radius: 14px; margin-bottom: 20px; text-align: center; }
.header h1 { margin: 0 0 6px; font-size: 22px; }
.header p { margin: 0; font-size: 13px; opacity: .9; }
.year-selector { background: #fff; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; border-top: 4px solid #1565c0; display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
.year-selector a { padding: 8px 16px; background: #e3f2fd; color: #1565c0; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 13px; }
.year-selector a.active { background: #1565c0; color: #fff; }
.notice { background: #fff8e1; border-right: 5px solid #f9a825; border-radius: 10px; padding: 14px 18px; margin: 20px 0; font-size: 13.5px; color: #5d4037; font-weight: 700; }
.notice.danger { background: #ffebee; border-right-color: #c62828; color: #b71c1c; }
.card { background: #fff; border-radius: 14px; padding: 20px; margin-bottom: 20px; border-top: 4px solid #1565c0; box-shadow: 0 4px 15px rgba(13,46,79,0.06); }
.card h2 { font-size: 17px; color: #0d47a1; margin: 0 0 14px; padding-bottom: 10px; border-bottom: 2px solid #e3f2fd; }
table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
th, td { padding: 11px 10px; border-bottom: 1px solid #eceff1; text-align: right; }
th { background: #e3f2fd; color: #0d47a1; font-weight: 800; }
tr:hover { background: #f5f9ff; }
.amount { font-family: monospace; direction: ltr; display: inline-block; font-weight: 700; }
.badge { display: inline-block; padding: 3px 10px; background: #f9a825; color: #fff; border-radius: 20px; font-size: 11px; font-weight: 800; }
@media (max-width: 700px) { th, td { padding: 8px 5px; font-size: 12.5px; } .header h1 { font-size: 18px; } }
</style>
</head>
<body>

<div class="container">

    <div class="header">
        <h1>💰 تعرفه عوارض و بهای خدمات {{ $village->name }}</h1>
        <p>سال {{ $year }} — بخش {{ $village->section?->name ?? '' }} — شهرستان چگنی، استان لرستان</p>
    </div>

    @if($publishedYears->count() > 1)
        <div class="year-selector">
            <strong>انتخاب سال:</strong>
            @foreach($publishedYears as $y)
                <a href="?year={{ $y }}" class="{{ $y == $year ? 'active' : '' }}">{{ $y }}</a>
            @endforeach
        </div>
    @endif

    <div class="notice danger">
        ⚠️ <strong>هشدار قانونی:</strong> دریافت وجه نقد توسط دهیار یا کارکنان دهیاری <strong>ممنوع</strong> است.
        وجوه باید به حساب بانکی دهیاری واریز شود.
    </div>

    @if($set->approval_document_path)
        <div class="notice">
            📎 <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($set->approval_document_path) }}" target="_blank">
                مشاهده PDF مصوبه شورای اسلامی روستا
            </a>
        </div>
    @endif

    {{-- عوارض --}}
    <div class="card">
        <h2>💰 عوارض محلی ({{ $levyItems->count() }} عنوان)</h2>
        @if($levyItems->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width:50px">ردیف</th>
                        <th>عنوان</th>
                        <th style="width:100px">واحد</th>
                        <th style="width:180px">نرخ نهایی</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($levyItems as $i => $item)
                        <tr>
                            <td class="amount">{{ $i + 1 }}</td>
                            <td>
                                {{ $item->title?->name_fa }}
                                @if($item->title?->is_optional) <span class="badge">اختیاری</span> @endif
                                @if($item->notes)
                                    <div style="font-size:11.5px; color:#78909c">{{ $item->notes }}</div>
                                @endif
                            </td>
                            <td style="color:#546e7a">{{ $item->unit === 'rial' ? 'ریال' : $item->unit }}</td>
                            <td class="amount" style="font-weight:800; font-size:14px">{{ number_format($item->final_amount) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color:#90a4ae">موردی ثبت نشده.</p>
        @endif
    </div>

    {{-- بهای خدمات --}}
    <div class="card">
        <h2>🏛 بهای خدمات ({{ $serviceItems->count() }} عنوان)</h2>
        @if($serviceItems->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width:50px">ردیف</th>
                        <th>عنوان</th>
                        <th style="width:100px">واحد</th>
                        <th style="width:180px">نرخ نهایی</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($serviceItems as $i => $item)
                        <tr>
                            <td class="amount">{{ $i + 1 }}</td>
                            <td>
                                {{ $item->title?->name_fa }}
                                @if($item->title?->is_optional) <span class="badge">اختیاری</span> @endif
                            </td>
                            <td style="color:#546e7a">{{ $item->unit === 'rial' ? 'ریال' : $item->unit }}</td>
                            <td class="amount" style="font-weight:800; font-size:14px">{{ number_format($item->final_amount) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color:#90a4ae">موردی ثبت نشده.</p>
        @endif
    </div>

    {{-- جریمه --}}
    @if($penaltyItems->count() > 0)
        <div class="card">
            <h2>⚠️ جریمه‌ها</h2>
            <table>
                <thead><tr><th>عنوان</th><th>نرخ</th><th>توضیح قانونی</th></tr></thead>
                <tbody>
                    @foreach($penaltyItems as $item)
                        <tr>
                            <td>{{ $item->title?->name_fa }}</td>
                            <td class="amount">{{ number_format($item->final_amount) }}</td>
                            <td style="font-size:12px; color:#546e7a">{{ $item->title?->statutory_note }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div style="text-align:center; padding: 20px; color:#90a4ae; font-size: 12px">
        این تعرفه بر اساس دستورالعمل وزارت کشور موضوع تبصره ۱ ماده ۲ قانون درآمد پایدار و هزینه شهرداری‌ها و دهیاری‌ها تصویب شده است.
        <br>
        تاریخ اعلام عمومی: {{ $set->announced_at ? \Morilog\Jalali\Jalalian::fromCarbon($set->announced_at)->format('Y/m/d') : '-' }}
    </div>

</div>
</body>
</html>

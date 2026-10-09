<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تعرفه عوارض و بهای خدمات | بخشداری {{ $section->name }}</title>
<style>
* { box-sizing: border-box; }
body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #eef2f7; color: #263238; padding-bottom: 40px; }
.s-header { background: linear-gradient(135deg, #1e5b9c, #2470b8); color: white; padding: 26px 24px; box-shadow: 0 4px 15px rgba(30,91,156,0.2); }
.s-header h1 { margin: 0 0 6px; font-size: 22px; }
.s-header p { margin: 0; font-size: 13.5px; opacity: 0.9; }
.s-header a.back { color: white; text-decoration: none; display: inline-block; margin-top: 14px; background: rgba(255,255,255,0.15); padding: 8px 16px; border-radius: 8px; font-size: 13px; }
.s-container { max-width: 1100px; margin: 24px auto; padding: 0 20px; }
.s-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 14px; margin-bottom: 24px; }
.s-stat { background: white; border-radius: 12px; padding: 18px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-top: 4px solid #1e5b9c; }
.s-stat .num { font-size: 26px; font-weight: 800; color: #1e5b9c; }
.s-stat .label { font-size: 12px; color: #78909c; margin-top: 4px; }
.s-stat.ok { border-top-color: #2e7d32; } .s-stat.ok .num { color: #2e7d32; }
.s-stat.warn { border-top-color: #d97706; } .s-stat.warn .num { color: #d97706; }
.s-card { background: white; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 24px; }
.s-card .card-head { padding: 16px 22px; background: #f8fafc; border-bottom: 2px solid #e3f2fd; font-weight: 700; color: #1e5b9c; display: flex; justify-content: space-between; align-items: center; font-size: 15px; }
.s-table { width: 100%; border-collapse: collapse; }
.s-table th, .s-table td { padding: 12px 14px; text-align: right; border-bottom: 1px solid #eceff1; font-size: 13.5px; }
.s-table th { background: #f8fafc; color: #546e7a; font-weight: 700; font-size: 12.5px; }
.s-table tr:hover { background: #f8fafc; }
.s-table tr:last-child td { border-bottom: none; }
.s-badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 11.5px; font-weight: 700; }
.s-badge.ok { background: #c8e6c9; color: #2e7d32; }
.s-badge.wait { background: #fef3c7; color: #92400e; }
.s-badge.none { background: #eceff1; color: #607d8b; }
.s-link { display: inline-block; padding: 6px 14px; border-radius: 8px; text-decoration: none; font-size: 12.5px; font-weight: 600; background: #1e5b9c; color: white; }
.s-link:hover { background: #14497f; }
.s-warning { background: #fef3c7; border-right: 4px solid #d97706; color: #78350f; padding: 14px 18px; border-radius: 10px; margin-bottom: 24px; font-size: 13.5px; line-height: 1.9; }
.s-empty { padding: 40px 20px; text-align: center; color: #90a4ae; }
@media (max-width: 700px) { .s-table { font-size: 12px; } .s-table th, .s-table td { padding: 8px 10px; } .s-table .hide-sm { display: none; } }
</style>
</head>
<body>
<div class="s-header">
    <h1>🧾 تعرفه عوارض و بهای خدمات دهیاری‌ها</h1>
    <p>بخشداری {{ $section->name }} — سال جاری</p>
    <a href="{{ url('/') }}" class="back">← بازگشت به صفحه‌ی بخشداری</a>
</div>
<div class="s-container">
    <div class="s-warning">
        ⚠️ دریافت وجه نقد توسط دهیار یا کارکنان <strong>ممنوع</strong> است. وجوه باید به حساب بانکی دهیاری واریز شود.<br>
        📖 مبنای قانونی: تبصره ۱ ماده ۲ قانون درآمد پایدار و هزینه شهرداری‌ها و دهیاری‌ها.
    </div>
    <div class="s-stats">
        <div class="s-stat"><div class="num">{{ number_format($stats['total']) }}</div><div class="label">کل روستاهای بخش</div></div>
        <div class="s-stat ok"><div class="num">{{ number_format($stats['published']) }}</div><div class="label">تعرفه‌ی منتشرشده</div></div>
        <div class="s-stat warn"><div class="num">{{ number_format($stats['pending']) }}</div><div class="label">در حال اقدام</div></div>
        <div class="s-stat"><div class="num">{{ number_format($stats['none']) }}</div><div class="label">بدون تعرفه</div></div>
    </div>
    <div class="s-card">
        <div class="card-head">
            <span>🏘 لیست روستاها</span>
            <span style="font-size:12px;color:#78909c;">{{ number_format($villages->count()) }} روستا</span>
        </div>
        @if($villages->isEmpty())
            <div class="s-empty">هیچ روستایی برای این بخش ثبت نشده است.</div>
        @else
            <table class="s-table">
                <thead><tr>
                    <th>#</th><th>نام روستا</th><th class="hide-sm">سال تعرفه</th><th>وضعیت</th><th>عملیات</th>
                </tr></thead>
                <tbody>
                    @foreach($villages as $i => $village)
                        @php
                            $pubSet = $published->get($village->id);
                            $anySet = $allSets->get($village->id)?->first();
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $village->name }}</td>
                            <td class="hide-sm">
                                @if($pubSet) {{ $pubSet->jalali_year }}
                                @elseif($anySet) {{ $anySet->jalali_year }} (پیش‌نویس)
                                @else — @endif
                            </td>
                            <td>
                                @if($pubSet) <span class="s-badge ok">✓ منتشر شده</span>
                                @elseif($anySet) <span class="s-badge wait">⏳ {{ $anySet->statusLabel() }}</span>
                                @else <span class="s-badge none">— بدون تعرفه</span>
                                @endif
                            </td>
                            <td>
                                @if($pubSet)
                                    <a href="{{ route('village.tariff.index', $village->slug) }}" class="s-link">مشاهده →</a>
                                @else
                                    <span style="color:#90a4ae;font-size:12px;">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
</body>
</html>

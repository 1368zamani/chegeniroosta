<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>عوارض روستاهای بخش {{ $section->name }} | پرتال چگنی</title>
<meta name="description" content="عوارض احداث بنا و پسماند روستاهای بخش {{ $section->name }}؛ مشاهده تنظیمات هر روستا و پرداخت به حساب دهیاری در پرتال چگنی.">
<meta name="robots" content="index, follow">
<meta property="og:site_name" content="بخشداری {{ $section->name }}">
<meta property="og:type" content="website">
<meta property="og:title" content="عوارض روستاهای بخش {{ $section->name }}">
<meta property="og:description" content="عوارض احداث بنا و پسماند روستاهای بخش {{ $section->name }}؛ مشاهده تنظیمات هر روستا و پرداخت به حساب دهیاری در پرتال چگنی.">
<meta property="og:url" content="{{ url()->current() }}">
<meta name="twitter:card" content="summary">
<link rel="canonical" href="{{ url()->current() }}">
<style>
* { box-sizing: border-box; }
body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #fff7e9; color: #2b2119; padding-bottom: 40px; }
.s-header { background: linear-gradient(135deg, #c94a2b, #b3401f); color: white; padding: 26px 24px; box-shadow: 0 4px 15px rgba(30,91,156,0.2); }
.s-header h1 { margin: 0 0 6px; font-size: 22px; }
.s-header p { margin: 0; font-size: 13.5px; opacity: 0.9; }
.s-header a.back { color: white; text-decoration: none; display: inline-block; margin-top: 14px; background: rgba(255,255,255,0.15); padding: 8px 16px; border-radius: 8px; font-size: 13px; }
.s-container { max-width: 1100px; margin: 24px auto; padding: 0 20px; }
.s-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 14px; margin-bottom: 24px; }
.s-stat { background: white; border-radius: 12px; padding: 18px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-top: 4px solid #c94a2b; }
.s-stat .num { font-size: 26px; font-weight: 800; color: #c94a2b; }
.s-stat .label { font-size: 12px; color: #8a7a6a; margin-top: 4px; }
.s-stat.ok { border-top-color: #c94a2b; } .s-stat.ok .num { color: #c94a2b; }
.s-stat.warn { border-top-color: #d97706; } .s-stat.warn .num { color: #d97706; }
.s-card { background: white; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 24px; }
.s-card .card-head { padding: 16px 22px; background: #fffdf9; border-bottom: 2px solid #fdeee7; font-weight: 700; color: #c94a2b; display: flex; justify-content: space-between; align-items: center; font-size: 15px; }
.s-table { width: 100%; border-collapse: collapse; }
.s-table th, .s-table td { padding: 12px 14px; text-align: right; border-bottom: 1px solid #f7f0e6; font-size: 13.5px; }
.s-table th { background: #fffdf9; color: #6b5a4a; font-weight: 700; font-size: 12.5px; }
.s-table tr:hover { background: #fffdf9; }
.s-table tr:last-child td { border-bottom: none; }
.s-badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 11.5px; font-weight: 700; }
.s-badge.ok { background: #f7d9cc; color: #c94a2b; }
.s-badge.wait { background: #fdf1dc; color: #92400e; }
.s-badge.none { background: #f7f0e6; color: #6b5a4a; }
.s-link { display: inline-block; padding: 6px 14px; border-radius: 8px; text-decoration: none; font-size: 12.5px; font-weight: 600; background: #c94a2b; color: white; }
.s-link:hover { background: #8e2f18; }
.s-warning { background: #fdf1dc; border-right: 4px solid #d97706; color: #78350f; padding: 14px 18px; border-radius: 10px; margin-bottom: 24px; font-size: 13.5px; line-height: 1.9; }
.s-empty { padding: 40px 20px; text-align: center; color: #a2948a; }
@media (max-width: 700px) { .s-table { font-size: 12px; } .s-table th, .s-table td { padding: 8px 10px; } .s-table .hide-sm { display: none; } }
</style>
@include('partials.pwa')
    <link rel="stylesheet" href="{{ asset('css/ui-v3.css') }}?v=8">
</head>
<body class="v3-body">
@include('partials.v3-crumbs', ['crumbs' => [
    ['label' => 'پرتال چگنی', 'url' => url('/')],
    ['label' => 'بخش ' . $section->name, 'url' => url('/')],
    ['label' => 'عوارض روستاها'],
]])
<div class="s-header">
    <h1>🧾 عوارض روستاهای بخش {{ $section->name }}</h1>
    <p>برای مشاهده مبلغ و پرداخت، روستای خود را انتخاب کنید.</p>
    <a href="{{ url('/') }}" class="back">← بازگشت به صفحه‌ی بخشداری</a>
</div>
<div class="s-container">
    <div class="s-warning">
        ⚠️ مبلغ عوارض بر اساس تنظیمات مصوب هر روستا محاسبه میشود و وجوه باید به حساب بانکی همان دهیاری واریز شود.<br>
        📖 مبنای قانونی: تبصره ۱ ماده ۲ قانون درآمد پایدار و هزینه شهرداری‌ها و دهیاری‌ها.
    </div>
    <div class="s-stats">
        <div class="s-stat"><div class="num">{{ number_format($stats['total']) }}</div><div class="label">کل روستاهای بخش</div></div>
        <div class="s-stat ok"><div class="num">{{ number_format($stats['ready']) }}</div><div class="label">عوارض تنظیمشده</div></div>
        <div class="s-stat warn"><div class="num">{{ number_format($stats['pending']) }}</div><div class="label">تنظیمنشده</div></div>
    </div>
    <div class="s-card">
        <div class="card-head">
            <span>🏘 لیست روستاها</span>
            <span style="font-size:12px;color:#8a7a6a;">{{ number_format($villages->count()) }} روستا</span>
        </div>
        @if($villages->isEmpty())
            <div class="s-empty">هیچ روستایی برای این بخش ثبت نشده است.</div>
        @else
            <table class="s-table">
                <thead><tr>
                    <th>#</th><th>نام روستا</th><th>وضعیت عوارض</th><th>عملیات</th>
                </tr></thead>
                <tbody>
                    @foreach($villages as $i => $village)
                        @php $param = $params->get($village->id); @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $village->name }}</td>
                            <td>
                                @if($param) <span class="s-badge ok">✓ تنظیم شده</span>
                                @else <span class="s-badge none">— تنظیم نشده</span>
                                @endif
                            </td>
                            <td>
                                @if($param)
                                    <a href="{{ route('village.tariff.index', $village->slug) }}" class="s-link">مشاهده عوارض →</a>
                                @else
                                    <span style="color:#a2948a;font-size:12px;">—</span>
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

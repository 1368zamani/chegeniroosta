<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>آمار سایت</title>
<style>
    * { box-sizing: border-box; }
    body { margin:0; background:#f5f7fa; color:#263238; }
    .wrap { max-width:1150px; margin:auto; padding:24px 18px 60px; }

    .top { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:20px; }
    .top h1 { margin:0; font-size:22px; color:#2e7d32; }
    .top .sub { color:#78909c; font-size:13px; margin-top:4px; }
    .top a.back { background:#2e7d32; color:#fff; text-decoration:none; padding:9px 18px; border-radius:8px; font-size:13px; }

    .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(165px,1fr)); gap:14px; margin-bottom:24px; }
    .card { background:#fff; border-radius:12px; padding:18px; box-shadow:0 4px 16px rgba(0,0,0,.05); text-align:center; border-top:3px solid #2e7d32; }
    .card .num { font-size:26px; font-weight:800; color:#2e7d32; }
    .card .lbl { font-size:12.5px; color:#607d8b; margin-top:6px; }

    .panel { background:#fff; border-radius:12px; padding:20px; box-shadow:0 4px 16px rgba(0,0,0,.05); margin-bottom:22px; }
    .panel h2 { margin:0 0 16px; font-size:16px; color:#2e7d32; padding-bottom:10px; border-bottom:2px solid #e8f5e9; }

    /* ── نمودار میله‌ای بدون هیچ کتابخانه‌ی خارجی ── */
    .chart { display:flex; align-items:flex-end; gap:3px; height:190px; padding:10px 4px 0; }
    .chart .bar { flex:1; background:linear-gradient(180deg,#43a047,#2e7d32); border-radius:4px 4px 0 0;
                  min-height:2px; position:relative; transition:opacity .15s; }
    .chart .bar:hover { opacity:.75; }
    .chart .bar.zero { background:#e0e0e0; }
    .chart-x { display:flex; gap:3px; margin-top:6px; }
    .chart-x span { flex:1; text-align:center; font-size:9.5px; color:#90a4ae; }
    .chart-note { font-size:12px; color:#90a4ae; margin-top:12px; }

    table { width:100%; border-collapse:collapse; }
    th, td { padding:10px 12px; text-align:right; font-size:13px; border-bottom:1px solid #eceff1; }
    th { background:#f8fafc; color:#546e7a; font-weight:600; font-size:12.5px; }
    tr:hover td { background:#f8fafc; }
    .rank { display:inline-flex; width:22px; height:22px; align-items:center; justify-content:center;
            border-radius:50%; background:#e8f5e9; color:#2e7d32; font-size:11.5px; font-weight:700; }
    .num-cell { font-weight:700; color:#2e7d32; }
    .empty { text-align:center; color:#90a4ae; padding:26px 10px; font-size:13.5px; }
    .cols { display:grid; grid-template-columns:1fr 1fr; gap:22px; }
    .kind { font-size:11.5px; background:#e8f5e9; color:#2e7d32; padding:2px 9px; border-radius:10px; }
    .note { background:#f1f8e9; border-right:4px solid #7cb342; padding:12px 15px; border-radius:8px;
            font-size:12.5px; color:#33691e; line-height:2; margin-bottom:20px; }
    @media (max-width:800px) { .cols { grid-template-columns:1fr; } .chart { height:140px; } }
</style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-master.css') }}?v=2">
</head>
<body>

<div class="wrap">

    <div class="top">
        <div>
            <h1>📊 آمار سایت</h1>
            <div class="sub">
                @if($isSuper)
                    آمار کل سامانه — همه‌ی بخش‌ها
                @else
                    آمار بخش <strong>{{ $section->name ?? '—' }}</strong> (فقط محدوده‌ی دسترسی شما)
                @endif
            </div>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="back">← بازگشت به داشبورد</a>
    </div>

    <div class="note">
        ℹ️ شمارش بازدید از زمانی شروع شده که این قابلیت اضافه شد. اعداد قبل از آن ثبت نشده‌اند.
        هر بازدیدکننده در هر نشست فقط <strong>یک بار</strong> شمرده می‌شود، پس رفرش پیاپی عدد را باد نمی‌کند.
        <br>
        نمودار روند از تاریخ راه‌اندازی این جدول پر می‌شود؛ روزهای قبل از آن صفر نمایش داده می‌شوند.
    </div>

    {{-- ═══════════ کارت‌های خلاصه ═══════════ --}}
    <div class="grid">
        <div class="card">
            <div class="num">{{ \App\Support\Number::fa(number_format($summary['views'])) }}</div>
            <div class="lbl">👁 کل بازدید</div>
        </div>
        <div class="card">
            <div class="num">{{ \App\Support\Number::fa(number_format($summary['downloads'])) }}</div>
            <div class="lbl">⬇️ کل دانلود</div>
        </div>
        <div class="card">
            <div class="num">{{ \App\Support\Number::fa(number_format($summary['views_today'])) }}</div>
            <div class="lbl">📅 بازدید امروز</div>
        </div>
        <div class="card">
            <div class="num">{{ \App\Support\Number::fa(number_format($summary['downloads_today'])) }}</div>
            <div class="lbl">📅 دانلود امروز</div>
        </div>
        <div class="card">
            <div class="num">{{ \App\Support\Number::fa(number_format($summary['news'])) }}</div>
            <div class="lbl">📰 اخبار بخش</div>
        </div>
        <div class="card">
            <div class="num">{{ \App\Support\Number::fa(number_format($summary['villages'])) }}</div>
            <div class="lbl">🏘️ روستاهای فعال</div>
        </div>
    </div>

    {{-- ═══════════ نمودار روند ═══════════ --}}
    <div class="panel">
        <h2>📈 روند بازدید ۳۰ روز اخیر</h2>

        @php $anyData = collect($trend)->sum('views') > 0; @endphp

        @if($anyData)
            <div class="chart">
                @foreach($trend as $day)
                    <div class="bar {{ $day['views'] === 0 ? 'zero' : '' }}"
                         style="height: {{ max($day['views'] === 0 ? 1 : 4, $day['percent']) }}%;"
                         title="{{ $day['label'] }} — {{ \App\Support\Number::fa(number_format($day['views'])) }} بازدید، {{ \App\Support\Number::fa(number_format($day['downloads'])) }} دانلود"></div>
                @endforeach
            </div>
            <div class="chart-x">
                @foreach($trend as $i => $day)
                    @if($i % 5 === 0 || $i === count($trend) - 1)
                        <span>{{ $day['label'] }}</span>
                    @else
                        <span></span>
                    @endif
                @endforeach
            </div>
            <div class="chart-note">
                بیشترین بازدید روزانه در این بازه: <strong>{{ \App\Support\Number::fa(number_format($trendMax)) }}</strong>
                — نشانگر ماوس را روی هر میله ببر تا عدد آن روز را ببینی.
            </div>
        @else
            <div class="empty">
                📉 هنوز داده‌ای برای نمودار ثبت نشده.<br>
                <span style="font-size:12px;">به‌محض بازدید کاربران از سایت، این نمودار پر می‌شود.</span>
            </div>
        @endif
    </div>

    {{-- ═══════════ پربازدیدترین‌ها ═══════════ --}}
    <div class="cols">

        <div class="panel">
            <h2>📰 پربازدیدترین اخبار</h2>
            @if($topNews->isEmpty())
                <div class="empty">هنوز خبری بازدید نشده است.</div>
            @else
                <table>
                    <thead>
                        <tr><th style="width:34px;">#</th><th>عنوان</th><th style="width:80px;">بازدید</th></tr>
                    </thead>
                    <tbody>
                        @foreach($topNews as $i => $row)
                            <tr>
                                <td><span class="rank">{{ \App\Support\Number::fa($i + 1) }}</span></td>
                                <td>{{ \Illuminate\Support\Str::limit($row['title'], 55) }}</td>
                                <td class="num-cell">{{ \App\Support\Number::fa(number_format($row['views'])) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="panel">
            <h2>🏘️ پربازدیدترین روستاها</h2>
            @if($topVillages->isEmpty())
                <div class="empty">هنوز صفحه‌ی روستایی بازدید نشده است.</div>
            @else
                <table>
                    <thead>
                        <tr><th style="width:34px;">#</th><th>روستا</th><th style="width:80px;">بازدید</th></tr>
                    </thead>
                    <tbody>
                        @foreach($topVillages as $i => $row)
                            <tr>
                                <td><span class="rank">{{ \App\Support\Number::fa($i + 1) }}</span></td>
                                <td>
                                    @if($row['url'])
                                        <a href="{{ $row['url'] }}" target="_blank" style="color:#2e7d32;text-decoration:none;">
                                            {{ $row['title'] }} ↗
                                        </a>
                                    @else
                                        {{ $row['title'] }}
                                    @endif
                                </td>
                                <td class="num-cell">{{ \App\Support\Number::fa(number_format($row['views'])) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>

    {{-- ═══════════ پردانلودترین‌ها ═══════════ --}}
    <div class="panel">
        <h2>⬇️ پردانلودترین فایل‌ها</h2>
        @if($topDownloads->isEmpty())
            <div class="empty">هنوز فایلی دانلود نشده است.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width:34px;">#</th>
                        <th>عنوان</th>
                        <th style="width:130px;">نوع</th>
                        <th style="width:80px;">دانلود</th>
                        <th style="width:80px;">بازدید</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topDownloads as $i => $row)
                        <tr>
                            <td><span class="rank">{{ \App\Support\Number::fa($i + 1) }}</span></td>
                            <td>{{ \Illuminate\Support\Str::limit($row['title'], 60) }}</td>
                            <td><span class="kind">{{ $row['kind'] }}</span></td>
                            <td class="num-cell">{{ \App\Support\Number::fa(number_format($row['downloads'])) }}</td>
                            <td style="color:#78909c;">{{ \App\Support\Number::fa(number_format($row['views'])) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>

</body>
</html>

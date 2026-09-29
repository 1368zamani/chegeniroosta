<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>مصوبات شورای اسلامی {{ $village->name }}</title>
<style>
* { box-sizing: border-box; }
body { margin: 0; font-family: 'Vazirmatn', Tahoma, Arial, sans-serif; background: #f5f7fa; color: #263238; }
.wrap { max-width: 1000px; margin: 30px auto; padding: 0 15px; }
.page-title { font-size: 26px; color: #1e5b9c; margin: 0 0 25px; padding-bottom: 15px; border-bottom: 3px solid #ffd54f; }
.page-title span { color: #14497f; }

.res-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 12px; margin-bottom: 25px; }
.res-stat { background: white; border-radius: 12px; padding: 16px; text-align: center; box-shadow: 0 3px 12px rgba(0,0,0,.06); border-top: 4px solid #1e5b9c; }
.res-stat .num { font-size: 22px; font-weight: 800; color: #1e5b9c; display: block; }
.res-stat .lbl { font-size: 12px; color: #78909c; margin-top: 4px; }

.res-card {
    background: white;
    border-radius: 14px;
    padding: 22px 24px;
    margin-bottom: 16px;
    box-shadow: 0 4px 15px rgba(0,0,0,.06);
    border-right: 5px solid #1e5b9c;
    transition: transform .2s, box-shadow .2s;
}
.res-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(30,91,156,.12); }

.res-card.status-approved { border-right-color: #1e40af; }
.res-card.status-in_progress { border-right-color: #f59e0b; }
.res-card.status-completed { border-right-color: #16a34a; }
.res-card.status-suspended { border-right-color: #6b7280; }
.res-card.status-rejected { border-right-color: #dc2626; }

.res-header { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px; margin-bottom: 14px; }
.res-title { margin: 0; font-size: 18px; color: #14497f; line-height: 1.5; flex: 1; min-width: 250px; }
.res-badge { display: inline-block; padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; white-space: nowrap; }

.res-meta { display: flex; flex-wrap: wrap; gap: 16px; color: #78909c; font-size: 12.5px; margin-bottom: 14px; }
.res-meta span { display: inline-flex; align-items: center; gap: 5px; }

.res-description { color: #546e7a; line-height: 2; font-size: 14px; margin-bottom: 18px; white-space: pre-line; }

.res-progress { margin-top: 15px; }
.res-progress-label { display: flex; justify-content: space-between; font-size: 12px; color: #64748b; margin-bottom: 6px; }
.res-progress-bar { background: #e2e8f0; border-radius: 10px; height: 12px; overflow: hidden; }
.res-progress-fill { height: 100%; border-radius: 10px; transition: width .5s ease; }

.res-note { background: #f8fafc; border-right: 3px solid #1e5b9c; padding: 12px 16px; border-radius: 8px; margin-top: 14px; font-size: 13px; color: #546e7a; line-height: 1.9; }
.res-note strong { color: #14497f; display: block; margin-bottom: 6px; }

.res-attachment { display: inline-block; margin-top: 12px; padding: 7px 16px; background: #f0f9ff; color: #1e5b9c; text-decoration: none; border-radius: 8px; font-size: 13px; border: 1px solid #bae6fd; }

.empty-box { background: white; padding: 60px 20px; text-align: center; border-radius: 14px; color: #90a4ae; }
.empty-box .icon { font-size: 60px; margin-bottom: 14px; }

@media (max-width: 700px) {
    .page-title { font-size: 20px; }
    .res-card { padding: 18px; }
    .res-title { font-size: 15.5px; }
}
</style>
</head>
<body>

@include('partials.gov-header', ['headerTitle' => 'مصوبات شورای اسلامی ' . $village->name])

<div class="wrap">

    <h1 class="page-title">📋 مصوبات شورای اسلامی <span>{{ $village->name }}</span></h1>

    @if($resolutions->count() > 0)

        {{-- آمار --}}
        @php
            $total = $resolutions->count();
            $completed = $resolutions->where('status', 'completed')->count();
            $inProgress = $resolutions->where('status', 'in_progress')->count();
            $avgProgress = $resolutions->avg('progress_percent') ?: 0;
        @endphp
        <div class="res-stats">
            <div class="res-stat"><span class="num">{{ $total }}</span><span class="lbl">کل مصوبات</span></div>
            <div class="res-stat"><span class="num">{{ $inProgress }}</span><span class="lbl">در حال اجرا</span></div>
            <div class="res-stat"><span class="num">{{ $completed }}</span><span class="lbl">اجرا شده</span></div>
            <div class="res-stat"><span class="num">{{ round($avgProgress) }}%</span><span class="lbl">میانگین پیشرفت</span></div>
        </div>

        {{-- لیست مصوبات --}}
        @foreach($resolutions as $res)
            <div class="res-card status-{{ $res->status }}">
                <div class="res-header">
                    <h3 class="res-title">{{ $res->title }}</h3>
                    <span class="res-badge" style="background:{{ $res->status_bg }};color:{{ $res->status_color }};">
                        {{ $res->status_label }}
                    </span>
                </div>

                <div class="res-meta">
                    @if($res->session_number)
                        <span>📌 جلسه شماره {{ $res->session_number }}</span>
                    @endif
                    @if($res->session_date)
                        <span>📅 {{ jalali($res->session_date) }}</span>
                    @endif
                    @if($res->budget)
                        <span>💰 {{ number_format($res->budget) }} ریال</span>
                    @endif
                    @if($res->deadline)
                        <span>⏰ مهلت: {{ jalali($res->deadline) }}</span>
                    @endif
                </div>

                @if($res->description)
                    <div class="res-description">{{ $res->description }}</div>
                @endif

                {{-- نوار پیشرفت --}}
                <div class="res-progress">
                    <div class="res-progress-label">
                        <span>📊 میزان پیشرفت</span>
                        <span style="color:{{ $res->status_color }};font-weight:700;">{{ $res->progress_percent }}%</span>
                    </div>
                    <div class="res-progress-bar">
                        <div class="res-progress-fill" style="width:{{ $res->progress_percent }}%;background:{{ $res->status_color }};"></div>
                    </div>
                </div>

                @if($res->admin_note)
                    <div class="res-note">
                        <strong>📝 توضیحات پیگیری:</strong>
                        {{ $res->admin_note }}
                    </div>
                @endif

                @if($res->attachment)
                    <a href="{{ asset('storage/' . $res->attachment) }}" target="_blank" class="res-attachment">📎 دانلود فایل پیوست</a>
                @endif
            </div>
        @endforeach

    @else
        <div class="empty-box">
            <div class="icon">📋</div>
            <p>هنوز مصوبه‌ای برای این روستا ثبت نشده است.</p>
        </div>
    @endif

</div>

@include('partials.gov-footer')
@include('partials.back-to-top')

</body>
</html>

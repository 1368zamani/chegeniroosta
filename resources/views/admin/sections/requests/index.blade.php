<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>درخواست‌های بخش {{ $section->name }}</title>
<style>
* { box-sizing: border-box; }
body { margin:0; font-family: Tahoma, Arial, sans-serif; background:#fff7e9; color:#2b2119; }
.header { background: linear-gradient(135deg, #c94a2b, #c94a2b); color: white; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
.header h1 { margin: 0; font-size: 20px; }
.header .nav { display: flex; gap: 10px; }
.header .nav a { color: white; text-decoration: none; padding: 8px 16px; background: rgba(255,255,255,0.15); border-radius: 8px; font-size: 13px; }
.header .nav a:hover { background: rgba(255,255,255,0.25); }
.container { max-width: 1200px; margin: auto; padding: 30px 20px; }
.success { background: #fbdcd0; color: #c94a2b; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; }
.stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 20px; }
.stat-card { background: white; padding: 16px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,.05); text-align: center; }
.stat-card .num { font-size: 24px; font-weight: 800; }
.stat-card .lbl { font-size: 12px; color: #5a4a3c; margin-top: 4px; }
.filter { background: white; padding: 16px; border-radius: 10px; margin-bottom: 16px; display: flex; gap: 10px; flex-wrap: wrap; align-items: end; }
.filter input, .filter select { padding: 10px; border: 1px solid #e6d8c3; border-radius: 8px; font-family: inherit; }
.filter button { padding: 10px 20px; background: #c94a2b; color: white; border: none; border-radius: 8px; cursor: pointer; font-family: inherit; }
table { width: 100%; background: white; border-radius: 12px; overflow: hidden; border-collapse: collapse; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
th, td { padding: 12px 14px; text-align: right; font-size: 13px; border-bottom: 1px solid #f5efe4; }
th { background: #fffdf9; color: #5a4a3c; font-weight: 600; }
tr:hover { background: #fffdf9; }
.badge { padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; display: inline-block; }
.btn { display: inline-block; padding: 6px 14px; background: #c94a2b; color: white; text-decoration: none; border-radius: 6px; font-size: 12px; }
.btn:hover { background: #8e2f18; }
.empty { text-align: center; padding: 60px 20px; color: #8a7a6a; background: white; border-radius: 12px; }
</style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
<link rel="stylesheet" href="/css/admin-master.css?v=2">
<link rel="stylesheet" href="{{ asset('css/admin-final-fix.css') }}?v=10">
<link rel="stylesheet" href="/css/admin-header-fix.css?v=1"><link rel="stylesheet" href="{{ asset('css/admin-utils.css') }}?v=1">
<link rel="stylesheet" href="{{ asset('css/admin-v3.css') }}?v=1">
</head>
<body>

<div class="header">
    <h1>📩 درخواست‌های مردمی — بخشداری {{ $section->name }}</h1>
    <div class="nav">
        <a href="{{ route('admin.dashboard') }}">← داشبورد</a>
        <a href="{{ route('admin.sections.edit', $section->id) }}">⚙️ تنظیمات بخش</a>
    </div>
</div>

<div class="container">

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <div class="stats">
        <div class="stat-card"><div class="num" style="color:#c94a2b;">{{ $stats['total'] }}</div><div class="lbl">کل درخواست‌ها</div></div>
        <div class="stat-card"><div class="num" style="color:#f5b841;">{{ $stats['pending'] }}</div><div class="lbl">در انتظار بررسی</div></div>
        <div class="stat-card"><div class="num" style="color:#c94a2b;">{{ $stats['resolved'] }}</div><div class="lbl">پاسخ داده شده</div></div>
        <div class="stat-card"><div class="num" style="color:#b3261e;">{{ $stats['rejected'] }}</div><div class="lbl">رد شده</div></div>
    </div>

    <form method="GET" class="filter">
        <div style="flex:1;min-width:200px;">
            <label style="display:block;font-size:12px;margin-bottom:6px;color:#5a4a3c;">جستجو</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="کد، نام، تلفن، موضوع..." style="width:100%;">
        </div>
        <div style="min-width:160px;">
            <label style="display:block;font-size:12px;margin-bottom:6px;color:#5a4a3c;">وضعیت</label>
            <select name="status" style="width:100%;">
                <option value="">— همه —</option>
                <option value="submitted"  @selected(request('status')=='submitted')>ثبت شده</option>
                <option value="reviewing"  @selected(request('status')=='reviewing')>در حال بررسی</option>
                <option value="assigned"   @selected(request('status')=='assigned')>ارجاع شده</option>
                <option value="processing" @selected(request('status')=='processing')>در حال پیگیری</option>
                <option value="resolved"   @selected(request('status')=='resolved')>پاسخ داده شده</option>
                <option value="rejected"   @selected(request('status')=='rejected')>رد شده</option>
            </select>
        </div>
        <div>
            <button type="submit">فیلتر</button>
            @if(request()->filled('status') || request()->filled('q'))
                <a href="{{ route('admin.sections.requests.index', $section->id) }}" style="padding:10px 16px;background:#f5efe4;color:#c94a2b;border-radius:8px;text-decoration:none;display:inline-block;font-size:13px;">پاک کردن</a>
            @endif
        </div>
    </form>

    @if($requests->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>کد پیگیری</th>
                    <th>نام</th>
                    <th>موضوع</th>
                    <th>وضعیت</th>
                    <th>تاریخ</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $req)
                    <tr>
                        <td style="font-family:monospace;direction:ltr;color:#c94a2b;font-weight:700;">{{ $req->tracking_code }}</td>
                        <td>{{ $req->full_name }}</td>
                        <td>{{ $req->subject }}</td>
                        <td>
                            @php
                                $badges = [
                                    'submitted'  => ['bg'=>'#e6d8c3','color'=>'#5a4a3c','label'=>'ثبت شده'],
                                    'reviewing'  => ['bg'=>'#fdeec9','color'=>'#a8730f','label'=>'در حال بررسی'],
                                    'assigned'   => ['bg'=>'#fdeec9','color'=>'#a8730f','label'=>'ارجاع شده'],
                                    'processing' => ['bg'=>'#fdeec9','color'=>'#a8730f','label'=>'در حال پیگیری'],
                                    'resolved'   => ['bg'=>'#fdeee7','color'=>'#8e2f18','label'=>'پاسخ داده شده'],
                                    'rejected'   => ['bg'=>'#fdeceb','color'=>'#991b1b','label'=>'رد شده'],
                                ];
                                $b = $badges[$req->status] ?? ['bg'=>'#e6d8c3','color'=>'#5a4a3c','label'=>$req->status];
                            @endphp
                            <span class="badge" style="background:{{ $b['bg'] }};color:{{ $b['color'] }};">{{ $b['label'] }}</span>
                        </td>
                        <td style="font-size:12px;color:#5a4a3c;">{{ jalali($req->created_at, 'Y/m/d H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.sections.requests.show', [$section->id, $req->id]) }}" class="btn">مشاهده و پاسخ</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top:16px;display:flex;justify-content:center;">
            {{ $requests->links() }}
        </div>
    @else
        <div class="empty">
            <div style="font-size:50px;margin-bottom:12px;">📭</div>
            <p>هیچ درخواستی یافت نشد.</p>
        </div>
    @endif

</div>

</body>
</html>

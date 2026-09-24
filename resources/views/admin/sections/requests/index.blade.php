<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>درخواست‌های بخش {{ $section->name }}</title>
<style>
* { box-sizing: border-box; }
body { margin:0; font-family: Tahoma, Arial, sans-serif; background:#f5f7fa; color:#263238; }
.header { background: linear-gradient(135deg, #1e5b9c, #2470b8); color: white; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
.header h1 { margin: 0; font-size: 20px; }
.header .nav { display: flex; gap: 10px; }
.header .nav a { color: white; text-decoration: none; padding: 8px 16px; background: rgba(255,255,255,0.15); border-radius: 8px; font-size: 13px; }
.header .nav a:hover { background: rgba(255,255,255,0.25); }
.container { max-width: 1200px; margin: auto; padding: 30px 20px; }
.success { background: #c8e6c9; color: #2e7d32; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; }
.stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 20px; }
.stat-card { background: white; padding: 16px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,.05); text-align: center; }
.stat-card .num { font-size: 24px; font-weight: 800; }
.stat-card .lbl { font-size: 12px; color: #64748b; margin-top: 4px; }
.filter { background: white; padding: 16px; border-radius: 10px; margin-bottom: 16px; display: flex; gap: 10px; flex-wrap: wrap; align-items: end; }
.filter input, .filter select { padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; }
.filter button { padding: 10px 20px; background: #1e5b9c; color: white; border: none; border-radius: 8px; cursor: pointer; font-family: inherit; }
table { width: 100%; background: white; border-radius: 12px; overflow: hidden; border-collapse: collapse; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
th, td { padding: 12px 14px; text-align: right; font-size: 13px; border-bottom: 1px solid #eceff1; }
th { background: #f8fafc; color: #546e7a; font-weight: 600; }
tr:hover { background: #f8fafc; }
.badge { padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; display: inline-block; }
.btn { display: inline-block; padding: 6px 14px; background: #1e5b9c; color: white; text-decoration: none; border-radius: 6px; font-size: 12px; }
.btn:hover { background: #14497f; }
.empty { text-align: center; padding: 60px 20px; color: #90a4ae; background: white; border-radius: 12px; }
</style>
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
        <div class="stat-card"><div class="num" style="color:#1e5b9c;">{{ $stats['total'] }}</div><div class="lbl">کل درخواست‌ها</div></div>
        <div class="stat-card"><div class="num" style="color:#f59e0b;">{{ $stats['pending'] }}</div><div class="lbl">در انتظار بررسی</div></div>
        <div class="stat-card"><div class="num" style="color:#16a34a;">{{ $stats['resolved'] }}</div><div class="lbl">پاسخ داده شده</div></div>
        <div class="stat-card"><div class="num" style="color:#dc2626;">{{ $stats['rejected'] }}</div><div class="lbl">رد شده</div></div>
    </div>

    <form method="GET" class="filter">
        <div style="flex:1;min-width:200px;">
            <label style="display:block;font-size:12px;margin-bottom:6px;color:#64748b;">جستجو</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="کد، نام، تلفن، موضوع..." style="width:100%;">
        </div>
        <div style="min-width:160px;">
            <label style="display:block;font-size:12px;margin-bottom:6px;color:#64748b;">وضعیت</label>
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
                <a href="{{ route('admin.sections.requests.index', $section->id) }}" style="padding:10px 16px;background:#f1f5f9;color:#1e5b9c;border-radius:8px;text-decoration:none;display:inline-block;font-size:13px;">پاک کردن</a>
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
                        <td style="font-family:monospace;direction:ltr;color:#1e5b9c;font-weight:700;">{{ $req->tracking_code }}</td>
                        <td>{{ $req->full_name }}</td>
                        <td>{{ $req->subject }}</td>
                        <td>
                            @php
                                $badges = [
                                    'submitted'  => ['bg'=>'#dbeafe','color'=>'#1e40af','label'=>'ثبت شده'],
                                    'reviewing'  => ['bg'=>'#fef3c7','color'=>'#92400e','label'=>'در حال بررسی'],
                                    'assigned'   => ['bg'=>'#fef3c7','color'=>'#92400e','label'=>'ارجاع شده'],
                                    'processing' => ['bg'=>'#fef3c7','color'=>'#92400e','label'=>'در حال پیگیری'],
                                    'resolved'   => ['bg'=>'#d1fae5','color'=>'#065f46','label'=>'پاسخ داده شده'],
                                    'rejected'   => ['bg'=>'#fee2e2','color'=>'#991b1b','label'=>'رد شده'],
                                ];
                                $b = $badges[$req->status] ?? ['bg'=>'#e2e8f0','color'=>'#475569','label'=>$req->status];
                            @endphp
                            <span class="badge" style="background:{{ $b['bg'] }};color:{{ $b['color'] }};">{{ $b['label'] }}</span>
                        </td>
                        <td style="font-size:12px;color:#64748b;">{{ $req->created_at->format('Y/m/d H:i') }}</td>
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

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>روستاهای {{ $section->name }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #f5f7fa; color: #263238; }

        .header {
            background: linear-gradient(135deg, #2e7d32, #1b5e20);
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header h1 { margin: 0; font-size: 20px; }
        .header .nav { display: flex; gap: 10px; }
        .header .nav a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            background: rgba(255,255,255,0.15);
            border-radius: 8px;
            font-size: 13px;
        }
        .header .nav a:hover { background: rgba(255,255,255,0.25); }

        .container { max-width: 1200px; margin: auto; padding: 30px 20px; }

        .success {
            background: #c8e6c9;
            color: #2e7d32;
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 24px;
        }

        .info-bar {
            background: white;
            padding: 18px 24px;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 5px 18px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .info-bar h2 { margin: 0; color: #2e7d32; font-size: 18px; }
        .info-bar .count { color: #78909c; font-size: 13px; }

        table {
            width: 100%;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            border-collapse: collapse;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        th, td {
            padding: 14px 16px;
            text-align: right;
            border-bottom: 1px solid #eceff1;
            font-size: 13px;
        }

        th { background: #f8fafc; color: #546e7a; font-weight: 600; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: #f8fafc; }

        .btn {
            display: inline-block;
            padding: 7px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            margin: 2px;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-primary { background: #2e7d32; color: white; }
        .btn-primary:hover { background: #1b5e20; }
        .btn-warning { background: #f9a825; color: white; }
        .btn-warning:hover { background: #f57f17; }
        .btn-danger { background: #e53935; color: white; }
        .btn-danger:hover { background: #c62828; }
        .btn-toggle { padding: 7px 12px; border-radius: 6px; font-size: 12px; border: none; cursor: pointer; font-family: inherit; margin: 2px; }
        .btn-toggle-off { background: #e53935; color: white; }
        .btn-toggle-off:hover { background: #c62828; }
        .btn-toggle-on { background: #43a047; color: white; }
        .btn-toggle-on:hover { background: #2e7d32; }

        .badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            display: inline-block;
        }

        .badge-active { background: #c8e6c9; color: #2e7d32; }
        .badge-inactive { background: #ffcdd2; color: #c62828; }

        .empty {
            text-align: center;
            padding: 60px 20px;
            color: #90a4ae;
            background: white;
            border-radius: 12px;
        }

        @media (max-width: 700px) {
            th, td { padding: 10px 8px; font-size: 12px; }
            .btn { padding: 6px 10px; font-size: 11px; }
        }
    </style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
<link rel="stylesheet" href="/css/admin-master.css?v=2">
<link rel="stylesheet" href="{{ asset('css/admin-final-fix.css') }}?v=10">
<link rel="stylesheet" href="/css/admin-header-fix.css?v=1"></head>
<body>

<div class="header">
    <h1>روستاهای {{ $section->name }}</h1>
    <div class="nav">
        <a href="{{ route('admin.dashboard') }}">← بازگشت</a>
        <a href="/" target="_blank">🌐 سایت</a>
    </div>
</div>

<div class="container">

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    @php
        $activeCount   = $villages->where('is_active', true)->count();
        $inactiveCount = $villages->where('is_active', false)->count();
        $totalCount    = $villages->count();
    @endphp

    <div class="info-bar">
        <div>
            <h2 style="margin:0 0 6px;">{{ $section->name }}</h2>
            <span class="count">
                <span id="visibleCount">{{ $totalCount }}</span> روستا در این بخش
                —
                <strong style="color:#2e7d32;">{{ $activeCount }} فعال</strong>
                @if($inactiveCount > 0)
                    / <strong style="color:#c62828;">{{ $inactiveCount }} غیرفعال</strong>
                @endif
            </span>
        </div>
        <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            <input type="text" id="villageSearch" placeholder="جستجو در روستاها..." style="padding:10px 14px; border:1px solid #cfd8dc; border-radius:8px; font-family:inherit; font-size:13px; width:200px;" onkeyup="filterVillages()">

            <a href="{{ route('admin.villages.create', ['section_id' => $section->id]) }}" style="background:#2e7d32; color:white; text-decoration:none; padding:10px 20px; border-radius:8px; font-size:13px;">➕ افزودن روستای جدید</a>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         فعال/غیرفعال کردن گروهی — با یک دکمه
         ═══════════════════════════════════════════════════════════ --}}
    @if($totalCount > 0 && auth()->user()->isSuperAdmin())
    <div style="background:#ffffff; border-radius:12px; padding:16px 20px; margin-bottom:20px;
                box-shadow:0 5px 18px rgba(0,0,0,.05); border-right:4px solid #2e7d32;
                display:flex; justify-content:space-between; align-items:center;
                flex-wrap:wrap; gap:12px;">

        <div>
            <div style="font-size:14px; font-weight:bold; color:#2e7d32; margin-bottom:3px;">
                🔄 فعال / غیرفعال کردن گروهی
            </div>
            <div style="font-size:12px; color:#78909c;">
                با یک کلیک، وضعیت <strong>همه‌ی {{ $totalCount }} روستای</strong> این بخش تغییر می‌کند.
            </div>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            {{-- فعال‌سازی همه --}}
            <form method="POST"
                  action="{{ route('admin.sections.villages.bulk-toggle', $section->id) }}"
                  style="display:inline; margin:0;"
                  onsubmit="return confirm('⚠️ همه‌ی {{ $totalCount }} روستای بخش «{{ $section->name }}» فعال شوند؟\n\nسایت همه‌ی این روستاها در دسترس قرار می‌گیرد.');">
                @csrf
                <input type="hidden" name="state" value="active">
                <button type="submit"
                        style="background:#43a047; color:#fff; border:none; cursor:pointer;
                               padding:11px 20px; border-radius:9px; font-size:13.5px;
                               font-family:inherit; font-weight:bold; display:inline-flex;
                               align-items:center; gap:7px;"
                        @if($inactiveCount === 0) title="همه از قبل فعال هستند" @endif>
                    ✅ فعال‌سازی همه
                    @if($inactiveCount > 0)
                        <span style="background:rgba(255,255,255,.25); padding:1px 8px; border-radius:9px; font-size:11.5px;">
                            {{ $inactiveCount }}
                        </span>
                    @endif
                </button>
            </form>

            {{-- غیرفعال‌سازی همه --}}
            <form method="POST"
                  action="{{ route('admin.sections.villages.bulk-toggle', $section->id) }}"
                  style="display:inline; margin:0;"
                  onsubmit="return confirm('⚠️ همه‌ی {{ $totalCount }} روستای بخش «{{ $section->name }}» غیرفعال شوند؟\n\nسایت همه‌ی این روستاها برای شهروندان از دسترس خارج می‌شود.\n\nمطمئن هستید؟');">
                @csrf
                <input type="hidden" name="state" value="inactive">
                <button type="submit"
                        style="background:#e53935; color:#fff; border:none; cursor:pointer;
                               padding:11px 20px; border-radius:9px; font-size:13.5px;
                               font-family:inherit; font-weight:bold; display:inline-flex;
                               align-items:center; gap:7px;"
                        @if($activeCount === 0) title="همه از قبل غیرفعال هستند" @endif>
                    ⛔ غیرفعال‌سازی همه
                    @if($activeCount > 0)
                        <span style="background:rgba(255,255,255,.25); padding:1px 8px; border-radius:9px; font-size:11.5px;">
                            {{ $activeCount }}
                        </span>
                    @endif
                </button>
            </form>
        </div>
    </div>
    @endif

    @if($villages->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>نام روستا</th>
                    <th>دهستان</th>
                    <th>خانوار</th>
                    <th>جمعیت</th>
                    <th>دهیار</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($villages as $i => $village)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $village->name }}</strong></td>
                        <td>{{ $village->district ?? '—' }}</td>
                        <td>{{ $village->households ? number_format($village->households) : '—' }}</td>
                        <td>{{ $village->population ? number_format($village->population) : '—' }}</td>
                        <td>{{ $village->dehyar_name ?? '—' }}</td>
                        <td>
                            @if($village->is_active)
                                <span class="badge badge-active">فعال</span>
                            @else
                                <span class="badge badge-inactive">غیرفعال</span>
                            @endif
                        </td>
                        <td>
                            @if(auth()->user()->isSuperAdmin()) <a href="{{ route('admin.village.dashboard', $village->id) }}" class="btn" style="background:#2e7d32;color:white;" title="پنل روستا">⚙️ پنل</a> @endif
                            <a href="{{ route('admin.villages.edit', $village->id) }}" class="btn btn-primary" title="ویرایش">✏️ ویرایش</a>
                            <a href="{{ route('admin.village.images.index', $village->id) }}" class="btn" style="background:#7b1fa2; color:white;" title="گالری">🖼 گالری</a>
                            <a href="{{ route('admin.village.news.index', $village->id) }}" class="btn btn-warning" title="اخبار">📰 اخبار</a>
                            @if(auth()->user()->isSuperAdmin())
                            <form method="POST" action="{{ route('admin.villages.toggle', $village->id) }}" style="display:inline;">
                                @csrf
                                @if($village->is_active)
                                    <button type="submit" class="btn-toggle btn-toggle-off" onclick="return confirm('روستای «{{ $village->name }}» را غیرفعال کنم؟')" title="غیرفعال کردن">🔒</button>
                                @else
                                    <button type="submit" class="btn-toggle btn-toggle-on" title="فعال کردن">🔓</button>
                                @endif
                            </form>
                            @endif
                            <form method="POST" action="{{ route('admin.villages.destroy', $village->id) }}" style="display:inline;" onsubmit="return confirm('حذف {{ $village->name }}؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" title="حذف">🗑 حذف</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty">
            <p>هنوز روستایی برای این بخش ثبت نشده است.</p>
        </div>
    @endif

</div>


<script>
function filterVillages() {
    const q = document.getElementById('villageSearch').value.trim();
    const rows = document.querySelectorAll('table tbody tr');
    let visible = 0;
    
    rows.forEach(row => {
        const text = row.textContent || '';
        if (text.includes(q)) {
            row.style.display = '';
            visible++;
        } else {
            row.style.display = 'none';
        }
    });
    
    document.getElementById('visibleCount').textContent = visible;
}
</script>

</body>
</html>

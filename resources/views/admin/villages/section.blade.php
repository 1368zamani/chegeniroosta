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
            background: linear-gradient(135deg, #1565c0, #0d47a1);
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

        .info-bar h2 { margin: 0; color: #1565c0; font-size: 18px; }
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

        .btn-primary { background: #1565c0; color: white; }
        .btn-primary:hover { background: #0d47a1; }
        .btn-warning { background: #f9a825; color: white; }
        .btn-warning:hover { background: #f57f17; }
        .btn-danger { background: #e53935; color: white; }
        .btn-danger:hover { background: #c62828; }

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
</head>
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

    <div class="info-bar">
        <div>
            <h2 style="margin:0 0 4px;">{{ $section->name }}</h2>
            <span class="count"><span id="visibleCount">{{ $villages->count() }}</span> روستا در این بخش</span>
        </div>
        <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            <input type="text" id="villageSearch" placeholder="جستجو در روستاها..." style="padding:10px 14px; border:1px solid #cfd8dc; border-radius:8px; font-family:inherit; font-size:13px; width:200px;" onkeyup="filterVillages()">
            <a href="{{ route('admin.villages.create', ['section_id' => $section->id]) }}" style="background:#1565c0; color:white; text-decoration:none; padding:10px 20px; border-radius:8px; font-size:13px;">➕ افزودن روستای جدید</a>
        </div>
    </div>

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
                            <a href="{{ route('admin.village.dashboard', $village->id) }}" class="btn" style="background:#1565c0;color:white;" title="پنل روستا">⚙️ پنل</a>
                            <a href="{{ route('admin.villages.edit', $village->id) }}" class="btn btn-primary" title="ویرایش">✏️ ویرایش</a>
                            <a href="{{ route('admin.village.images.index', $village->id) }}" class="btn" style="background:#7b1fa2; color:white;" title="گالری">🖼 گالری</a>
                            <a href="{{ route('admin.village.news.index', $village->id) }}" class="btn btn-warning" title="اخبار">📰 اخبار</a>
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

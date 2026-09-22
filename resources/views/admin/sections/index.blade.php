<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مدیریت بخش‌ها</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #f5f7fa; color: #263238; }
        .header { background: #1565c0; color: white; padding: 20px; }
        .header h1 { margin: 0; font-size: 20px; }
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        .nav-links { margin-bottom: 20px; }
        .nav-links a { display: inline-block; padding: 10px 18px; background: white; color: #1565c0; text-decoration: none; border-radius: 8px; margin-right: 10px; font-size: 14px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); }
        .nav-links a:hover { background: #1565c0; color: white; }
        .success { background: #c8e6c9; color: #2e7d32; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; }
        table { width: 100%; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 6px 20px rgba(0,0,0,0.06); border-collapse: collapse; }
        th, td { padding: 14px 16px; text-align: right; border-bottom: 1px solid #eceff1; font-size: 14px; }
        th { background: #f8fafc; color: #546e7a; font-weight: 600; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: #f8fafc; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; }
        .badge-active { background: #c8e6c9; color: #2e7d32; }
        .badge-inactive { background: #ffcdd2; color: #c62828; }
        .btn { display: inline-block; padding: 8px 16px; background: #1565c0; color: white; text-decoration: none; border-radius: 6px; font-size: 13px; border: none; cursor: pointer; font-family: inherit; }
        .btn:hover { background: #0d47a1; }
        .btn-toggle { padding: 8px 14px; font-size: 13px; border: none; cursor: pointer; font-family: inherit; border-radius: 6px; margin-right: 4px; }
        .btn-toggle-off { background: #e53935; color: white; }
        .btn-toggle-off:hover { background: #c62828; }
        .btn-toggle-on { background: #43a047; color: white; }
        .btn-toggle-on:hover { background: #2e7d32; }
        .photo-thumb { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 2px solid #e3f2fd; }
        .photo-empty { width: 44px; height: 44px; border-radius: 50%; background: #cfd8dc; display: inline-flex; align-items: center; justify-content: center; color: #90a4ae; font-size: 18px; }
    </style>
</head>
<body>

<div class="header">
    <h1>پنل مدیریت — بخش‌ها</h1>
</div>

<div class="container">

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <div class="nav-links">
        <a href="{{ route('admin.dashboard') }}">← داشبورد</a>
        <a href="/">مشاهده سایت</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>عکس بخشدار</th>
                <th>نام بخش</th>
                <th>Subdomain</th>
                <th>نام بخشدار</th>
                <th>تعداد روستا</th>
                <th>وضعیت</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sections as $section)
                <tr>
                    <td>
                        @if($section->bakhshdar_photo)
                            <img src="{{ asset('storage/' . $section->bakhshdar_photo) }}" class="photo-thumb" alt="">
                        @else
                            <div class="photo-empty">👤</div>
                        @endif
                    </td>
                    <td>{{ $section->name }}</td>
                    <td>{{ $section->subdomain ?? '-' }}</td>
                    <td>{{ $section->bakhshdar_name ?? '—' }}</td>
                    <td>{{ $section->villages()->count() }}</td>
                    <td>
                        @if($section->is_active)
                            <span class="badge badge-active">فعال</span>
                        @else
                            <span class="badge badge-inactive">غیرفعال</span>
                        @endif
                    </td>
                    <td style="white-space: nowrap;">
                        <a href="{{ route('admin.sections.edit', $section->id) }}" class="btn">ویرایش</a>
                        <form method="POST" action="{{ route('admin.sections.toggle', $section->id) }}" style="display:inline;">
                            @csrf
                            @if($section->is_active)
                                <button type="submit" class="btn-toggle btn-toggle-off" onclick="return confirm('بخش «{{ $section->name }}» را غیرفعال کنم؟ سایت این بخش بسته می‌شود.')" title="غیرفعال کردن">🔒 غیرفعال</button>
                            @else
                                <button type="submit" class="btn-toggle btn-toggle-on" onclick="return confirm('بخش «{{ $section->name }}» را فعال کنم؟')" title="فعال کردن">🔓 فعال</button>
                            @endif
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

</body>
</html>

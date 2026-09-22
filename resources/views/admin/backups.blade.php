<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>بکاپ‌ها</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #f5f7fa; color: #263238; }
        .header { background: linear-gradient(135deg, #0d2e4f, #103d68); color: white; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .header h1 { margin: 0; font-size: 19px; }
        .header a { color: white; text-decoration: none; padding: 8px 16px; background: rgba(255,255,255,0.15); border-radius: 8px; font-size: 13px; }
        .container { max-width: 1000px; margin: auto; padding: 30px 20px; }
        .success { background: #c8e6c9; color: #2e7d32; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; white-space: pre-line; font-size: 13px; }
        .card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 5px 18px rgba(0,0,0,0.06); margin-bottom: 20px; }
        .btn { display: inline-block; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-family: inherit; font-size: 14px; cursor: pointer; border: none; }
        .btn-primary { background: #0d2e4f; color: white; }
        .btn-primary:hover { background: #1565c0; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; }
        th, td { padding: 12px 14px; text-align: right; border-bottom: 1px solid #eceff1; font-size: 13px; }
        th { background: #f8fafc; color: #546e7a; font-weight: 600; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: #f8fafc; }
        .empty { text-align: center; padding: 40px; color: #90a4ae; }
    </style>
</head>
<body>

<div class="header">
    <h1>💾 بکاپ‌های خودکار</h1>
    <a href="{{ route('admin.dashboard') }}">← بازگشت</a>
</div>

<div class="container">

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <div class="card" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
            <h3 style="margin:0 0 4px; color:#0d2e4f; font-size:15px;">بکاپ دستی</h3>
            <p style="margin:0; color:#78909c; font-size:12.5px;">بکاپ فوری از دیتابیس و فایل‌های آپلود</p>
        </div>
        <form method="POST" action="{{ route('admin.backups.run') }}">
            @csrf
            <button type="submit" class="btn btn-primary">🚀 اجرای بکاپ</button>
        </form>
    </div>

    <h2 style="color:#0d2e4f; font-size:16px; margin-bottom:14px;">📁 لیست بکاپ‌ها ({{ count($files) }})</h2>

    @if(count($files) > 0)
        <table>
            <thead>
                <tr>
                    <th>نام فایل</th>
                    <th>حجم</th>
                    <th>تاریخ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($files as $f)
                    <tr>
                        <td style="direction:ltr; font-family:monospace; font-size:12px;">{{ $f['name'] }}</td>
                        <td>
                            @if($f['size'] >= 1048576)
                                {{ round($f['size'] / 1048576, 2) }} MB
                            @elseif($f['size'] >= 1024)
                                {{ round($f['size'] / 1024, 2) }} KB
                            @else
                                {{ $f['size'] }} B
                            @endif
                        </td>
                        <td>{{ date('Y/m/d H:i', $f['date']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty card">
            هنوز بکاپی ساخته نشده است. روی «اجرای بکاپ» کلیک کن.
        </div>
    @endif

    <div class="card" style="margin-top:20px;">
        <h3 style="margin:0 0 10px; color:#0d2e4f; font-size:14px;">🕐 بکاپ خودکار روزانه</h3>
        <p style="margin:0; color:#546e7a; font-size:13px; line-height:2;">
            برای فعال‌سازی، این دستور رو در سرور اجرا کن:<br>
            <code style="background:#f5f7fa; padding:6px 12px; border-radius:6px; direction:ltr; display:inline-block; margin-top:6px;">crontab -e</code><br>
            و این خط رو اضافه کن:<br>
            <code style="background:#f5f7fa; padding:6px 12px; border-radius:6px; direction:ltr; display:inline-block; margin-top:6px;">0 3 * * * cd /var/www/chegeniroosta && php artisan app:backup >> /dev/null 2>&1</code><br>
            <small style="color:#90a4ae;">یعنی هر شب ساعت ۳ بامداد بکاپ خودکار گرفته می‌شود.</small>
        </p>
    </div>

</div>

</body>
</html>

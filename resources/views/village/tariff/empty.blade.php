<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>تعرفه عوارض | {{ $village->name }}</title>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
<style>
body { font-family: 'Vazirmatn', Tahoma; background: #f5f9ff; padding: 40px; text-align: center; color: #1565c0; }
.card { max-width: 600px; margin: 40px auto; background: #fff; padding: 40px; border-radius: 14px; border-top: 4px solid #1565c0; }
h1 { color: #0d47a1; }
</style>
</head>
<body>
<div class="card">
    <div style="font-size: 60px">📋</div>
    <h1>تعرفه {{ $village->name }} منتشر نشده است</h1>
    <p>تعرفه عوارض و بهای خدمات این روستا هنوز توسط شورای اسلامی روستا تصویب و اعلام عمومی نشده است.</p>
    <a href="/village/{{ $village->slug }}" style="display:inline-block; padding:10px 20px; background:#1565c0; color:#fff; border-radius:9px; text-decoration:none; font-weight:700; margin-top:14px">بازگشت به سایت روستا</a>
</div>
</body>
</html>

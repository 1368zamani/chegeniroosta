<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ورود کارشناس مالی و عمرانی | روستا نما</title>
<style>
    *{box-sizing:border-box}
    body{font-family:Tahoma,sans-serif;background:linear-gradient(135deg,#1e5b9c,#2470b8);margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:16px;color:#1e3a5f}
    .card{max-width:420px;width:100%;background:#fff;border-radius:16px;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.25)}
    .icon{font-size:42px;text-align:center;margin-bottom:8px}
    h1{font-size:18px;color:#1565c0;margin:0 0 6px;text-align:center}
    .sub{text-align:center;color:#607d8b;font-size:13px;margin-bottom:22px}
    label{display:block;margin:14px 0 6px;font-size:13.5px;font-weight:bold;color:#37474f}
    input{width:100%;padding:12px 14px;border:1px solid #cfd8dc;border-radius:10px;font-family:inherit;font-size:14px;background:#fbfdff}
    input:focus{outline:none;border-color:#1565c0;background:#fff}
    .btn{display:block;width:100%;margin-top:20px;padding:13px;background:#1565c0;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:bold;cursor:pointer;font-family:inherit}
    .btn:hover{background:#0d47a1}
    .err{background:#ffebee;color:#c62828;padding:10px 13px;border-radius:9px;margin-bottom:14px;font-size:13px}
    .note{background:#fff8e1;border-right:4px solid #f9a825;padding:10px 14px;border-radius:8px;font-size:12.5px;color:#5d4037;margin-top:16px}
    .back{text-align:center;margin-top:14px;font-size:12.5px;color:#607d8b}
    .back a{color:#1565c0;text-decoration:none}
</style>
</head>
<body>
<div class="card">
    <div class="icon">💰</div>
    <h1>ورود کارشناس مالی و عمرانی بخشداری</h1>
    <div class="sub">دسترسی اختصاصی به پنل عوارض و بهای خدمات</div>

    @if($errors->any())
        <div class="err">
            @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('fees.login.submit') }}">
        @csrf
        <label>ایمیل</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus dir="ltr">

        <label>رمز عبور</label>
        <input type="password" name="password" required dir="ltr">

        <label style="display:flex;align-items:center;gap:8px;margin-top:12px;font-weight:normal">
            <input type="checkbox" name="remember" value="1" style="width:auto;margin:0">
            <span>مرا به خاطر بسپار</span>
        </label>

        <button class="btn" type="submit">ورود به پنل عوارض</button>
    </form>

    <div class="note">
        ⚠️ این صفحه مخصوص <b>کارشناس مالی و عمرانی بخشداری</b> است. اگر دهیار یا بخشدار هستید، از
        <a href="{{ route('login') }}">صفحه ورود اصلی</a> وارد شوید.
    </div>

    <div class="back">
        <a href="/">← بازگشت به سایت</a>
    </div>
</div>
</body>
</html>

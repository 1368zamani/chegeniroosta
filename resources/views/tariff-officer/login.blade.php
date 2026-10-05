<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ورود کارشناسان | پنل عوارض</title>
<style>
* { box-sizing: border-box; }
body {
    margin: 0;
    font-family: Tahoma, Arial, sans-serif;
    background: linear-gradient(135deg, #1e5b9c 0%, #2470b8 60%, #2e8b57 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.login-box {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.3);
    width: 100%;
    max-width: 440px;
    padding: 40px 34px;
    animation: boxIn .35s cubic-bezier(0.2,0.9,0.3,1.2);
}
@keyframes boxIn {
    from { opacity: 0; transform: translateY(20px) scale(0.96); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
.icon { text-align: center; font-size: 56px; margin-bottom: 12px; }
h1 { text-align: center; color: #1e5b9c; margin: 0 0 6px; font-size: 20px; }
.subtitle { text-align: center; color: #78909c; font-size: 13px; margin-bottom: 28px; line-height: 1.7; }
label { display: block; color: #37474f; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
input[type=text], input[type=password] {
    width: 100%;
    padding: 12px 14px;
    border: 2px solid #e3f2fd;
    border-radius: 10px;
    font-family: inherit;
    font-size: 14px;
    transition: all .2s;
}
input[type=text]:focus, input[type=password]:focus {
    outline: none;
    border-color: #1e5b9c;
    box-shadow: 0 0 0 4px rgba(30,91,156,0.1);
}
.form-group { margin-bottom: 16px; }
.remember { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #546e7a; margin-bottom: 20px; cursor: pointer; }
.remember input { margin: 0; cursor: pointer; }
.btn-login {
    width: 100%;
    background: linear-gradient(135deg, #1e5b9c, #2470b8);
    color: white;
    border: none;
    padding: 14px;
    border-radius: 10px;
    font-family: inherit;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all .2s;
}
.btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(30,91,156,0.35);
}
.btn-login:disabled { opacity: 0.6; cursor: not-allowed; }
.error-box {
    background: #fee2e2;
    border-right: 4px solid #dc2626;
    color: #991b1b;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 13px;
    margin-bottom: 18px;
    line-height: 1.8;
}
.error-box ul { margin: 0; padding-right: 20px; }
.notice {
    background: #fef3c7;
    border-right: 4px solid #d97706;
    color: #78350f;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 12.5px;
    line-height: 1.9;
    margin-top: 20px;
}
.back-link { text-align: center; margin-top: 22px; }
.back-link a { color: #78909c; text-decoration: none; font-size: 13px; }
.back-link a:hover { color: #1e5b9c; }
</style>
</head>
<body>

<div class="login-box">
    <div class="icon">🧾</div>
    <h1>ورود کارشناسان فنی و مالی</h1>
    <p class="subtitle">
        پنل مدیریت تعرفه‌ی عوارض و بهای خدمات دهیاری‌ها<br>
        مخصوص کارشناسان بخشداری
    </p>

    @if($errors->any())
        <div class="error-box">
            <ul>
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('tariff.officer.login.post') }}">
        @csrf
        <div class="form-group">
            <label for="identifier">نام کاربری یا ایمیل</label>
            <input type="text" id="identifier" name="identifier"
                   value="{{ old('identifier') }}" autofocus required>
        </div>
        <div class="form-group">
            <label for="password">رمز عبور</label>
            <input type="password" id="password" name="password" required>
        </div>
        <label class="remember">
            <input type="checkbox" name="remember" value="1">
            <span>مرا به خاطر بسپار</span>
        </label>
        <button type="submit" class="btn-login">ورود به پنل عوارض ←</button>
    </form>

    <div class="notice">
        ⚠️ <strong>توجه:</strong> این پنل مخصوص کارشناسان فنی و مالی دهیاری‌ها است.
        پس از ورود، فقط به تعرفه‌ی روستاهای <strong>بخش خودتان</strong> دسترسی خواهید داشت.
    </div>

    <div class="back-link">
        <a href="{{ url('/') }}">← بازگشت به سایت بخشداری</a>
    </div>
</div>

</body>
</html>

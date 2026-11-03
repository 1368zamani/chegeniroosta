<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود کارشناسان — چگنی روستا</title>
    {{-- Bootstrap و آیکون‌ها محلی (قبلاً از jsDelivr) --}}
    <link href="{{ asset('vendor/bootstrap/bootstrap.rtl.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: 'Vazirmatn', 'Tahoma', sans-serif;
            background: linear-gradient(135deg, #2e7d32 0%, #0d3b2e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 440px;
            width: 100%;
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #2e7d32, #0d3b2e);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-header i { font-size: 3rem; margin-bottom: 1rem; }
        .login-body { padding: 2rem; }
        .form-control {
            padding: 0.8rem 1rem;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
        }
        .form-control:focus {
            border-color: #2e7d32;
            box-shadow: 0 0 0 3px rgba(26,84,144,0.1);
        }
        .btn-login {
            background: linear-gradient(135deg, #2e7d32, #0d3b2e);
            border: none;
            padding: 0.9rem;
            border-radius: 10px;
            font-weight: 600;
            color: white;
            width: 100%;
        }
        .btn-login:hover { opacity: 0.95; color: white; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <i class="bi bi-shield-lock"></i>
            <h4 class="mb-1">پنل کارشناسان فنی و مالی</h4>
            <small class="opacity-75">سامانه تعرفه‌های دهیاری</small>
        </div>
        <div class="login-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('expert.login.post') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">نام کاربری یا ایمیل</label>
                    <input type="text" name="identifier" class="form-control"
                           value="{{ old('identifier') }}" required autofocus
                           placeholder="مثال: expert@chegeniroosta.ir">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">رمز عبور</label>
                    <input type="password" name="password" class="form-control" required
                           placeholder="رمز عبور خود را وارد کنید">
                </div>
                <button type="submit" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-left"></i> ورود به پنل کارشناسان
                </button>
            </form>

            <hr class="my-4">
            <p class="text-center text-muted small mb-0">
                ورود فقط برای کارشناسان فنی و مالی مجاز می‌باشد.
            </p>
        </div>
    </div>
</body>
</html>

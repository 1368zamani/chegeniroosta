<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ورود | سامانه چه‌گنی روستا</title>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Vazirmatn', Tahoma, sans-serif;
            background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            color: #1f2937;
        }
        .card {
            background: #fff;
            width: 100%;
            max-width: 420px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
            padding: 2.25rem 2rem;
        }
        .brand {
            text-align: center;
            margin-bottom: 1.75rem;
        }
        .brand h1 {
            font-size: 1.4rem;
            color: #0f766e;
            margin-bottom: .35rem;
        }
        .brand p {
            color: #6b7280;
            font-size: .9rem;
        }
        .field { margin-bottom: 1.1rem; }
        label {
            display: block;
            font-size: .9rem;
            margin-bottom: .4rem;
            color: #374151;
            font-weight: 500;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: .75rem .9rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            transition: border .15s, box-shadow .15s;
            background: #f9fafb;
        }
        input:focus {
            outline: none;
            border-color: #0f766e;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(15,118,110,.15);
        }
        .remember {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-size: .875rem;
            color: #4b5563;
            margin-bottom: 1.25rem;
        }
        button {
            width: 100%;
            padding: .85rem;
            background: #0f766e;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
        }
        button:hover { background: #115e59; }
        .errors {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            border-radius: 8px;
            padding: .75rem .9rem;
            margin-bottom: 1rem;
            font-size: .875rem;
        }
        .errors ul { list-style: none; }
        .errors li + li { margin-top: .25rem; }
        .footer {
            text-align: center;
            margin-top: 1.25rem;
            font-size: .8rem;
            color: #9ca3af;
        }
    </style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
</head>
<body>
    <div class="card">
        <div class="brand">
            <h1>سامانه مدیریت روستا</h1>
            <p>چه‌گنی روستا — ورود به پنل مدیریت</p>
        </div>

        @if ($errors->any())
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf

            <div class="field">
                <label for="email">ایمیل</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    dir="ltr"
                >
            </div>

            <div class="field">
                <label for="password">رمز عبور</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    dir="ltr"
                >
            </div>

            <label class="remember">
                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                <span>مرا به خاطر بسپار</span>
            </label>

            <button type="submit">ورود</button>
        </form>

        <div class="footer">
            © {{ date('Y') }} چه‌گنی روستا
        </div>
    </div>
</body>
</html>

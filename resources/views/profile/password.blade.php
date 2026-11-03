<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تغییر رمز عبور | سامانه اطلاع‌رسانی چگنی</title>

    <link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}?v=1">

    @php
        $user = auth()->user();
        $backUrl = url('/admin');
        if ($user && $user->role === 'village_admin' && $user->village_id) {
            $backUrl = route('admin.village.dashboard', $user->village_id);
        } elseif ($user && $user->role === 'super_admin') {
            $backUrl = route('admin.dashboard');
        }
        $roleLabel = match ($user->role ?? '') {
            'super_admin'   => 'مدیر کل سامانه',
            'section_admin' => 'مدیر بخش',
            'village_admin' => 'مدیر روستا',
            'fee_officer'   => 'کارشناس',
            'tariff_officer'=> 'مسئول تعرفه',
            default         => 'کاربر',
        };
    @endphp

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Vazirmatn', Tahoma, Arial, sans-serif;
            background: linear-gradient(160deg, #eef5ef 0%, #f5f7fa 60%);
            color: #263238;
            min-height: 100vh;
        }

        .wrap {
            width: min(520px, 94%);
            margin: 34px auto 60px;
        }

        .head {
            text-align: center;
            margin-bottom: 22px;
        }
        .head .icon {
            width: 68px; height: 68px; margin: 0 auto 12px;
            background: linear-gradient(135deg, #2e7d32, #1b5e20);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 32px;
            box-shadow: 0 8px 24px rgba(46,125,50,.28);
        }
        .head h1 {
            margin: 0 0 6px;
            font-size: 21px;
            color: #1b5e20;
            font-weight: 800;
        }
        .head p {
            margin: 0;
            font-size: 12.5px;
            color: #78909c;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            padding: 26px 24px;
            box-shadow: 0 10px 40px rgba(13,46,79,.08);
            border: 1px solid #eef2ef;
        }

        .who {
            display: flex; align-items: center; gap: 12px;
            background: #f1f8e9;
            border-radius: 11px;
            padding: 12px 14px;
            margin-bottom: 20px;
        }
        .who .avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: #c8e6c9; color: #1b5e20;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; font-weight: 800; flex-shrink: 0;
        }
        .who .info { flex: 1; min-width: 0; }
        .who .name { font-size: 13.5px; font-weight: 700; color: #1b5e20; }
        .who .role { font-size: 11.5px; color: #78909c; margin-top: 2px; }

        .alert {
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 12.5px;
            line-height: 1.9;
            margin-bottom: 16px;
        }
        .alert.success {
            background: #e8f5e9;
            color: #2e7d32;
            border-right: 4px solid #2e7d32;
        }
        .alert.error {
            background: #ffebee;
            color: #c62828;
            border-right: 4px solid #c62828;
        }

        .field { margin-bottom: 16px; }

        label {
            display: block;
            font-size: 12.5px;
            font-weight: 700;
            color: #37474f;
            margin-bottom: 6px;
        }

        .input-wrap { position: relative; }

        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 12px 44px 12px 14px;
            border: 1.5px solid #d7dee3;
            border-radius: 9px;
            font-family: inherit;
            font-size: 14px;
            transition: border-color .2s, box-shadow .2s;
            direction: ltr;
            text-align: left;
        }
        input:focus {
            outline: none;
            border-color: #2e7d32;
            box-shadow: 0 0 0 3px rgba(46,125,50,.12);
        }
        input.is-invalid { border-color: #e53935; }

        .toggle {
            position: absolute;
            left: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            padding: 4px 6px;
            color: #90a4ae;
            line-height: 1;
        }

        .hint {
            font-size: 11.5px;
            color: #90a4ae;
            margin-top: 5px;
            line-height: 1.8;
        }

        /* ── نوار قدرت رمز ── */
        .strength {
            margin-top: 8px;
            display: none;
        }
        .strength.is-shown { display: block; }
        .strength-bar {
            height: 5px;
            background: #eceff1;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 5px;
        }
        .strength-fill {
            height: 100%;
            width: 0;
            border-radius: 3px;
            transition: width .28s ease, background-color .28s ease;
        }
        .strength-text {
            font-size: 11px;
            color: #90a4ae;
        }

        .error-text {
            font-size: 11.5px;
            color: #e53935;
            margin-top: 5px;
        }

        .submit {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #2e7d32, #1b5e20);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: inherit;
            font-size: 14.5px;
            font-weight: 800;
            cursor: pointer;
            margin-top: 6px;
            box-shadow: 0 5px 18px rgba(46,125,50,.28);
            transition: transform .18s, box-shadow .18s;
        }
        .submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(46,125,50,.34);
        }
        .submit:active { transform: translateY(0); }

        .back {
            display: block;
            text-align: center;
            margin-top: 16px;
            font-size: 12.5px;
            color: #2e7d32;
            text-decoration: none;
        }
        .back:hover { text-decoration: underline; }

        .security-note {
            background: #f5f7fa;
            border-radius: 10px;
            padding: 12px 14px;
            margin-top: 18px;
            font-size: 11.5px;
            color: #607d8b;
            line-height: 1.9;
        }
        .security-note strong { color: #37474f; }

        @media (max-width: 560px) {
            .wrap { margin: 18px auto 40px; }
            .card { padding: 20px 16px; }
            .head h1 { font-size: 18px; }
        }

        @media (prefers-reduced-motion: reduce) {
            * { transition: none !important; }
        }
    </style>
</head>
<body>

<div class="wrap">

    <div class="head">
        <div class="icon">🔑</div>
        <h1>تغییر رمز عبور</h1>
        <p>برای امنیت حساب خود، رمزی قوی انتخاب کنید</p>
    </div>

    <div class="card">

        <div class="who">
            <div class="avatar">{{ mb_substr($user->name ?? 'ک', 0, 1) }}</div>
            <div class="info">
                <div class="name">{{ $user->name ?? 'کاربر' }}</div>
                <div class="role">{{ $roleLabel }}</div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert success">✅ {{ session('success') }}</div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert error">
                @foreach($errors->all() as $e)
                    <div>❌ {{ $e }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('profile.password.update') }}" autocomplete="off">
            @csrf

            <div class="field">
                <label for="current_password">رمز عبور فعلی</label>
                <div class="input-wrap">
                    <input type="password" name="current_password" id="current_password"
                           required autocomplete="current-password"
                           class="{{ $errors->has('current_password') ? 'is-invalid' : '' }}">
                    <button type="button" class="toggle" onclick="toggleField('current_password', this)" tabindex="-1">👁</button>
                </div>
                @error('current_password')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password">رمز عبور جدید</label>
                <div class="input-wrap">
                    <input type="password" name="password" id="password"
                           required autocomplete="new-password"
                           class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                           oninput="checkStrength(this.value)">
                    <button type="button" class="toggle" onclick="toggleField('password', this)" tabindex="-1">👁</button>
                </div>
                <div class="strength" id="strength">
                    <div class="strength-bar">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <div class="strength-text" id="strengthText"></div>
                </div>
                <div class="hint">
                    {{ \App\Support\PasswordPolicy::hint() }}
                </div>
                @error('password')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password_confirmation">تکرار رمز عبور جدید</label>
                <div class="input-wrap">
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           required autocomplete="new-password">
                    <button type="button" class="toggle" onclick="toggleField('password_confirmation', this)" tabindex="-1">👁</button>
                </div>
            </div>

            <button type="submit" class="submit">💾 ذخیره‌ی رمز جدید</button>
        </form>

        <a href="{{ $backUrl }}" class="back">← بازگشت به پنل</a>

        <div class="security-note">
            <strong>🛡 نکات امنیتی:</strong><br>
            • رمز نباید با رمز قبلی یکسان باشد<br>
            • رمز را با کسی به اشتراک نگذارید<br>
            • پس از تغییر، از دستگاه‌های دیگر خارج می‌شوید
        </div>
    </div>

</div>

<script>
function toggleField(id, btn) {
    var el = document.getElementById(id);
    if (!el) return;
    var show = el.type === 'password';
    el.type = show ? 'text' : 'password';
    btn.textContent = show ? '🙈' : '👁';
}

function checkStrength(value) {
    var box  = document.getElementById('strength');
    var fill = document.getElementById('strengthFill');
    var text = document.getElementById('strengthText');
    if (!box || !fill || !text) return;

    if (!value) {
        box.classList.remove('is-shown');
        return;
    }
    box.classList.add('is-shown');

    var score = 0;
    if (value.length >= 8)  score++;
    if (value.length >= 12) score++;
    if (/[\p{L}]/u.test(value)) score++;
    if (/[0-9\u06F0-\u06F9]/.test(value)) score++;
    if (/[^A-Za-z0-9\u0600-\u06FF\u06F0-\u06F9]/.test(value)) score++;

    var pct, color, label;
    if (score <= 2)      { pct = 30;  color = '#e53935'; label = 'ضعیف'; }
    else if (score === 3){ pct = 60;  color = '#f9a825'; label = 'متوسط'; }
    else if (score === 4){ pct = 82;  color = '#2e7d32'; label = 'خوب'; }
    else                 { pct = 100; color = '#1b5e20'; label = 'قوی'; }

    fill.style.width = pct + '%';
    fill.style.backgroundColor = color;
    text.textContent = 'قدرت رمز: ' + label;
    text.style.color = color;
}
</script>

</body>
</html>

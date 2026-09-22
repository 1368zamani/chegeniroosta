<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صفحه یافت نشد | 404</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Tahoma, Arial, sans-serif;
            background: linear-gradient(135deg, #0d2e4f, #1565c0);
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .err-box {
            text-align: center;
            max-width: 550px;
        }
        .err-code {
            font-size: 130px;
            font-weight: 900;
            color: #ffd54f;
            line-height: 1;
            margin: 0;
            text-shadow: 0 8px 30px rgba(0,0,0,0.5);
        }
        .err-title {
            font-size: 26px;
            margin: 16px 0 12px;
            font-weight: 800;
        }
        .err-desc {
            font-size: 15px;
            opacity: 0.9;
            line-height: 2;
            margin: 0 0 30px;
        }
        .err-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .err-btn {
            display: inline-block;
            padding: 12px 26px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.2s;
        }
        .err-btn-primary {
            background: #ffd54f;
            color: #0d2e4f;
        }
        .err-btn-primary:hover {
            background: #ffb300;
            transform: translateY(-2px);
        }
        .err-btn-secondary {
            background: rgba(255,255,255,0.15);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
        }
        .err-btn-secondary:hover {
            background: rgba(255,255,255,0.25);
        }
        .err-icon {
            font-size: 60px;
            margin-bottom: 10px;
            opacity: 0.4;
        }
        @media (max-width: 600px) {
            .err-code { font-size: 90px; }
            .err-title { font-size: 20px; }
            .err-desc { font-size: 13px; }
        }
    </style>
</head>
<body>

<div class="err-box">
    <div class="err-icon">🔍</div>
    <h1 class="err-code">404</h1>
    <h2 class="err-title">صفحه مورد نظر پیدا نشد</h2>
    <p class="err-desc">
        متأسفانه صفحه‌ای که به دنبال آن هستید وجود ندارد یا منتقل شده است.
        لطفاً به صفحه اصلی برگردید.
    </p>
    <div class="err-actions">
        <a href="/" class="err-btn err-btn-primary">🏠 بازگشت به صفحه اصلی</a>
        <a href="javascript:history.back()" class="err-btn err-btn-secondary">← بازگشت به صفحه قبل</a>
    </div>
</div>

</body>
</html>

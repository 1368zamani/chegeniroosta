<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>بخشداران شهرستان چگنی</title>
<style>
* { box-sizing: border-box; }
body { margin: 0; font-family: 'Vazirmatn', Tahoma, Arial, sans-serif; background: #fff7e9; color: #2b2119; }
.wrap { max-width: 1100px; margin: 40px auto; padding: 0 15px; }
.page-title { font-size: 26px; color: #b3401f; margin: 0 0 25px; padding-bottom: 15px; border-bottom: 3px solid #f5b841; }
.grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px; }
.card { background: white; border-radius: 14px; padding: 25px 20px; text-align: center; box-shadow: 0 4px 18px rgba(0,0,0,.06); text-decoration: none; color: inherit; border-top: 4px solid #c94a2b; transition: transform .25s, box-shadow .25s; }
.card:hover { transform: translateY(-5px); box-shadow: 0 14px 35px rgba(201,74,43,.15); }
.card .photo { width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 4px solid #fdeee7; margin: 0 auto 15px; display: block; }
.card .photo-ph { width: 110px; height: 110px; border-radius: 50%; background: linear-gradient(135deg, #fdeee7, #f7d9cc); display: flex; align-items: center; justify-content: center; font-size: 50px; color: #a2948a; margin: 0 auto 15px; border: 4px solid #fdeee7; }
.card h4 { margin: 0 0 6px; color: #b3401f; font-size: 18px; font-weight: 800; }
.card .role { color: #6b5a4a; font-size: 13px; margin-bottom: 6px; }
.card .bakhshdar-name { color: #c94a2b; font-size: 14px; font-weight: 600; margin-bottom: 12px; }
.card .btn { display: inline-block; margin-top: 8px; padding: 8px 20px; background: #c94a2b; color: white; border-radius: 6px; font-size: 13px; font-weight: 600; }
.empty-box { background: white; padding: 60px 20px; text-align: center; border-radius: 14px; color: #a2948a; }
@media (max-width: 700px) { .grid { grid-template-columns: 1fr; } }
</style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ui-v3.css') }}?v=5">
</head>
<body class="v3-body">

@include('partials.gov-header', ['headerTitle' => 'بخشداران شهرستان چگنی'])
@include('partials.v3-crumbs', ['crumbs' => [
    ['label' => 'پرتال چگنی', 'url' => url('/')],
    ['label' => 'مسئولان شهرستان', 'url' => url('/officials/governor')],
    ['label' => 'بخشداران'],
]])

<div class="wrap">
    <h1 class="page-title">بخشداران شهرستان چگنی</h1>

    @if($sections->count() > 0)
        <div class="grid">
            @foreach($sections as $section)
                @php
                    $subdomain = $section->subdomain;
                    $bakhshdarUrl = $subdomain ? "https://{$subdomain}.chegeniroosta.ir/bakhshdar" : '#';
                @endphp
                <a href="{{ $bakhshdarUrl }}" class="card" target="_blank" rel="noopener">
                    @if($section->bakhshdar_photo)
                        <img src="{{ asset('storage/' . $section->bakhshdar_photo) }}" alt="{{ $section->bakhshdar_name }}" class="photo">
                    @else
                        <div class="photo-ph">👤</div>
                    @endif
                    <h4>{{ $section->name }}</h4>
                    <div class="role">بخشدار</div>
                    <div class="bakhshdar-name">{{ $section->bakhshdar_name ?? 'در حال تکمیل' }}</div>
                    <span class="btn">مشاهده پروفایل ←</span>
                </a>
            @endforeach
        </div>
    @else
        <div class="empty-box">
            <div style="font-size:50px;margin-bottom:12px;">🏛</div>
            <p>هنوز بخشی ثبت نشده است.</p>
        </div>
    @endif

    <div style="text-align:center;margin-top:30px;">
        <a href="/" style="display:inline-block;background:#c94a2b;color:white;padding:12px 30px;border-radius:8px;text-decoration:none;font-weight:600;">→ بازگشت به صفحه اصلی</a>
    </div>
</div>

@include('partials.gov-footer')
</body>
</html>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>بخشداران شهرستان چگنی</title>
<style>
* { box-sizing: border-box; }
body { margin: 0; font-family: 'Vazirmatn', Tahoma, Arial, sans-serif; background: #f5f7fa; color: #263238; }
.wrap { max-width: 1100px; margin: 40px auto; padding: 0 15px; }
.page-title { font-size: 26px; color: #c62828; margin: 0 0 25px; padding-bottom: 15px; border-bottom: 3px solid #ffd54f; }
.grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px; }
.card { background: white; border-radius: 14px; padding: 25px 20px; text-align: center; box-shadow: 0 4px 18px rgba(0,0,0,.06); text-decoration: none; color: inherit; border-top: 4px solid #2e7d32; transition: transform .25s, box-shadow .25s; }
.card:hover { transform: translateY(-5px); box-shadow: 0 14px 35px rgba(46,125,50,.15); }
.card .photo { width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 4px solid #e8f5e9; margin: 0 auto 15px; display: block; }
.card .photo-ph { width: 110px; height: 110px; border-radius: 50%; background: linear-gradient(135deg, #e8f5e9, #c8e6c9); display: flex; align-items: center; justify-content: center; font-size: 50px; color: #90a4ae; margin: 0 auto 15px; border: 4px solid #e8f5e9; }
.card h4 { margin: 0 0 6px; color: #c62828; font-size: 18px; font-weight: 800; }
.card .role { color: #546e7a; font-size: 13px; margin-bottom: 6px; }
.card .bakhshdar-name { color: #2e7d32; font-size: 14px; font-weight: 600; margin-bottom: 12px; }
.card .btn { display: inline-block; margin-top: 8px; padding: 8px 20px; background: #2e7d32; color: white; border-radius: 6px; font-size: 13px; font-weight: 600; }
.empty-box { background: white; padding: 60px 20px; text-align: center; border-radius: 14px; color: #90a4ae; }
@media (max-width: 700px) { .grid { grid-template-columns: 1fr; } }
</style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
</head>
<body>

@include('partials.gov-header', ['headerTitle' => 'بخشداران شهرستان چگنی'])

<div class="wrap">
    <h1 class="page-title">👤 بخشداران شهرستان چگنی</h1>

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
        <a href="/" style="display:inline-block;background:#2e7d32;color:white;padding:12px 30px;border-radius:8px;text-decoration:none;font-weight:600;">→ بازگشت به صفحه اصلی</a>
    </div>
</div>

@include('partials.gov-footer')
</body>
</html>

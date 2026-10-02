<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>شورای اسلامی بخش {{ $section->name }}</title>
<style>
* { box-sizing: border-box; }
body { margin: 0; font-family: 'Vazirmatn', Tahoma, Arial, sans-serif; background: #f5f7fa; color: #263238; }
.wrap { max-width: 1100px; margin: 30px auto; padding: 0 15px; }
.page-title { font-size: 26px; color: #1e5b9c; margin: 0 0 25px; padding-bottom: 15px; border-bottom: 3px solid #ffd54f; }
.page-title span { color: #14497f; }
.council-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px; }
.council-card {
    background: white; border-radius: 14px; padding: 25px 20px;
    text-align: center; box-shadow: 0 4px 18px rgba(0,0,0,.06);
    transition: transform .25s ease, box-shadow .25s ease;
    border-top: 4px solid #1e5b9c;
}
.council-card:hover { transform: translateY(-5px); box-shadow: 0 14px 35px rgba(30,91,156,.15); }
.council-card .photo {
    width: 110px; height: 110px; border-radius: 50%;
    object-fit: cover; border: 4px solid #e3f2fd;
    margin: 0 auto 15px; display: block;
}
.council-card .photo-ph {
    width: 110px; height: 110px; border-radius: 50%;
    background: linear-gradient(135deg, #e3f2fd, #bbdefb);
    display: flex; align-items: center; justify-content: center;
    font-size: 50px; color: #90a4ae; margin: 0 auto 15px;
    border: 4px solid #e3f2fd;
}
.council-card .position-badge {
    display: inline-block; padding: 4px 14px;
    background: #ffd54f; color: #14497f;
    border-radius: 20px; font-size: 11.5px; font-weight: 800;
    margin-bottom: 10px;
}
.council-card h4 { margin: 0 0 6px; color: #1e5b9c; font-size: 17px; font-weight: 800; }
.council-card .phone { color: #1565c0; font-size: 13px; text-decoration: none; direction: ltr; display: inline-block; margin-top: 8px; }
.council-card .phone:hover { text-decoration: underline; }
.council-card .bio { color: #64748b; font-size: 12.5px; line-height: 1.9; margin-top: 12px; text-align: right; }
.empty-box { background: white; padding: 60px 20px; text-align: center; border-radius: 14px; color: #90a4ae; }
.empty-box .icon { font-size: 60px; margin-bottom: 14px; }
@media (max-width: 700px) {
    .page-title { font-size: 20px; }
    .council-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
    .council-card { padding: 18px 12px; }
    .council-card .photo, .council-card .photo-ph { width: 80px; height: 80px; font-size: 36px; }
    .council-card h4 { font-size: 14px; }
}
</style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
</head>
<body>

@include('partials.gov-header', ['headerTitle' => 'شورای اسلامی بخشداری ' . $section->name])

<div class="wrap">
    <h1 class="page-title">👥 اعضای شورای اسلامی بخش <span>{{ $section->name }}</span></h1>

    @if($members->count() > 0)
        <div class="council-grid">
            @foreach($members as $m)
                <div class="council-card">
                    @if($m->photo)
                        <img src="{{ asset('storage/' . $m->photo) }}" alt="{{ $m->name }}" class="photo">
                    @else
                        <div class="photo-ph">👤</div>
                    @endif
                    <div class="position-badge">{{ $m->position }}</div>
                    <h4>{{ $m->name }}</h4>
                    @if($m->phone)
                        <a href="tel:{{ $m->phone }}" class="phone">📞 {{ $m->phone }}</a>
                    @endif
                    @if($m->bio)
                        <p class="bio">{{ $m->bio }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-box">
            <div class="icon">👥</div>
            <p>هنوز اعضای شورای این بخش ثبت نشده است.</p>
        </div>
    @endif
</div>

@include('partials.gov-footer')

</body>
</html>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>معرفی {{ $deputy->name }} - معاون فرماندار چگنی</title>
<style>
* { box-sizing: border-box; }
body { margin: 0; font-family: 'Vazirmatn', Tahoma, Arial, sans-serif; background: #fff7e9; color: #2b2119; }
.wrap { max-width: 900px; margin: 40px auto; padding: 0 15px; }
.profile-card { background: white; border-radius: 16px; box-shadow: 0 10px 35px rgba(0,0,0,0.08); overflow: hidden; }
.profile-top { display: flex; gap: 30px; padding: 35px; background: linear-gradient(135deg, #fdeee7, #fff7e9); align-items: center; flex-wrap: wrap; }
.profile-photo { width: 180px; height: 180px; border-radius: 50%; object-fit: cover; border: 5px solid white; box-shadow: 0 8px 25px rgba(30,91,156,0.25); flex-shrink: 0; }
.profile-photo-ph { width: 180px; height: 180px; border-radius: 50%; background: #e6d8c3; display: flex; align-items: center; justify-content: center; font-size: 60px; color: #a2948a; border: 5px solid white; flex-shrink: 0; }
.profile-info h2 { margin: 0 0 8px; color: #b3401f; font-size: 28px; }
.profile-info .role { color: #c94a2b; font-size: 16px; margin-bottom: 12px; font-weight: 600; }
.profile-info .contact { font-size: 14px; color: #6b5a4a; line-height: 2; }
.profile-info .contact a { color: #c94a2b; text-decoration: none; direction: ltr; }
.profile-body { padding: 35px; }
.profile-section { margin-bottom: 30px; }
.profile-section h3 { color: #b3401f; font-size: 18px; margin: 0 0 15px; padding-bottom: 10px; border-bottom: 2px solid #fdeee7; }
.profile-section p { color: #5c2313; line-height: 2; margin: 0; white-space: pre-line; }
.empty-note { color: #a2948a; font-style: italic; font-size: 14px; }
@media (max-width: 700px) {
    .profile-top { flex-direction: column; text-align: center; padding: 25px; }
    .profile-photo, .profile-photo-ph { width: 140px; height: 140px; font-size: 50px; }
    .profile-info h2 { font-size: 22px; }
}
</style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ui-v3.css') }}?v=6">
</head>
<body class="v3-body">

@include('partials.gov-header', ['headerTitle' => 'معرفی معاون فرماندار چگنی'])
@include('partials.v3-crumbs', ['crumbs' => [
    ['label' => 'پرتال چگنی', 'url' => url('/')],
    ['label' => 'مسئولان شهرستان', 'url' => url('/officials/governor')],
    ['label' => 'معاون فرماندار'],
]])

<div style="width: min(1120px, 94%); margin: 0 auto;">
    <h1 class="page-title">معاون فرماندار</h1>
</div>

<div class="wrap">
    <div class="profile-card">
        <div class="profile-top">
            @if($deputy->photo)
                <img src="{{ asset('storage/' . $deputy->photo) }}" alt="{{ $deputy->name }}" class="profile-photo">
            @else
                <div class="profile-photo-ph">👤</div>
            @endif
            <div class="profile-info">
                <h2>{{ $deputy->name }}</h2>
                <div class="role">{{ $deputy->position }}</div>
                <div class="contact">
                    @if($deputy->phone)<div>📞 <a href="tel:{{ $deputy->phone }}">{{ $deputy->phone }}</a></div>@endif
                    @if($deputy->email)<div>✉️ <a href="mailto:{{ $deputy->email }}">{{ $deputy->email }}</a></div>@endif
                </div>
            </div>
        </div>
        <div class="profile-body">
            <div class="profile-section">
                <h3>📝 درباره ایشان</h3>
                @if($deputy->bio)<p>{{ $deputy->bio }}</p>@else<p class="empty-note">در حال تکمیل</p>@endif
            </div>
            <div class="profile-section">
                <h3>🎓 تحصیلات</h3>
                @if($deputy->education)<p>{{ $deputy->education }}</p>@else<p class="empty-note">در حال تکمیل</p>@endif
            </div>
            <div class="profile-section">
                <h3>💼 سوابق شغلی</h3>
                @if($deputy->experience)<p>{{ $deputy->experience }}</p>@else<p class="empty-note">در حال تکمیل</p>@endif
            </div>
            <div style="text-align:center;margin-top:30px;">
                <a href="{{ route('officials.deputies') }}" style="display:inline-block;background:#c94a2b;color:white;padding:12px 30px;border-radius:8px;text-decoration:none;font-weight:600;">→ لیست معاونین</a>
            </div>
        </div>
    </div>
</div>

@include('partials.gov-footer')
</body>
</html>

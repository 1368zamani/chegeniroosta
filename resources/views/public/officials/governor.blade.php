<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>معرفی فرماندار چگنی</title>
<style>
* { box-sizing: border-box; }
body { margin: 0; font-family: 'Vazirmatn', Tahoma, Arial, sans-serif; background: #f5f7fa; color: #263238; }
.wrap { max-width: 900px; margin: 40px auto; padding: 0 15px; }
.profile-card { background: white; border-radius: 16px; box-shadow: 0 10px 35px rgba(0,0,0,0.08); overflow: hidden; }
.profile-top { display: flex; gap: 30px; padding: 35px; background: linear-gradient(135deg, #e3f2fd, #f5f7fa); align-items: center; flex-wrap: wrap; }
.profile-photo { width: 180px; height: 180px; border-radius: 50%; object-fit: cover; border: 5px solid white; box-shadow: 0 8px 25px rgba(30,91,156,0.25); flex-shrink: 0; }
.profile-photo-ph { width: 180px; height: 180px; border-radius: 50%; background: #cfd8dc; display: flex; align-items: center; justify-content: center; font-size: 60px; color: #90a4ae; border: 5px solid white; flex-shrink: 0; }
.profile-info h2 { margin: 0 0 8px; color: #c62828; font-size: 28px; }
.profile-info .role { color: #1e5b9c; font-size: 16px; margin-bottom: 12px; font-weight: 600; }
.profile-info .contact { font-size: 14px; color: #607d8b; line-height: 2; }
.profile-info .contact a { color: #1565c0; text-decoration: none; direction: ltr; display: inline-block; }
.profile-body { padding: 35px; }
.profile-section { margin-bottom: 30px; }
.profile-section:last-child { margin-bottom: 0; }
.profile-section h3 { color: #c62828; font-size: 18px; margin: 0 0 15px; padding-bottom: 10px; border-bottom: 2px solid #e3f2fd; }
.profile-section p { color: #1a3a5c; line-height: 2; margin: 0; white-space: pre-line; }
.empty-note { color: #90a4ae; font-style: italic; font-size: 14px; }
@media (max-width: 700px) {
    .profile-top { flex-direction: column; text-align: center; padding: 25px; }
    .profile-photo, .profile-photo-ph { width: 140px; height: 140px; font-size: 50px; }
    .profile-info h2 { font-size: 22px; }
    .profile-body { padding: 25px; }
}
</style>
<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
</head>
<body>

@include('partials.gov-header', ['headerTitle' => 'معرفی فرماندار شهرستان چگنی'])

<div class="wrap">
    <div class="profile-card">
        <div class="profile-top">
            @if($governor->photo)
                <img src="{{ asset('storage/' . $governor->photo) }}" alt="{{ $governor->name }}" class="profile-photo">
            @else
                <div class="profile-photo-ph">👤</div>
            @endif
            <div class="profile-info">
                <h2>{{ $governor->name }}</h2>
                <div class="role">{{ $governor->position }}</div>
                <div class="contact">
                    @if($governor->phone)
                        <div>📞 <a href="tel:{{ $governor->phone }}">{{ $governor->phone }}</a></div>
                    @endif
                    @if($governor->email)
                        <div>✉️ <a href="mailto:{{ $governor->email }}">{{ $governor->email }}</a></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="profile-body">
            <div class="profile-section">
                <h3>📝 درباره فرماندار</h3>
                @if($governor->bio)
                    <p>{{ $governor->bio }}</p>
                @else
                    <p class="empty-note">در حال تکمیل</p>
                @endif
            </div>

            <div class="profile-section">
                <h3>🎓 تحصیلات</h3>
                @if($governor->education)
                    <p>{{ $governor->education }}</p>
                @else
                    <p class="empty-note">در حال تکمیل</p>
                @endif
            </div>

            <div class="profile-section">
                <h3>💼 سوابق شغلی</h3>
                @if($governor->experience)
                    <p>{{ $governor->experience }}</p>
                @else
                    <p class="empty-note">در حال تکمیل</p>
                @endif
            </div>

            <div style="text-align:center;margin-top:30px;">
                <a href="/" style="display:inline-block;background:#1e5b9c;color:white;padding:12px 30px;border-radius:8px;text-decoration:none;font-weight:600;">→ بازگشت به صفحه اصلی</a>
            </div>
        </div>
    </div>
</div>

@include('partials.gov-footer')
</body>
</html>

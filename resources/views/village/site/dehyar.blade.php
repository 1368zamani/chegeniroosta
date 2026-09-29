<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>معرفی دهیار {{ $village->name }}</title>
<style>
* { box-sizing: border-box; }
body { margin: 0; font-family: 'Vazirmatn', Tahoma, Arial, sans-serif; background: #f5f7fa; color: #263238; }
.wrap { width: min(900px, 94%); margin: 40px auto; padding: 0 15px; }
.profile-card { background: white; border-radius: 16px; box-shadow: 0 10px 35px rgba(0,0,0,0.08); overflow: hidden; }
.profile-top {
    display: flex; gap: 30px; padding: 35px;
    background: linear-gradient(135deg, #e3f2fd, #f5f7fa);
    align-items: center; flex-wrap: wrap;
}
.profile-photo {
    width: 180px; height: 180px; border-radius: 50%;
    object-fit: cover; border: 5px solid white;
    box-shadow: 0 8px 25px rgba(30,91,156,0.25); flex-shrink: 0;
}
.profile-photo-placeholder {
    width: 180px; height: 180px; border-radius: 50%;
    background: #cfd8dc; display: flex; align-items: center; justify-content: center;
    font-size: 60px; color: #90a4ae; border: 5px solid white; flex-shrink: 0;
}
.profile-info h2 { margin: 0 0 8px; color: #1e5b9c; font-size: 26px; }
.profile-info .role { color: #546e7a; font-size: 15px; margin-bottom: 12px; }
.profile-info .contact { font-size: 14px; color: #607d8b; line-height: 2; }
.profile-info .contact a { color: #1e5b9c; text-decoration: none; direction: ltr; display: inline-block; }
.profile-info .contact a:hover { text-decoration: underline; }
.profile-body { padding: 35px; }
.profile-section { margin-bottom: 35px; }
.profile-section:last-child { margin-bottom: 0; }
.profile-section h3 {
    color: #1e5b9c; font-size: 18px; margin: 0 0 15px;
    padding-bottom: 10px; border-bottom: 2px solid #e3f2fd;
}
.profile-section p { color: #546e7a; line-height: 2; margin: 0; white-space: pre-line; }
.empty-note { color: #90a4ae; font-style: italic; font-size: 14px; }
@media (max-width: 700px) {
    .profile-top { flex-direction: column; text-align: center; padding: 25px; }
    .profile-photo, .profile-photo-placeholder { width: 140px; height: 140px; font-size: 50px; }
    .profile-info h2 { font-size: 22px; }
    .profile-body { padding: 25px; }
}
</style>
</head>
<body>

@include('partials.gov-header', ['headerTitle' => 'معرفی دهیار ' . $village->name])

<div class="wrap">
    <div class="profile-card">

        <div class="profile-top">
            @if($village->dehyar_photo)
                <img src="{{ asset('storage/' . $village->dehyar_photo) }}" alt="{{ $village->dehyar_name }}" class="profile-photo">
            @else
                <div class="profile-photo-placeholder">👤</div>
            @endif

            <div class="profile-info">
                <h2>{{ $village->dehyar_name ?? 'در حال تکمیل' }}</h2>
                <div class="role">دهیار روستای {{ $village->name }}</div>
                <div class="contact">
                    @if($village->dehyar_phone)
                        <div>📞 <a href="tel:{{ $village->dehyar_phone }}">{{ $village->dehyar_phone }}</a></div>
                    @endif
                    @if($village->dehyar_email)
                        <div>✉️ <a href="mailto:{{ $village->dehyar_email }}">{{ $village->dehyar_email }}</a></div>
                    @endif
                    @if($village->dehyar_instagram)
                        @php
                            $ig = $village->dehyar_instagram;
                            $igUrl = \Illuminate\Support\Str::startsWith($ig, ['http://', 'https://'])
                                ? $ig
                                : 'https://instagram.com/' . ltrim($ig, '@/');
                        @endphp
                        <div>📷 <a href="{{ $igUrl }}" target="_blank" rel="noopener">صفحه اینستاگرام دهیار</a></div>
                    @endif
                    @if($village->dehyar_hours)
                        <div>🕐 ساعات حضور: {{ $village->dehyar_hours }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="profile-body">

            <div class="profile-section">
                <h3>📝 درباره دهیار</h3>
                @if($village->dehyar_bio)
                    <p>{{ $village->dehyar_bio }}</p>
                @else
                    <p class="empty-note">بیوگرافی دهیار در حال تکمیل است.</p>
                @endif
            </div>

            <div class="profile-section">
                <h3>🎓 تحصیلات</h3>
                @if($village->dehyar_education)
                    <p>{{ $village->dehyar_education }}</p>
                @else
                    <p class="empty-note">اطلاعات تحصیلات در حال تکمیل است.</p>
                @endif
            </div>

            <div class="profile-section">
                <h3>💼 سوابق شغلی</h3>
                @if($village->dehyar_experience)
                    <p>{{ $village->dehyar_experience }}</p>
                @else
                    <p class="empty-note">اطلاعات سوابق در حال تکمیل است.</p>
                @endif
            </div>

            <div style="text-align:center;margin-top:30px;">
                <a href="{{ route('public.village.show', $village->slug) }}"
                   style="display:inline-block;background:#1e5b9c;color:white;padding:12px 30px;border-radius:8px;text-decoration:none;font-weight:600;">
                    → بازگشت به سایت روستا
                </a>
            </div>

        </div>
    </div>
</div>

@include('partials.gov-footer')

</body>
</html>

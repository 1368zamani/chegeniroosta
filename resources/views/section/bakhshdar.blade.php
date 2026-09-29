<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta name="google-site-verification" content="{{ config('services.google.search_console_verification') }}">
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>معرفی بخشدار {{ $section->name }}</title>
    <meta name="robots" content="index, follow">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #f5f7fa; color: #263238; }
        .header { background: linear-gradient(135deg, #1565c0, #0d47a1); color: white; padding: 20px; }
        .header-inner { width: min(1150px, 92%); margin: auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .header h1 { margin: 0; font-size: 20px; }
        .header a { color: white; text-decoration: none; font-size: 14px; }
        .container { width: min(900px, 92%); margin: auto; padding: 40px 0; }
        .profile-card { background: white; border-radius: 16px; box-shadow: 0 10px 35px rgba(0,0,0,0.08); overflow: hidden; }
        .profile-top { display: flex; gap: 30px; padding: 35px; background: linear-gradient(135deg, #e3f2fd, #f5f7fa); align-items: center; flex-wrap: wrap; }
        .profile-photo { width: 180px; height: 180px; border-radius: 50%; object-fit: cover; border: 5px solid white; box-shadow: 0 8px 25px rgba(21,101,192,0.25); flex-shrink: 0; }
        .profile-photo-placeholder { width: 180px; height: 180px; border-radius: 50%; background: #cfd8dc; display: flex; align-items: center; justify-content: center; font-size: 60px; color: #90a4ae; border: 5px solid white; flex-shrink: 0; }
        .profile-info h2 { margin: 0 0 8px; color: #1565c0; font-size: 26px; }
        .profile-info .role { color: #546e7a; font-size: 15px; margin-bottom: 12px; }
        .profile-info .contact { font-size: 14px; color: #607d8b; line-height: 2; }
        .profile-info .contact a { color: #1565c0; text-decoration: none; direction: ltr; display: inline-block; }
        .profile-body { padding: 35px; }
        .profile-section { margin-bottom: 35px; }
        .profile-section:last-child { margin-bottom: 0; }
        .profile-section h3 { color: #1565c0; font-size: 18px; margin: 0 0 15px; padding-bottom: 10px; border-bottom: 2px solid #e3f2fd; }
        .profile-section p { color: #546e7a; line-height: 2; margin: 0; }
        .timeline-list { list-style: none; margin: 0; padding: 0; }
        .timeline-list li { padding: 12px 20px; border-right: 3px solid #1565c0; margin-bottom: 10px; background: #f8fafc; border-radius: 6px; color: #37474f; line-height: 1.8; }
        .empty-note { color: #90a4ae; font-style: italic; font-size: 14px; }
        @media (max-width: 700px) {
            .profile-top { flex-direction: column; text-align: center; padding: 25px; }
            .profile-photo, .profile-photo-placeholder { width: 140px; height: 140px; }
            .profile-info h2 { font-size: 22px; }
            .profile-body { padding: 25px; }
        }
    
        /* ============ عکس بخشدار - مربع و بزرگ ============ */
        .bakhshdar-side-card {
            text-align: center;
            padding: 22px 16px;
        }
        .bakhshdar-side-card .photo {
            width: 100%;
            max-width: 220px;
            height: 220px;
            border-radius: 12px;
            object-fit: cover;
            border: 3px solid #ffd54f;
            box-shadow: 0 8px 25px rgba(255,213,79,0.4);
            display: block;
            margin: 0 auto 14px;
        }
        .bakhshdar-side-card .photo-ph {
            width: 100%;
            max-width: 220px;
            height: 220px;
            border-radius: 12px;
            background: linear-gradient(135deg, #e3f2fd, #f5f7fa);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 80px;
            color: #90a4ae;
            margin: 0 auto 14px;
            border: 3px solid #ffd54f;
            box-shadow: 0 8px 25px rgba(255,213,79,0.3);
        }
        .bakhshdar-side-card .label {
            display: inline-block;
            font-size: 11px;
            background: #0d2e4f;
            color: #ffd54f;
            padding: 3px 14px;
            border-radius: 14px;
            margin-bottom: 10px;
            font-weight: 700;
        }
        .bakhshdar-side-card h4 {
            margin: 0 0 4px;
            color: #0d2e4f;
            font-size: 17px;
            font-weight: 800;
        }
        .bakhshdar-side-card .role {
            font-size: 12.5px;
            color: #78909c;
            margin: 0 0 16px;
        }
        .bakhshdar-side-card .btn {
            display: inline-block;
            background: #0d2e4f;
            color: #fff;
            padding: 10px 22px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .bakhshdar-side-card .btn:hover {
            background: #ffd54f;
            color: #0d2e4f;
            transform: translateY(-2px);
        }

        @media (max-width: 700px) {
            .bakhshdar-side-card .photo,
            .bakhshdar-side-card .photo-ph {
                max-width: 180px;
                height: 180px;
            }
        }

    </style>
    @include("partials.analytics")

<style>
/* CARD-HOVER-2026 */
.info-item,
.info-card,
[class*="info-"] {
    transition: transform .25s ease, box-shadow .25s ease !important;
}
.info-item:hover,
.info-card:hover {
    transform: translateY(-3px) !important;
    box-shadow: 0 8px 24px rgba(13,46,79,0.15) !important;
}
.v-gallery-item:hover {
    transform: translateY(-3px) !important;
    box-shadow: 0 12px 30px rgba(13,46,79,0.18) !important;
}
/* tel: — لینک تماس */
a[href^="tel:"] {
    direction: ltr;
    display: inline-block;
    font-weight: 600;
}
</style>

</head>
<body>

@include('partials.gov-header', ['headerTitle' => isset($section) ? 'پایگاه اطلاع‌رسانی بخشداری ' . $section->name : 'پایگاه اطلاع‌رسانی بخش‌های شهرستان چگنی'])

<div class="container">
    <div class="profile-card">

        <div class="profile-top">
            @if($section->bakhshdar_photo)
                <img src="{{ asset('storage/' . $section->bakhshdar_photo) }}" alt="{{ $section->bakhshdar_name }}" class="profile-photo">
            @else
                <div class="profile-photo-placeholder">👤</div>
            @endif

            <div class="profile-info">
                <h2>{{ $section->bakhshdar_name ?? 'در حال تکمیل' }}</h2>
                <div class="role">
                    بخشدار {{ $section->name }}
                    @if($section->county) — شهرستان {{ $section->county }} @endif
                </div>
                <div class="contact">
                    @if($section->bakhshdar_phone)
                        <div>📞 <a href="tel:{{ $section->bakhshdar_phone }}">{{ $section->bakhshdar_phone }}</a></div>
                    @endif
                    @if($section->bakhshdar_email)
                        <div>✉️ <a href="mailto:{{ $section->bakhshdar_email }}">{{ $section->bakhshdar_email }}</a></div>
                    @endif
                    @if($section->bakhshdar_instagram)
                        @php
                            $ig = $section->bakhshdar_instagram;
                            $igUrl = \Illuminate\Support\Str::startsWith($ig, ['http://', 'https://'])
                                ? $ig
                                : 'https://instagram.com/' . ltrim($ig, '@/');
                        @endphp
                        <div>📷 <a href="{{ $igUrl }}" target="_blank" rel="noopener">صفحه اینستاگرام بخشدار</a></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="profile-body">

            <div class="profile-section">
                <h3>📝 درباره بخشدار</h3>
                @if($section->bakhshdar_bio)
                    <p>{{ $section->bakhshdar_bio }}</p>
                @else
                    <p class="empty-note">بیوگرافی بخشدار در حال تکمیل است.</p>
                @endif
            </div>

            <div class="profile-section">
                <h3>🎓 تحصیلات</h3>
                @if(!empty(trim((string) ($section->bakhshdar_education ?? ''))))
                    <p style="white-space: pre-line; line-height: 2; color: #546e7a;">{{ $section->bakhshdar_education }}</p>
                @else
                    <p class="empty-note">اطلاعات تحصیلات در حال تکمیل است.</p>
                @endif
            </div>

            <div class="profile-section">
                <h3>💼 سوابق شغلی</h3>
                @if(!empty(trim((string) ($section->bakhshdar_experience ?? ''))))
                    <p style="white-space: pre-line; line-height: 2; color: #546e7a;">{{ $section->bakhshdar_experience }}</p>
                @else
                    <p class="empty-note">اطلاعات سوابق در حال تکمیل است.</p>
                @endif
            </div>

        </div>
    </div>
</div>

</body>
</html>

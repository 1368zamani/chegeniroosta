<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $village->name }} | روستا نما</title>
    <meta name="description" content="{{ $village->description ?: 'اطلاعات و اخبار روستای ' . $village->name }}">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#0d2e4f">

    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Tahoma, Arial, sans-serif; background: #eef2f7; color: #263238; }

        .container { width: min(1100px, 92%); margin: auto; padding: 30px 0; }

        .section-title {
            color: #0d2e4f;
            font-size: 20px;
            font-weight: 800;
            margin: 30px 0 18px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e3f2fd;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ============ اطلاعات روستا ============ */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
            margin-bottom: 30px;
        }
        .info-item {
            background: white;
            border-radius: 10px;
            padding: 16px 18px;
            box-shadow: 0 3px 12px rgba(13,46,79,0.06);
            border-right: 4px solid #1565c0;
        }
        .info-item .label {
            display: block;
            font-size: 12.5px;
            color: #78909c;
            margin-bottom: 6px;
            font-weight: 600;
        }
        .info-item .value {
            display: block;
            font-size: 16px;
            color: #0d2e4f;
            font-weight: 800;
        }
        .info-item .value a { color: #1565c0; text-decoration: none; direction: ltr; display: inline-block; }

        /* ============ گالری تصاویر ============ */
        .v-gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 14px;
            margin-bottom: 30px;
        }
        .v-gallery-item {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            transition: transform .2s;
        }
        .v-gallery-item:hover { transform: translateY(-3px); }
        .v-gallery-item img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            display: block;
        }
        .v-gallery-caption {
            padding: 10px 12px;
            font-size: 12.5px;
            color: #546e7a;
            text-align: center;
        }

        /* ============ اخبار ============ */
        .v-news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 18px;
            margin-bottom: 30px;
        }
        .v-news-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 18px rgba(0,0,0,0.06);
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
        }
        .v-news-card .body { padding: 18px; }
        .v-news-card h4 { margin: 0 0 8px; color: #0d2e4f; font-size: 15px; }
        .v-news-card .date { color: #90a4ae; font-size: 12px; margin-bottom: 8px; }
        .v-news-card p { margin: 0; color: #607d8b; font-size: 13px; line-height: 1.8; }

        /* ============ امکانات ============ */
        .v-facilities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 14px;
            margin-bottom: 30px;
        }
        .v-facility-card {
            background: white;
            border-radius: 12px;
            padding: 18px 12px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 2px solid #e3f2fd;
        }
        .v-facility-card.unavailable { border-color: #ffcdd2; opacity: 0.6; }
        .v-facility-card .icon { font-size: 30px; display: block; margin-bottom: 8px; }
        .v-facility-card .name { font-size: 13px; font-weight: 700; color: #0d2e4f; }
        .v-facility-card .status { font-size: 11px; color: #78909c; margin-top: 4px; }

        /* ============ شورا ============ */
        .v-council-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 30px;
        }
        .v-council-card {
            background: white;
            border-radius: 12px;
            padding: 20px 16px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }
        .v-council-card .photo {
            width: 80px; height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e3f2fd, #f5f7fa);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 12px;
            font-size: 32px;
            color: #90a4ae;
            border: 3px solid #ffd54f;
            overflow: hidden;
        }
        .v-council-card .photo img { width: 100%; height: 100%; object-fit: cover; }
        .v-council-card h5 { margin: 0 0 4px; color: #0d2e4f; font-size: 14px; }
        .v-council-card .position { color: #c62828; font-size: 12px; font-weight: 700; margin: 0 0 8px; }
        .v-council-card .phone { color: #1565c0; font-size: 12.5px; text-decoration: none; direction: ltr; display: inline-block; }

        /* ============ جاذبه‌ها ============ */
        .v-attractions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 18px;
            margin-bottom: 30px;
        }
        .v-attraction-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }
        .v-attraction-card .image {
            width: 100%; height: 160px;
            background: linear-gradient(135deg, #e8f5e9, #f5f7fa);
            display: flex; align-items: center; justify-content: center;
            font-size: 50px;
            color: #90a4ae;
            overflow: hidden;
        }
        .v-attraction-card .image img { width: 100%; height: 100%; object-fit: cover; }
        .v-attraction-card .body { padding: 16px; }
        .v-attraction-card h5 { margin: 0 0 6px; color: #0d2e4f; font-size: 15px; }
        .v-attraction-card .category {
            display: inline-block; background: #e8f5e9; color: #2e7d32;
            font-size: 10.5px; padding: 2px 10px; border-radius: 10px;
            font-weight: 700; margin-bottom: 8px;
        }
        .v-attraction-card p { margin: 0; color: #607d8b; font-size: 12.5px; line-height: 1.8; }

        /* ============ درخواست مردمی ============ */
        .v-request-box {
            background: linear-gradient(135deg, #0d2e4f, #1565c0);
            color: white;
            border-radius: 16px;
            padding: 32px 24px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(13,46,79,0.2);
            margin-bottom: 30px;
        }
        .v-request-box h3 { margin: 0 0 12px; font-size: 22px; }
        .v-request-box p { margin: 0 0 22px; opacity: 0.9; font-size: 14px; line-height: 1.9; }
        .v-request-buttons { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .v-btn {
            display: inline-block; padding: 12px 28px; border-radius: 10px;
            text-decoration: none; font-weight: 700; font-size: 14px;
        }
        .v-btn-primary { background: #ffd54f; color: #0d2e4f; }
        .v-btn-secondary { background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.3); }

        @media (max-width: 700px) {
            .info-grid { grid-template-columns: 1fr; }
            .v-gallery-grid { grid-template-columns: repeat(2, 1fr); }
            .v-gallery-item img { height: 120px; }
            .v-facilities-grid { grid-template-columns: repeat(2, 1fr); }
            .v-council-grid { grid-template-columns: repeat(2, 1fr); }
            .v-attractions-grid { grid-template-columns: 1fr; }
        }
    </style>

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

@php
    $__villageSection = null;
    if (isset($village) && $village->section_id) {
        $__villageSection = \App\Models\Section::find($village->section_id);
    }
@endphp
@include('partials.gov-header', [
    'headerTitle' => 'پایگاه اطلاع‌رسانی روستای ' . ($village->name ?? ''),
    'section' => $__villageSection
])

<div class="container">

    {{-- ============ درباره روستا ============ --}}
    @if($village->description)
        <h2 class="section-title">📝 درباره {{ $village->name }}</h2>
        <div class="v-request-box" style="background:white; color:#546e7a; text-align:right; box-shadow:0 3px 12px rgba(13,46,79,0.06); padding:20px 24px; font-size:14px; line-height:2;">
            {!! nl2br(e($village->description)) !!}
        </div>
    @endif

    {{-- ============ اطلاعات روستا ============ --}}
    <h2 class="section-title">📋 اطلاعات روستا</h2>
    <div class="info-grid">
        @if($village->households)
            <div class="info-item">
                <span class="label">👥 تعداد خانوار</span>
                <span class="value">{{ number_format($village->households) }} خانوار</span>
            </div>
        @endif
        @if($village->population)
            <div class="info-item">
                <span class="label">🧑 جمعیت</span>
                <span class="value">{{ number_format($village->population) }} نفر</span>
            </div>
        @endif
        @if($village->area)
            <div class="info-item">
                <span class="label">📐 مساحت</span>
                <span class="value">{{ number_format($village->area, 1) }} هکتار</span>
            </div>
        @endif
        @if($village->section)
            <div class="info-item">
                <span class="label">📍 بخش</span>
                <span class="value">{{ $village->section }}</span>
            </div>
        @endif
        @if($village->district)
            <div class="info-item">
                <span class="label">🏘 دهستان</span>
                <span class="value">{{ $village->district }}</span>
            </div>
        @endif
        @if($village->county)
            <div class="info-item">
                <span class="label">🗺 شهرستان</span>
                <span class="value">{{ $village->county }}</span>
            </div>
        @endif
        @if($village->province)
            <div class="info-item">
                <span class="label">🏞 استان</span>
                <span class="value">{{ $village->province }}</span>
            </div>
        @endif
        @if($village->dehyar_name)
            <div class="info-item">
                <span class="label">👤 دهیار</span>
                <span class="value">{{ $village->dehyar_name }}</span>
            </div>
        @endif
        @if($village->dehyar_instagram)
            @php
                $ig = $village->dehyar_instagram;
                $igUrl = \Illuminate\Support\Str::startsWith($ig, ['http://', 'https://'])
                    ? $ig
                    : 'https://instagram.com/' . ltrim($ig, '@/');
            @endphp
            <div class="info-item">
                <span class="label">📷 اینستاگرام</span>
                <span class="value">
                    <a href="{{ $igUrl }}" target="_blank" rel="noopener" style="color:#e91e63;text-decoration:none;font-weight:600;direction:ltr;display:inline-block;">صفحه دهیار</a>
                </span>
            </div>
        @endif
        @if($village->council_head_name)
            <div class="info-item">
                <span class="label">👥 رئیس شورا</span>
                <span class="value">{{ $village->council_head_name }}</span>
            </div>
        @endif
        @if($village->phone)
            <div class="info-item">
                <span class="label">📞 تلفن</span>
                <span class="value"><a href="tel:{{ $village->phone }}">{{ $village->phone }}</a></span>
            </div>
        @endif
        @if($village->email)
            <div class="info-item">
                <span class="label">✉️ ایمیل</span>
                <span class="value"><a href="mailto:{{ $village->email }}">{{ $village->email }}</a></span>
            </div>
        @endif
        @if($village->address)
            <div class="info-item">
                <span class="label">🏠 آدرس</span>
                <span class="value">{{ $village->address }}</span>
            </div>
        @endif
    </div>

    {{-- ============ اخبار ============ --}}
    @if(isset($news) && $news->count() > 0)
        <h2 class="section-title">📰 اخبار {{ $village->name }}</h2>
        <div class="v-news-grid">
            @foreach($news as $item)
                <div class="v-news-card">
                    <div class="body">
                        <h4>
                            @if($item->is_pinned)
                                <span style="color:#f9a825;">📌</span>
                            @endif
                            {{ $item->title }}
                        </h4>
                        <div class="date">🗓 {{ $item->created_at->format('Y/m/d') }}</div>
                        <p>{{ \Illuminate\Support\Str::limit($item->excerpt ?: strip_tags($item->body), 140) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ============ گالری تصاویر ============ --}}
    @if(isset($images) && $images->count() > 0)
        <h2 class="section-title">🖼 گالری تصاویر {{ $village->name }}</h2>
        <div class="v-gallery-grid">
            @foreach($images as $image)
                <div class="v-gallery-item"
                     data-image-src="{{ asset('storage/' . $image->image) }}"
                     data-title="{{ $image->title }}"
                     data-description="{{ $image->description }}">
                    <img src="{{ asset('storage/' . $image->image) }}" alt="{{ $image->title ?: $village->name }}" loading="lazy">
                    @if($image->title)
                        <div class="v-gallery-caption">{{ $image->title }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
{{-- ============ پروژه‌ها ============ --}}
    @if(isset($projects) && $projects->count() > 0)
        <h2 class="section-title">🏗 پروژه‌های عمرانی</h2>
        <div class="v-news-grid">
            @foreach($projects as $project)
                <div class="v-news-card">
                    <div class="body">
                        <h4>{{ $project->title }}</h4>
                        <div class="date">{{ $project->progress }}% پیشرفت</div>
                        @if($project->description)
                            <p>{{ \Illuminate\Support\Str::limit($project->description, 120) }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ============ امکانات ============ --}}
    @if(isset($facilities) && $facilities->count() > 0)
        <h2 class="section-title">🏛 امکانات {{ $village->name }}</h2>
        <div class="v-facilities-grid">
            @foreach($facilities as $facility)
                <div class="v-facility-card {{ $facility->is_available ? '' : 'unavailable' }}">
                    <span class="icon">{{ $facility->icon ?: '🏛' }}</span>
                    <div class="name">{{ $facility->name }}</div>
                    <div class="status">{{ $facility->is_available ? 'دارد' : 'ندارد' }}</div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ============ شورا ============ --}}
    @if(isset($councilMembers) && $councilMembers->count() > 0)
        <h2 class="section-title">👥 شورای اسلامی روستا</h2>
        <div class="v-council-grid">
            @foreach($councilMembers as $member)
                <div class="v-council-card">
                    <div class="photo">
                        @if($member->photo)
                            <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}">
                        @else
                            👤
                        @endif
                    </div>
                    <h5>{{ $member->name }}</h5>
                    <p class="position">{{ $member->position }}</p>
                    @if($member->phone)
                        <a href="tel:{{ $member->phone }}" class="phone">📞 {{ $member->phone }}</a>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- ============ جاذبه‌ها ============ --}}
    @if(isset($attractions) && $attractions->count() > 0)
        <h2 class="section-title">🏞 جاذبه‌های {{ $village->name }}</h2>
        <div class="v-attractions-grid">
            @foreach($attractions as $attr)
                <div class="v-attraction-card">
                    <div class="image">
                        @if($attr->image)
                            <img src="{{ asset('storage/' . $attr->image) }}" alt="{{ $attr->name }}">
                        @else
                            🏞
                        @endif
                    </div>
                    <div class="body">
                        @if($attr->category)
                            <span class="category">{{ $attr->category }}</span>
                        @endif
                        <h5>{{ $attr->name }}</h5>
                        @if($attr->description)
                            <p>{{ \Illuminate\Support\Str::limit($attr->description, 100) }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ============ درخواست مردمی ============ --}}
    <div class="v-request-box">
        <h3>📩 درخواست خود را ثبت کنید</h3>
        <p>درخواست‌های خود در زمینه آب، برق، گاز، راه و ... را آنلاین ثبت کنید و با کد پیگیری دنبال کنید.</p>
        <div class="v-request-buttons">
            <a href="{{ route('village.site.request.create', $village->slug) }}" class="v-btn v-btn-primary">✍️ ثبت درخواست</a>
            <a href="{{ route('village.site.request.track', $village->slug) }}" class="v-btn v-btn-secondary">🔍 پیگیری</a>
        </div>
    </div>

</div>

{{-- ============ موقعیت روی نقشه ============ --}}
@if(!empty($village->latitude) && !empty($village->longitude))
    <h2 class="section-title">📍 موقعیت {{ $village->name }} روی نقشه</h2>
    @include('partials.village-map', ['village' => $village])
@endif

@include('partials.gov-footer')
@include('partials.dark-mode')
@include('partials.back-to-top')
@include('partials.gallery-lightbox')

</body>
</html>

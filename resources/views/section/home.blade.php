<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $section->name }} | پایگاه اطلاع‌رسانی</title>
    <meta name="description" content="{{ $section->description ?? 'پایگاه اطلاع‌رسانی بخش ' . $section->name . ' شهرستان چگنی' }}">
    
    <link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('css/green-theme.css') }}">
    @include('partials.pwa')

    


    


    

    <link rel="stylesheet" href="{{ asset('css/section-home.css') }}?v=1">
    <link rel="canonical" href="{{ url()->current() }}">
</head>
<body>

@include('partials.gov-header', ['headerTitle' => 'پایگاه اطلاع‌رسانی بخشداری ' . $section->name])
@include('partials.news-ticker', ['news' => $news ?? collect()])

@if($section->cover_image)
<div class="s-cover-image" style="width: 100%; max-height: 500px; overflow: hidden; margin-bottom: 20px; position: relative;">
    <img src="{{ asset('storage/' . $section->cover_image) }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" alt="{{ $section->name }}">
</div>
@endif


<div class="s-container">
    <div class="two-columns">
        {{-- ستون اصلی: اخبار --}}
        <main>
            <div class="box">
                <div class="box-header">
                    <h3>📰 آخرین اخبار و اطلاعیه‌ها</h3>
                    <div class="line"></div>
                </div>
                <div class="box-body">
                    @if(isset($news) && $news->count() > 0)
                        @foreach($news as $item)
                            <div class="news-list-item">
                                <div class="news-thumb">
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" loading="lazy">
                                    @else
                                        <div class="news-thumb-empty">📰</div>
                                    @endif
                                </div>
                                <div class="news-content">
                                    <h4>
                                        @if($item->is_pinned) <span class="pin-badge">📌 مهم</span> @endif
                                        <a href="{{ route('section.news.show', $item->slug) }}">{{ $item->title }}</a>
                                    </h4>
                                    <div class="meta">🗓 {{ $item->published_at ? jalali($item->published_at) : jalali($item->created_at) }}</div>
                                    <p>{{ Illuminate\Support\Str::limit($item->excerpt ?: strip_tags($item->content), 150) }}</p>
                                </div>
                            </div>
                        @endforeach
                        <div style="text-align:center; padding-top:16px;">
                            <a href="/news" style="color:#2e7d32; text-decoration:none; font-size:13px;">مشاهده همه اخبار ←</a>
                        </div>
                    @else
                        <div class="empty-note">هنوز خبری منتشر نشده است.</div>
                    @endif
                </div>
            </div>

            {{-- گالری تصاویر --}}
            @if(isset($gallery) && count($gallery) > 0)
                <div class="box">
                    <div class="box-header">
                        <h3>🖼 گالری تصاویر {{ $section->name }}</h3>
                        <div class="line"></div>
                    </div>
                    <div class="box-body">
                        <div class="gallery-grid-mini" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 12px;">
                            @foreach($gallery as $img)
                                <div class="gallery-item-mini" style="border-radius: 8px; overflow: hidden; background: #f5f7fa; box-shadow: 0 3px 10px rgba(0,0,0,0.06);">
                                    <img src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $img->caption ?: $section->name }}" loading="lazy" style="width: 100%; height: 120px; object-fit: cover; display: block;">
                                    @if($img->caption)
                                        <div style="padding: 8px 10px; font-size: 11.5px; color: #546e7a; background: #fff;">{{ $img->caption }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </main>


        {{-- سایدبار --}}
        <aside>
            @if($section->bakhshdar_name)
                <div class="box">
                    <div class="bakhshdar-side-card">
                        @if($section->bakhshdar_photo)
                            <img src="{{ asset('storage/' . $section->bakhshdar_photo) }}" alt="{{ $section->bakhshdar_name }}" class="photo">
                        @else
                            <div class="photo-ph">👤</div>
                        @endif
                        <br>
                        <span class="label">👤 بخشدار</span>
                        <h4>{{ $section->bakhshdar_name }}</h4>
                        <p class="role">بخشدار {{ $section->name }}</p>
                        <a href="{{ url('/bakhshdar') }}" class="btn">مشاهده پروفایل ←</a>
                    </div>
                </div>
            @endif

            <div class="box" id="villages">
                <div class="box-header">
                    <h3>🏘 روستاهای {{ $section->name }}</h3>
                    <div class="line"></div>
                </div>
                @if($villages->count() > 0)
                    <ul class="villages-side-list">
                        @foreach($villages as $village)
                            <li>
                                <a href="{{ route('public.village.show', $village->slug) }}" class="village-item-link">
                                    <span class="village-name-text">{{ $village->name }}</span>
                                    <span class="village-enter-text">
                                        <span class="enter-label">ورود</span>
                                        <span class="enter-arrow">←</span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="empty-note">هنوز روستایی ثبت نشده است.</div>
                @endif
            </div>
        </aside>
    </div>
</div>

{{-- ═══ بخش معرفی و همکاران (پایین صفحه) ═══ --}}
<div class="s-container" style="margin-bottom: 30px;">
    <div class="box" style="background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden;">
        <div class="box-header" style="background: #0d3b2e; color: white; padding: 20px 25px;">
            <h3 style="color: #ffd54f; margin: 0; font-size: 18px;">🌟 معرفی بخش {{ $section->name }}</h3>
        </div>
        <div class="box-body" style="padding: 25px;">
            <div style="font-size: 14.5px; line-height: 2.2; color: #37474f; text-align: justify; margin-bottom: 30px;">
                @if($section->description) {!! nl2br(e($section->description)) !!} @else <span style="color: #94a3b8; font-style: italic;">توضیحات این بخش در حال تکمیل است.</span> @endif
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 30px;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; border-top: 4px solid #2e7d32;">
                    <h4 style="color: #2e7d32; margin: 0 0 15px; font-size: 15px;">🏢 امکانات و خدمات</h4>
                    <div style="color: #546e7a; font-size: 13.5px; line-height: 2;">
                        @if($section->facilities) {!! nl2br(e($section->facilities)) !!} @else <span style="color: #94a3b8; font-style: italic;">در حال تکمیل...</span> @endif
                    </div>
                </div>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; border-top: 4px solid #d97706;">
                    <h4 style="color: #d97706; margin: 0 0 15px; font-size: 15px;">🏛 جاذبه‌های تاریخی و زیارتی</h4>
                    <div style="color: #546e7a; font-size: 13.5px; line-height: 2;">
                        @if($section->historical_sites) {!! nl2br(e($section->historical_sites)) !!} @else <span style="color: #94a3b8; font-style: italic;">در حال تکمیل...</span> @endif
                    </div>
                </div>
            </div>
            @if($section->population_count > 0)
            <div style="text-align: center; background-color: #e8f5e9 !important; border-radius: 12px; padding: 25px; border: 2px dashed #a5d6a7 !important; margin-top: 20px;">
                <div style="font-size: 16px; color: #2e7d32 !important; font-weight: bold; margin-bottom: 15px;">👥 جمعیت کل بخش</div>
                <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                    <span class="counter-value" data-target="{{ $section->population_count }}" style="font-size: 50px !important; font-weight: 900 !important; color: #0d3b2e !important; direction: ltr !important; background: transparent !important; line-height: 1 !important; display: inline-block;">0</span>
                    <span style="font-size: 16px; color: #64748b !important; font-weight: bold;">نفر</span>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@if($section->staffs && $section->staffs->count() > 0)
<div class="s-container" style="margin-bottom: 25px;">
    <div class="box">
        <div class="box-header">
            <h3>👥 کارکنان بخشداری {{ $section->name }}</h3>
            <div class="line"></div>
        </div>
        <div class="box-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
                @foreach($section->staffs as $staff)
                    <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 15px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                        @if($staff->photo)
                            <img src="{{ asset('storage/' . $staff->photo) }}" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin: 0 auto 15px; border: 3px solid #e8f5e9; display: block;">
                        @else
                            <div style="width: 100px; height: 100px; border-radius: 50%; background: #f0f3f7; display: flex; align-items: center; justify-content: center; font-size: 45px; color: #94a3b8; margin: 0 auto 15px; border: 3px solid #e8f5e9;">👤</div>
                        @endif
                        <div style="font-weight: 800 !important; color: #0d3b2e !important; background: transparent !important; margin-bottom: 6px; font-size: 16px; display: block;">{{ $staff->name }}</div>
                        <div style="font-size: 13px !important; color: #64748b !important; background: transparent !important; margin-bottom: 15px; display: block;">{{ $staff->position }}</div>
                        @if($staff->phone)
                            <a href="tel:{{ $staff->phone }}" style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; background: #f1f8e9; color: #2e7d32; padding: 8px 16px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 700; direction: ltr; width: 100%;">📞 {{ $staff->phone }}</a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

{{-- باکس درخواست مردمی --}}

    {{-- باکس ورود کارشناسان --}}
    <style>
        .expert-login-widget-final {
            background-color: #ffffff !important;
            border: 2px solid #2e7d32 !important;
            border-radius: 12px !important;
            padding: 20px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: wrap !important;
            gap: 15px !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05) !important;
            font-family: Tahoma, Arial, sans-serif !important;
        }
        .expert-login-widget-final .expert-text {
            font-size: 15px !important;
            color: #333333 !important;
            line-height: 1.8 !important;
        }
        .expert-login-widget-final .expert-text strong {
            color: #2e7d32 !important;
            font-weight: bold !important;
            font-size: 16px !important;
        }
        .expert-login-widget-final .expert-btn {
            display: inline-block !important;
            background-color: #2e7d32 !important;
            color: #ffffff !important;
            padding: 12px 25px !important;
            border-radius: 8px !important;
            text-decoration: none !important;
            font-weight: bold !important;
            font-size: 14px !important;
            white-space: nowrap !important;
            transition: 0.3s !important;
        }
        .expert-login-widget-final .expert-btn:hover {
            background-color: #1b5e20 !important;
            transform: translateY(-2px) !important;
        }
    </style>

    <div class="s-container" style="margin-bottom: 30px;">
        <div class="expert-login-widget-final">
            <div class="expert-text">
                <strong>🔐 کارشناسان فنی و مالی دهیاری‌ها:</strong><br>
                برای ثبت، ویرایش و مدیریت تعرفه‌ی عوارض روستاهای این بخش وارد شوید.
            </div>
            <a href="{{ route('expert.login') }}" class="expert-btn">ورود کارشناسان ←</a>
        </div>
    </div>



<script>
    document.addEventListener("DOMContentLoaded", () => {
        const counter = document.querySelector('.counter-value');
        if (!counter) return;
        const target = +counter.getAttribute('data-target');
        if (target > 0) {
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;
            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    counter.innerText = Math.ceil(current).toLocaleString('fa-IR');
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.innerText = target.toLocaleString('fa-IR');
                }
            };
            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting) {
                    updateCounter();
                    observer.disconnect();
                }
            }, { threshold: 0.5 });
            observer.observe(counter);
        }
    });
</script>

@include('partials.gov-footer')
@include('partials.back-to-top')
@include('partials.analytics')
</body>
</html>

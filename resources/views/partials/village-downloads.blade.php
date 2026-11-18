{{--
    بخش «فایل‌های قابل دانلود» روستا
    در پایین صفحه‌ی «ثبت درخواست» نمایش داده می‌شود.

    ورودی: $downloads  (کالکشن VillageDownload)
    ورودی: $village
--}}
@if(isset($downloads) && $downloads->isNotEmpty())
    <div class="vd-section" style="margin-top:36px;">

        <h2 style="display:flex; align-items:center; gap:9px; font-size:18px; color:#8e2f18;
                   margin:0 0 6px; padding-bottom:12px; border-bottom:2px solid #fdeee7;">
            📥 فایل‌ها و فرم‌های قابل دانلود
        </h2>
        <p style="font-size:12.5px; color:#8a7a6a; margin:0 0 16px; line-height:1.9;">
            فایل‌های زیر توسط دهیاری روستای <strong>{{ $village->name }}</strong> برای استفاده‌ی شما قرار داده شده است.
        </p>

        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:12px;">
            @foreach($downloads as $d)
                <a href="{{ route('village.site.download', [$village->slug, $d->id]) }}"
                   class="vd-card"
                   style="display:flex; align-items:flex-start; gap:12px; background:#fff;
                          border:1.5px solid #f2e4cd; border-radius:12px; padding:14px;
                          text-decoration:none; color:inherit;
                          transition:border-color .2s ease, box-shadow .2s ease, transform .2s ease;">

                    <div style="width:44px; height:44px; border-radius:10px; background:#fdeee7;
                                display:flex; align-items:center; justify-content:center;
                                font-size:22px; flex-shrink:0;">
                        {{ $d->icon() }}
                    </div>

                    <div style="flex:1; min-width:0;">
                        <div style="font-weight:700; color:#8e2f18; font-size:13.5px; line-height:1.6;">
                            {{ $d->title }}
                        </div>

                        @if($d->description)
                            <div style="font-size:11.5px; color:#8a7a6a; margin-top:3px; line-height:1.7;">
                                {{ $d->description }}
                            </div>
                        @endif

                        <div style="display:flex; flex-wrap:wrap; gap:9px; align-items:center;
                                    margin-top:8px; font-size:11px; color:#a2948a;">
                            <span>📦 {{ $d->sizeLabel() }}</span>
                            @if($d->download_count > 0)
                                <span>⬇️ {{ \App\Support\Number::fa(number_format($d->download_count)) }} بار دانلود</span>
                            @endif
                        </div>
                    </div>

                    <div style="flex-shrink:0; color:#c94a2b; font-size:18px; align-self:center;">⬇️</div>
                </a>
            @endforeach
        </div>
    </div>

    <style>
        .vd-card:hover {
            border-color: #c94a2b !important;
            box-shadow: 0 8px 26px rgba(201, 74, 43, .13) !important;
            transform: translateY(-2px);
        }
        @media (max-width: 640px) {
            .vd-section > div[style*="grid"] {
                grid-template-columns: 1fr !important;
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .vd-card:hover { transform: none !important; }
        }
    </style>
@endif

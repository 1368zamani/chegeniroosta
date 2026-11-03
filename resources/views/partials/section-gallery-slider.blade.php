@php
    if (!isset($galleryImages)) {
        $galleryImages = \App\Models\SectionGallery::where('section_id', $section->id)
            ->orderBy('sort_order')
            ->get();
    }
@endphp

@if($galleryImages->count() > 0)
<style>
    .s-slider {
        position: relative;
        width: 100%;
        height: 420px;
        overflow: hidden;
        background: #0d3b2e;
    }
    .s-slide {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        opacity: 0;
        transition: opacity 1.2s ease-in-out;
        z-index: 1;
    }
    .s-slide.active { opacity: 1; z-index: 2; }
    .s-slide::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg,
            rgba(13,46,79,0.15) 0%,
            rgba(13,46,79,0) 40%,
            rgba(13,46,79,0.65) 100%);
    }
    .s-slide-caption {
        position: absolute;
        bottom: 65px;
        right: 30px;
        z-index: 3;
        background: rgba(13,46,79,0.78);
        -webkit-backdrop-filter: blur(10px);
        backdrop-filter: blur(10px);
        color: #fff;
        padding: 10px 20px;
        border-radius: 8px;
        border-right: 3px solid #ffd54f;
        font-family: 'Vazirmatn', Tahoma, sans-serif;
        font-size: 15px;
        font-weight: 600;
        max-width: 70%;
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }
    .s-slider-dots {
        position: absolute;
        bottom: 25px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 8px;
        z-index: 5;
    }
    .s-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        background: rgba(255,255,255,0.55);
        cursor: pointer;
        transition: all .3s ease;
        border: none;
        padding: 0;
    }
    .s-dot.active {
        background: #ffd54f;
        width: 32px;
        border-radius: 5px;
        box-shadow: 0 0 12px rgba(255,213,79,0.6);
    }
    .s-slider-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 5;
        background: rgba(13,46,79,0.6);
        -webkit-backdrop-filter: blur(6px);
        backdrop-filter: blur(6px);
        color: #fff;
        border: none;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 22px;
        line-height: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity .3s ease, background .3s;
    }
    .s-slider:hover .s-slider-nav { opacity: 1; }
    .s-slider-nav:hover {
        background: rgba(255,213,79,0.85);
        color: #0d3b2e;
    }
    .s-slider-prev { right: 20px; }
    .s-slider-next { left: 20px; }
    @media (max-width: 700px) {
        .s-slider { height: 260px; }
        .s-slide-caption {
            font-size: 12.5px;
            padding: 8px 14px;
            bottom: 55px;
            right: 15px;
            max-width: 85%;
        }
        .s-slider-nav {
            width: 36px;
            height: 36px;
            font-size: 18px;
            opacity: 1;
        }
        .s-dot { width: 8px; height: 8px; }
        .s-dot.active { width: 24px; }
    }
</style>

<div class="s-slider" id="sGallerySlider">
    @foreach($galleryImages as $i => $img)
        <div class="s-slide {{ $i === 0 ? 'active' : '' }}"
             style="background-image: url('{{ asset('storage/' . $img->image_path) }}');">
            @if($img->caption)
                <div class="s-slide-caption">{{ $img->caption }}</div>
            @endif
        </div>
    @endforeach

    @if($galleryImages->count() > 1)
        <button class="s-slider-nav s-slider-prev" id="sSliderPrev" aria-label="قبلی">‹</button>
        <button class="s-slider-nav s-slider-next" id="sSliderNext" aria-label="بعدی">›</button>
        <div class="s-slider-dots" id="sSliderDots">
            @foreach($galleryImages as $i => $img)
                <button type="button" class="s-dot {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}"></button>
            @endforeach
        </div>
    @endif
</div>

@if($galleryImages->count() > 1)
<script>
(function(){
    var slider = document.getElementById('sGallerySlider');
    if (!slider) return;
    var slides = slider.querySelectorAll('.s-slide');
    var dots = slider.querySelectorAll('.s-dot');
    var prevBtn = document.getElementById('sSliderPrev');
    var nextBtn = document.getElementById('sSliderNext');
    var total = slides.length;
    var current = 0;
    var interval;
    var INTERVAL_MS = 5000;

    function show(idx) {
        if (idx < 0) idx = total - 1;
        if (idx >= total) idx = 0;
        slides.forEach(function(s, i) { s.classList.toggle('active', i === idx); });
        dots.forEach(function(d, i) { d.classList.toggle('active', i === idx); });
        current = idx;
    }
    function next() { show(current + 1); }
    function prev() { show(current - 1); }
    function start() { stop(); interval = setInterval(next, INTERVAL_MS); }
    function stop() { if (interval) clearInterval(interval); }

    if (nextBtn) nextBtn.addEventListener('click', function(){ next(); start(); });
    if (prevBtn) prevBtn.addEventListener('click', function(){ prev(); start(); });
    dots.forEach(function(d) {
        d.addEventListener('click', function() {
            show(parseInt(this.dataset.index, 10));
            start();
        });
    });
    slider.addEventListener('mouseenter', stop);
    slider.addEventListener('mouseleave', start);

    var startX = 0;
    slider.addEventListener('touchstart', function(e) { startX = e.touches[0].clientX; }, { passive: true });
    slider.addEventListener('touchend', function(e) {
        var dx = e.changedTouches[0].clientX - startX;
        if (Math.abs(dx) < 50) return;
        if (dx > 0) prev(); else next();
        start();
    });
    start();
})();
</script>
@endif
@endif

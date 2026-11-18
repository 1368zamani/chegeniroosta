{{-- ═══════════════════════════════════════════════════════════════════════
     اسلایدر خودکار کارت‌های خبر (NEWS-SLIDER-V3)
     هر .news-slider را فعال می‌کند: پیمایش خودکار، فلش‌ها، نقطه‌ها،
     کشیدن لمسی و توقف روی نگه‌داشتن نشانگر. متن و تصویر کارت‌ها
     هیچ تغییری نمی‌کند.
     ═══════════════════════════════════════════════════════════════════════ --}}
<script>
(function () {
    'use strict';
    var sliders = document.querySelectorAll('.news-slider');
    if (!sliders.length) return;
    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    Array.prototype.forEach.call(sliders, function (slider) {
        var viewport = slider.querySelector('.news-viewport');
        var track    = slider.querySelector('.cards');
        var nav      = slider.querySelector('.news-nav');
        var dotsWrap = slider.querySelector('.news-dots');
        var prevBtn  = slider.querySelector('.news-prev');
        var nextBtn  = slider.querySelector('.news-next');
        if (!viewport || !track) return;

        var cards = Array.prototype.slice.call(track.children);
        if (!cards.length) return;

        var rtl   = getComputedStyle(slider).direction === 'rtl';
        var sign  = rtl ? 1 : -1;
        var index = 0, perView = 1, pages = 1, step = 0, timer = null;
        var delay = parseInt(slider.getAttribute('data-autoplay'), 10);
        if (isNaN(delay)) delay = 5000;

        function measure() {
            var pv = parseFloat(getComputedStyle(slider).getPropertyValue('--per'));
            perView = (pv && pv > 0) ? Math.round(pv) : 1;
            if (perView > cards.length) perView = cards.length;
            var cs  = getComputedStyle(track);
            var gap = parseFloat(cs.columnGap);
            if (isNaN(gap)) gap = parseFloat(cs.gap);
            if (isNaN(gap)) gap = 0;
            var w = cards[0].getBoundingClientRect().width;
            step  = w + gap;
            pages = Math.max(1, Math.ceil(cards.length / perView));
        }

        function offsetFor(i) {
            var max = Math.max(0, cards.length - perView);
            return Math.min(i * perView, max);
        }

        function buildDots() {
            if (!dotsWrap) return;
            dotsWrap.innerHTML = '';
            if (pages <= 1) return;
            for (var i = 0; i < pages; i++) {
                var b = document.createElement('button');
                b.type = 'button';
                b.className = 'news-dot';
                b.setAttribute('aria-label', 'صفحهٔ ' + (i + 1));
                (function (n) { b.addEventListener('click', function () { go(n); restart(); }); })(i);
                dotsWrap.appendChild(b);
            }
        }

        function syncDots() {
            if (!dotsWrap) return;
            var ds = dotsWrap.children;
            for (var i = 0; i < ds.length; i++) ds[i].classList.toggle('active', i === index);
        }

        function update(animate) {
            track.style.transition = animate === false ? 'none' : '';
            track.style.transform  = 'translateX(' + (sign * offsetFor(index) * step) + 'px)';
            syncDots();
            if (nav) nav.style.display = pages > 1 ? '' : 'none';
        }

        function go(n) {
            index = ((n % pages) + pages) % pages;
            update(true);
        }
        function nextSlide() { go(index + 1); }
        function prevSlide() { go(index - 1); }

        function stop() { if (timer) { clearInterval(timer); timer = null; } }
        function start() {
            stop();
            if (reduce || pages <= 1 || delay <= 0) return;
            timer = window.setInterval(nextSlide, delay);
        }
        function restart() { start(); }

        if (prevBtn) prevBtn.addEventListener('click', function () { prevSlide(); restart(); });
        if (nextBtn) nextBtn.addEventListener('click', function () { nextSlide(); restart(); });

        slider.addEventListener('mouseenter', stop);
        slider.addEventListener('mouseleave', start);
        slider.addEventListener('focusin', stop);
        slider.addEventListener('focusout', function (e) {
            if (!slider.contains(e.relatedTarget)) start();
        });
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) stop(); else start();
        });

        /* کشیدن لمسی */
        var sx = 0, sy = 0, swiping = false;
        viewport.addEventListener('touchstart', function (e) {
            if (e.touches.length !== 1) return;
            sx = e.touches[0].clientX; sy = e.touches[0].clientY;
            swiping = true; stop();
        }, { passive: true });
        viewport.addEventListener('touchend', function (e) {
            if (!swiping) return;
            swiping = false;
            var t  = e.changedTouches[0];
            var dx = t.clientX - sx, dy = t.clientY - sy;
            if (Math.abs(dx) > 42 && Math.abs(dx) > Math.abs(dy)) {
                var toNext = rtl ? (dx > 0) : (dx < 0);
                if (toNext) nextSlide(); else prevSlide();
            }
            start();
        }, { passive: true });

        /* بازمحاسبه در تغییر اندازه */
        var rt = null;
        window.addEventListener('resize', function () {
            if (rt) clearTimeout(rt);
            rt = setTimeout(function () {
                measure();
                if (index >= pages) index = pages - 1;
                buildDots();
                update(true);
            }, 150);
        });

        measure();
        buildDots();
        update(false);
        requestAnimationFrame(function () { track.style.transition = ''; });
        start();
    });
})();
</script>

<script>
(function () {
    'use strict';
    var boxes = document.querySelectorAll('.feat-big');
    if (!boxes.length) return;
    Array.prototype.forEach.call(boxes, function (box) {
        var slides = Array.prototype.slice.call(box.querySelectorAll('.feat-slide'));
        if (!slides.length) return;
        var dots = box.querySelectorAll('.feat-dot');
        var idx = 0;
        function go(i) {
            i = ((i % slides.length) + slides.length) % slides.length;
            for (var j = 0; j < slides.length; j++) slides[j].classList.toggle('active', j === i);
            for (var k = 0; k < dots.length; k++) dots[k].classList.toggle('active', k === i);
            idx = i;
        }
        var delay = parseInt(box.getAttribute('data-autoplay'), 10) || 3000;
        var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var t = reduce ? null : window.setInterval(function () { go(idx + 1); }, delay);
        Array.prototype.forEach.call(dots, function (d) {
            d.addEventListener('click', function () { go(parseInt(d.getAttribute('data-feat-idx'), 10)); });
        });
        box.addEventListener('mouseenter', function () { window.clearInterval(t); });
        box.addEventListener('mouseleave', function () { window.clearInterval(t); if (!reduce) { t = window.setInterval(function () { go(idx + 1); }, delay); } });
    });
})();
</script>

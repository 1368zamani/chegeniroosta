{{--
    ═══════════════════════════════════════════════════════════════
    انیمیشن‌های عمومی سایت
    ─────────────────────────────────────────────────────────────
    کافیست این پارشال را داخل <head> هر layout یا صفحه اضافه کنی.
    خودش عناصر رایج را پیدا می‌کند و انیمیشن ظاهر شدن را اعمال می‌کند.

    • اگر جاوااسکریپت نباشد → هیچ محتوایی مخفی نمی‌ماند
    • اگر «کاهش حرکت» فعال باشد → همه‌ی حرکت‌ها خاموش می‌شوند
    • شبکه‌ی ایمنی ۲.۶ ثانیه‌ای → همه‌چیز حتماً نمایش داده می‌شود
    ═══════════════════════════════════════════════════════════════
--}}
<link rel="stylesheet" href="{{ asset('css/site-animations.css') }}?v=1">
<script>
document.documentElement.classList.add('js-anim');

(function () {
    'use strict';

    /* عناصری که به‌صورت خودکار انیمیت می‌شوند */
    var AUTO = [
        '.anim-reveal',
        '.page-title',
        '.section-card',
        '.village-card',
        '.v-news-card',
        '.v-project-card',
        '.v-council-card',
        '.v-attraction-card',
        '.v-facility-card',
        '.cf-news-item',
        '.news-card',
        '.news-list-item',
        '.dehyar-side-card',
        '.bakhshdar-side-card',
        '.info-bar',
        '.stat-card'
    ];

    function init() {
        var list;

        try {
            list = document.querySelectorAll(AUTO.join(','));
        } catch (e) {
            return;
        }

        if (!list || !list.length) {
            return;
        }

        var els = Array.prototype.slice.call(list);

        /* افزودن کلاس انیمیشن */
        els.forEach(function (el) { el.classList.add('anim-reveal'); });

        function revealAll() {
            els.forEach(function (el) { el.classList.add('is-visible'); });
        }

        var reduce = window.matchMedia
            && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (reduce || !('IntersectionObserver' in window)) {
            revealAll();
            return;
        }

        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.05, rootMargin: '0px 0px -30px 0px' });

        els.forEach(function (el) {
            /* عناصر داخل نواحی مخفی (تب بسته) رصد نمی‌شوند — فوری نشان داده شوند */
            if (el.offsetParent === null) {
                el.classList.add('is-visible');
            } else {
                io.observe(el);
            }
        });

        /* شبکه‌ی ایمنی */
        setTimeout(revealAll, 2600);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>

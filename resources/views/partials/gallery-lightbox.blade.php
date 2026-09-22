<style>
.v-glb-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.9);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    animation: vglb-fade .2s;
}
.v-glb-overlay.active { display: flex; }
@keyframes vglb-fade { from{opacity:0} to{opacity:1} }

.v-glb-content {
    position: relative;
    max-width: 90vw;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.v-glb-image {
    max-width: 90vw;
    max-height: 75vh;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0,0,0,.5);
}
.v-glb-caption {
    background: rgba(255,255,255,.95);
    color: #1e293b;
    padding: .9rem 1.2rem;
    border-radius: 8px;
    margin-top: 1rem;
    max-width: 90vw;
    text-align: center;
    font-family: 'Vazirmatn', Tahoma, sans-serif;
    direction: rtl;
}
.v-glb-caption h4 { color: #1565c0; margin-bottom: .35rem; font-size: 1.05rem; }
.v-glb-caption p { color: #475569; font-size: .9rem; line-height: 1.6; }
.v-glb-close {
    position: absolute;
    top: -50px;
    left: 0;
    background: rgba(255,255,255,.15);
    color: #fff;
    border: none;
    font-size: 2rem;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    cursor: pointer;
    line-height: 1;
    transition: background .15s;
}
.v-glb-close:hover { background: rgba(255,255,255,.3); }
.v-glb-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255,255,255,.15);
    color: #fff;
    border: none;
    font-size: 2rem;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    cursor: pointer;
    line-height: 1;
    transition: background .15s;
}
.v-glb-nav:hover { background: rgba(255,255,255,.3); }
.v-glb-prev { right: -60px; }
.v-glb-next { left: -60px; }
.v-glb-counter {
    position: absolute;
    top: -50px;
    right: 0;
    color: #fff;
    font-size: .9rem;
    font-family: 'Vazirmatn', Tahoma, sans-serif;
    direction: ltr;
}

/* موبایل */
@media (max-width: 720px) {
    .v-glb-prev { right: 8px; }
    .v-glb-next { left: 8px; }
    .v-glb-close { top: 8px; left: 8px; }
    .v-glb-counter { top: 8px; right: 8px; }
    .v-glb-image { max-height: 65vh; }
}

/* گالری: کرسر و افکت کلیک */
.v-gallery-item { cursor: pointer; position: relative; }
.v-gallery-item::after {
    content: '🔍';
    position: absolute;
    top: 10px;
    left: 10px;
    background: rgba(0,0,0,.6);
    color: #fff;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    opacity: 0;
    transition: opacity .2s;
    pointer-events: none;
}
.v-gallery-item:hover::after { opacity: 1; }
</style>

<div class="v-glb-overlay" id="vGlb" role="dialog" aria-modal="true">
    <div class="v-glb-content">
        <span class="v-glb-counter" id="vGlbCounter"></span>
        <button class="v-glb-close" id="vGlbClose" aria-label="بستن">×</button>
        <button class="v-glb-nav v-glb-prev" id="vGlbPrev" aria-label="قبلی">‹</button>
        <img src="" alt="" class="v-glb-image" id="vGlbImage">
        <button class="v-glb-nav v-glb-next" id="vGlbNext" aria-label="بعدی">›</button>
        <div class="v-glb-caption" id="vGlbCaption" style="display:none">
            <h4 id="vGlbTitle"></h4>
            <p id="vGlbDesc"></p>
        </div>
    </div>
</div>

<script>
(function(){
    var overlay = document.getElementById('vGlb');
    if (!overlay) return;

    var img     = document.getElementById('vGlbImage');
    var titleEl = document.getElementById('vGlbTitle');
    var descEl  = document.getElementById('vGlbDesc');
    var capEl   = document.getElementById('vGlbCaption');
    var counter = document.getElementById('vGlbCounter');

    var items = [];
    var current = 0;

    function collect() {
        items = [];
        document.querySelectorAll('.v-gallery-item[data-image-src]').forEach(function(el){
            items.push({
                src:   el.getAttribute('data-image-src'),
                title: el.getAttribute('data-title') || '',
                desc:  el.getAttribute('data-description') || ''
            });
        });
    }

    function show(index) {
        if (!items.length) return;
        if (index < 0) index = items.length - 1;
        if (index >= items.length) index = 0;
        current = index;
        var it = items[current];
        img.src = it.src;
        img.alt = it.title || '';
        titleEl.textContent = it.title;
        descEl.textContent  = it.desc;
        capEl.style.display = (it.title || it.desc) ? 'block' : 'none';
        counter.textContent = (current + 1) + ' / ' + items.length;
    }

    function open(index) {
        collect();
        show(index);
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function close() {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    function next() { show(current + 1); }
    function prev() { show(current - 1); }

    // اتصال به آیتم‌های گالری
    document.addEventListener('click', function(e){
        var item = e.target.closest('.v-gallery-item[data-image-src]');
        if (!item) return;
        e.preventDefault();
        var all = Array.from(document.querySelectorAll('.v-gallery-item[data-image-src]'));
        open(all.indexOf(item));
    });

    document.getElementById('vGlbClose').addEventListener('click', close);
    document.getElementById('vGlbNext').addEventListener('click', next);
    document.getElementById('vGlbPrev').addEventListener('click', prev);

    overlay.addEventListener('click', function(e){
        if (e.target === overlay) close();
    });

    document.addEventListener('keydown', function(e){
        if (!overlay.classList.contains('active')) return;
        if (e.key === 'Escape')     close();
        if (e.key === 'ArrowLeft')  next();
        if (e.key === 'ArrowRight') prev();
    });

    // سوایپ موبایل
    var startX = 0;
    overlay.addEventListener('touchstart', function(e){ startX = e.touches[0].clientX; }, {passive:true});
    overlay.addEventListener('touchend', function(e){
        var dx = e.changedTouches[0].clientX - startX;
        if (Math.abs(dx) < 50) return;
        if (dx > 0) prev(); else next();
    });
})();
</script>

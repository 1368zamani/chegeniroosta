@if(isset($news) && $news->count() > 0)
<style>
    .news-ticker {
        background: linear-gradient(90deg, #0d2e4f 0%, #103d68 50%, #0d2e4f 100%);
        color: #fff;
        height: 52px;
        overflow: hidden;
        position: relative;
        border-top: 2px solid #ffd54f;
        border-bottom: 2px solid #ffd54f;
        box-shadow: 0 4px 15px rgba(0,0,0,0.18);
        display: flex;
        align-items: stretch;
        font-family: 'Vazirmatn', Tahoma, sans-serif;
    }
    .news-ticker-label {
        background: #ffd54f;
        color: #0d2e4f;
        padding: 0 18px;
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 800;
        font-size: 13.5px;
        flex-shrink: 0;
    }
    .news-ticker-viewport {
        flex: 1;
        position: relative;
        overflow: hidden;
    }
    .news-ticker-item {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        padding: 0 20px;
        opacity: 0;
        transform: translateY(100%);
        transition: opacity .55s ease, transform .55s ease;
    }
    .news-ticker-item.active {
        opacity: 1;
        transform: translateY(0);
        z-index: 2;
    }
    .news-ticker-item.prev {
        opacity: 0;
        transform: translateY(-100%);
    }
    .news-ticker-item a {
        color: #fff;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        overflow: hidden;
        white-space: nowrap;
        font-size: 14px;
        font-weight: 600;
    }
    .news-ticker-item a:hover { color: #ffd54f; }
    .news-ticker-item .title {
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .news-ticker-item .badge {
        background: #e53935;
        color: #fff;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 10.5px;
        font-weight: 700;
        flex-shrink: 0;
    }
    .news-ticker-item .date {
        color: #90a4ae;
        font-size: 11.5px;
        margin-right: auto;
        flex-shrink: 0;
    }
    .news-ticker-next {
        background: rgba(255,255,255,0.08);
        color: #ffd54f;
        border: none;
        width: 44px;
        cursor: pointer;
        font-size: 20px;
        flex-shrink: 0;
        transition: background .2s;
        font-family: inherit;
    }
    .news-ticker-next:hover {
        background: rgba(255,213,79,0.25);
    }
    @media (max-width: 700px) {
        .news-ticker { height: 46px; }
        .news-ticker-label { padding: 0 12px; font-size: 12px; }
        .news-ticker-item { padding: 0 12px; }
        .news-ticker-item a { font-size: 12.5px; gap: 6px; }
        .news-ticker-item .date { display: none; }
        .news-ticker-next { width: 36px; font-size: 16px; }
    }
</style>

<div class="news-ticker" id="newsTicker">
    <div class="news-ticker-label">
        <span>📢</span>
        <span>آخرین اخبار</span>
    </div>

    <div class="news-ticker-viewport" id="newsTickerViewport">
        @foreach($news as $i => $item)
            <div class="news-ticker-item {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}">
                <a href="{{ route('section.news.show', $item->slug) }}">
                    @if($item->is_pinned)
                        <span class="badge">📌 مهم</span>
                    @endif
                    <span class="title">{{ $item->title }}</span>
                    <span class="date">{{ $item->published_at ? $item->published_at->format('Y/m/d') : $item->created_at->format('Y/m/d') }}</span>
                </a>
            </div>
        @endforeach
    </div>

    @if($news->count() > 1)
        <button type="button" class="news-ticker-next" id="newsTickerNext" aria-label="خبر بعدی">‹</button>
    @endif
</div>

@if($news->count() > 1)
<script>
(function(){
    var ticker = document.getElementById('newsTicker');
    var viewport = document.getElementById('newsTickerViewport');
    if (!ticker || !viewport) return;
    var items = viewport.querySelectorAll('.news-ticker-item');
    if (items.length < 2) return;

    var total = items.length;
    var current = 0;
    var interval;
    var INTERVAL_MS = 4000;

    function show(idx) {
        if (idx < 0) idx = total - 1;
        if (idx >= total) idx = 0;
        items.forEach(function(el, i) {
            el.classList.remove('active', 'prev');
            if (i === idx) {
                el.classList.add('active');
            } else if (i === current) {
                el.classList.add('prev');
            }
        });
        current = idx;
    }
    function next() { show(current + 1); }
    function prev() { show(current - 1); }
    function start() { stop(); interval = setInterval(next, INTERVAL_MS); }
    function stop() { if (interval) clearInterval(interval); }

    var nextBtn = document.getElementById('newsTickerNext');
    if (nextBtn) nextBtn.addEventListener('click', function(){ next(); start(); });

    ticker.addEventListener('mouseenter', stop);
    ticker.addEventListener('mouseleave', start);

    start();
})();
</script>
@endif
@endif

{{-- Back to top button --}}
<button id="backToTop" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" title="بازگشت به بالا" aria-label="بازگشت به بالا">
    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="18 15 12 9 6 15"></polyline>
    </svg>
</button>

<style>
    #backToTop {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 46px;
        height: 46px;
        background: linear-gradient(150deg, #e2603c, #c94a2b);
        color: #fff7e9;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 12px 26px -12px rgba(201, 74, 43, .8);
        opacity: 0;
        visibility: hidden;
        transition: all .3s;
        z-index: 999;
    }
    #backToTop.visible {
        opacity: 1;
        visibility: visible;
    }
    #backToTop:hover {
        filter: brightness(1.06);
        transform: translateY(-3px);
    }
    @media (max-width: 700px) {
        #backToTop { bottom: 20px; right: 20px; width: 42px; height: 42px; }
    }
</style>

<script>
(function() {
    const btn = document.getElementById('backToTop');
    if (!btn) return;

    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            btn.classList.add('visible');
        } else {
            btn.classList.remove('visible');
        }
    });
})();
</script>

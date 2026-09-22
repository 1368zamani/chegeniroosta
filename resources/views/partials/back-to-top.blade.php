{{-- Back to top button --}}
<button id="backToTop" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" title="بازگشت به بالا">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="18 15 12 9 6 15"></polyline>
    </svg>
</button>

<style>
    #backToTop {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 48px;
        height: 48px;
        background: #1565c0;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px rgba(21,101,192,0.35);
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
        background: #0d47a1;
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

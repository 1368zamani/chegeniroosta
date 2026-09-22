{{-- Dark Mode Toggle + Full Footer --}}

<style>
    /* Dark mode variables */
    :root {
        --bg-primary: #f5f7fa;
        --bg-secondary: #ffffff;
        --bg-tertiary: #f8fafc;
        --text-primary: #263238;
        --text-secondary: #546e7a;
        --text-muted: #90a4ae;
        --border-color: #eceff1;
        --shadow-color: rgba(0,0,0,0.06);
        --accent: #1565c0;
        --accent-dark: #0d47a1;
    }

    [data-theme="dark"] {
        --bg-primary: #0f1419;
        --bg-secondary: #1a1f2e;
        --bg-tertiary: #232a3d;
        --text-primary: #e8eaed;
        --text-secondary: #b0b8c1;
        --text-muted: #7a8591;
        --border-color: #2d3748;
        --shadow-color: rgba(0,0,0,0.4);
        --accent: #64b5f6;
        --accent-dark: #42a5f5;
    }

    [data-theme="dark"] body {
        background: var(--bg-primary) !important;
        color: var(--text-primary) !important;
    }

    [data-theme="dark"] .header {
        background: linear-gradient(135deg, #1a237e, #0d1b4d) !important;
    }

    [data-theme="dark"] .hero,
    [data-theme="dark"] .card,
    [data-theme="dark"] .stat-card,
    [data-theme="dark"] .news-card,
    [data-theme="dark"] .village-card,
    [data-theme="dark"] .drawer,
    [data-theme="dark"] .bakhshdar-card,
    [data-theme="dark"] .info-item,
    [data-theme="dark"] .description-card,
    [data-theme="dark"] .news-item,
    [data-theme="dark"] .article,
    [data-theme="dark"] .gallery-item {
        background: var(--bg-secondary) !important;
        color: var(--text-primary) !important;
        box-shadow: 0 5px 18px var(--shadow-color) !important;
    }

    [data-theme="dark"] .section-title h2,
    [data-theme="dark"] h1,
    [data-theme="dark"] h2,
    [data-theme="dark"] h3,
    [data-theme="dark"] h4 {
        color: var(--accent) !important;
    }

    [data-theme="dark"] p,
    [data-theme="dark"] .description,
    [data-theme="dark"] .excerpt {
        color: var(--text-secondary) !important;
    }

    [data-theme="dark"] .footer {
        background: #080c14 !important;
        color: var(--text-secondary) !important;
    }

    [data-theme="dark"] .nav a {
        color: white !important;
    }

    /* Dark mode toggle button */
    #darkModeToggle {
        position: fixed;
        top: 90px;
        left: 20px;
        width: 44px;
        height: 44px;
        background: var(--bg-secondary);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px var(--shadow-color);
        z-index: 998;
        transition: all .3s;
        font-size: 20px;
    }
    #darkModeToggle:hover {
        transform: scale(1.1);
    }
    @media (max-width: 700px) {
        #darkModeToggle { top: auto; bottom: 90px; left: 20px; width: 40px; height: 40px; font-size: 18px; }
    }
</style>

<button id="darkModeToggle" onclick="toggleDarkMode()" title="حالت تاریک/روشن">🌙</button>

<script>
(function() {
    // Check saved preference
    const saved = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const theme = saved || (prefersDark ? 'dark' : 'light');
    document.documentElement.setAttribute('data-theme', theme);
    updateIcon(theme);
})();

function toggleDarkMode() {
    const current = document.documentElement.getAttribute('data-theme') || 'light';
    const newTheme = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateIcon(newTheme);
}

function updateIcon(theme) {
    const btn = document.getElementById('darkModeToggle');
    if (btn) {
        btn.textContent = theme === 'dark' ? '☀️' : '🌙';
    }
}
</script>

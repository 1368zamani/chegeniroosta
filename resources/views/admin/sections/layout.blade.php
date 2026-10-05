    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li class="menu-label">منوی اصلی</li>
            <li>
                <a href="{{ url('/admin/sections/' . ($section->id ?? '') . '/dashboard') }}"
                   class="{{ request()->is('admin/sections/*/dashboard') ? 'active' : '' }}">
                    <span>📊</span> داشبورد بخشداری
                </a>
            </li>
            <li>
                <a href="{{ url('/admin/sections/' . ($section->id ?? '') . '/villages') }}"
                   class="{{ request()->is('admin/sections/*/villages') ? 'active' : '' }}">
                    <span>🏘</span> روستاهای بخش
                </a>
            </li>
            <li>
                <a href="{{ url('/admin/sections/' . ($section->id ?? '') . '/requests') }}"
                   class="{{ request()->is('admin/sections/*/requests*') ? 'active' : '' }}">
                    <span>📩</span> درخواست‌ها
                </a>
            </li>
            <li class="menu-divider"></li>
            <li class="menu-label">تعرفه و عوارض</li>
            <li>
                <a href="{{ route('admin.tariffs.index') }}"
                   class="{{ request()->routeIs('admin.tariffs.*') ? 'active' : '' }}">
                    <span>🧾</span> تعرفه‌ی عوارض
                </a>
            </li>
            <li>
                <a href="{{ url('/section/site/tariff') }}" target="_blank">
                    <span>🌐</span> نمای عمومی روستاها
                </a>
            </li>
            <li class="menu-divider"></li>
            <li class="menu-label">مدیریت</li>
            <li>
                <a href="{{ url('/admin/sections/' . ($section->id ?? '') . '/council') }}">
                    <span>👥</span> اعضای شورا
                </a>
            </li>
            <li>
                <a href="{{ url('/admin/section-news') }}">
                    <span>📰</span> اخبار بخشداری
                </a>
            </li>
            <li class="menu-divider"></li>
            <li>
                <a href="{{ url('/logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
                    <span>🚪</span> خروج
                </a>
                <form id="logout-form-sidebar" method="POST" action="{{ url('/logout') }}" style="display:none;">
                    @csrf
                </form>
            </li>
        </ul>
    </aside>
    </aside>

    <main class="content">
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</div>

</body>
</html>

<link rel="stylesheet" href="{{ asset('css/admin-master.css') }}?v=1791127161">

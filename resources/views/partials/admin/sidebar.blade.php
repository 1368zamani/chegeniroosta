{{-- ═══ ADMIN-SIDEBAR-WHITE-BLUE-2026 ═══ --}}
@php
    $u = auth()->user();
    $isSuper = $u?->isSuperAdmin() ?? false;
    $isSection = $u?->isSectionAdmin() ?? false;
    $isOfficer = $u?->isFeeOfficer() ?? false;
    $isVillage = $u?->isVillageAdmin() ?? false;
    $path = request()->path();
@endphp

<style>
.unified-sidebar {
    background: #ffffff;
    border: 1px solid #e8f5e9;
    border-radius: 14px;
    box-shadow: 0 4px 16px rgba(21,101,192,0.08);
    width: 240px;
    flex-shrink: 0;
    padding: 0;
    overflow: hidden;
    position: sticky;
    top: 16px;
    max-height: calc(100vh - 32px);
    overflow-y: auto;
}
.unified-sidebar::-webkit-scrollbar { width: 6px; }
.unified-sidebar::-webkit-scrollbar-thumb { background: rgba(21,101,192,0.2); border-radius: 3px; }

/* پروفایل بالای منو */
.unified-sidebar .sb-profile {
    background: #ffffff;
    padding: 18px 14px;
    text-align: center;
    border-bottom: 2px solid #e8f5e9;
}
.unified-sidebar .sb-profile .avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #2e7d32;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-size: 26px;
    font-weight: 900;
    box-shadow: 0 4px 14px rgba(21,101,192,0.3);
    border: 3px solid #e8f5e9;
}
.unified-sidebar .sb-profile .name {
    color: #2e7d32;
    font-size: 14px;
    font-weight: 800;
    margin: 0;
}
.unified-sidebar .sb-profile .role {
    color: #2e7d32;
    font-size: 11.5px;
    margin: 4px 0 0;
    font-weight: 700;
    opacity: 0.85;
}

.unified-sidebar .sb-menu {
    list-style: none;
    padding: 10px 8px;
    margin: 0;
    background: #ffffff;
}
.unified-sidebar .sb-section {
    font-size: 10.5px;
    color: #2e7d32;
    font-weight: 900;
    padding: 14px 12px 6px;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: 0.75;
}
.unified-sidebar .sb-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 14px;
    color: #2e7d32;
    text-decoration: none;
    border-radius: 9px;
    font-size: 13.5px;
    font-weight: 700;
    margin-bottom: 3px;
    transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    background: #ffffff;
    border: 1px solid transparent;
}
.unified-sidebar .sb-item::before {
    content: '';
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    width: 0;
    background: #2e7d32;
    transition: width 0.22s;
}
.unified-sidebar .sb-item:hover {
    background: #e8f5e9;
    color: #1b5e20;
    padding-right: 22px;
    border-color: #e8f5e9;
}
.unified-sidebar .sb-item:hover::before { width: 4px; }
.unified-sidebar .sb-item.active {
    background: #2e7d32;
    color: #ffffff !important;
    box-shadow: 0 3px 12px rgba(21,101,192,0.3);
}
.unified-sidebar .sb-item.active::before { width: 4px; background: #ffd54f; }
.unified-sidebar .sb-item.active .sb-icon { color: #ffd54f !important; }
.unified-sidebar .sb-item .sb-icon {
    width: 24px;
    text-align: center;
    font-size: 16px;
    flex-shrink: 0;
    color: #2e7d32;
}
.unified-sidebar .sb-item .sb-badge {
    background: #c62828;
    color: #ffffff;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 10.5px;
    font-weight: 900;
    margin-right: auto;
}
.unified-sidebar .sb-divider {
    height: 1px;
    background: #e8f5e9;
    margin: 10px 14px;
}

/* فرم خروج — بدون حاشیه، زمینه سفید */
.unified-sidebar .sb-menu form button.sb-item {
    background: #ffffff;
    border: 1px solid transparent;
    color: #2e7d32 !important;
}
.unified-sidebar .sb-menu form button.sb-item:hover {
    background: #e8f5e9;
    color: #1b5e20 !important;
}

@media (max-width: 900px) {
    .unified-sidebar {
        width: 100%;
        position: static;
        max-height: none;
        margin-bottom: 16px;
    }
    .unified-sidebar .sb-menu {
        display: flex;
        overflow-x: auto;
        flex-wrap: nowrap;
        gap: 6px;
    }
    .unified-sidebar .sb-section,
    .unified-sidebar .sb-divider { display: none; }
    .unified-sidebar .sb-item { flex-shrink: 0; }
}
</style>

<aside class="unified-sidebar">
    <div class="sb-profile">
        <div class="avatar">{{ mb_substr($u->name ?? 'U', 0, 1) }}</div>
        <p class="name">{{ $u->name ?? 'کاربر' }}</p>
        <p class="role">
            @if($isSuper) 🔴 سوپرادمین
            @elseif($isSection) 🟢 بخشدار {{ $u->section?->name ?? '' }}
            @elseif($isOfficer) 💰 کارشناس {{ $u->officer_type === 'financial' ? 'مالی' : 'عمرانی' }}
            @elseif($isVillage) 🏘 دهیار {{ $u->village?->name ?? '' }}
            @else کاربر
            @endif
        </p>
    </div>

    <ul class="sb-menu">
            <li></li>
            <li>
            </li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        @endif

        @if($isSuper || $isSection)
            <li class="sb-section">🏛 مدیریت بخش</li>
            @if($u?->section_id)
                <li><a href="{{ route('admin.sections.dashboard', $u->section_id) }}" class="sb-item"><span class="sb-icon">📊</span> داشبورد بخش</a></li>
                <li><a href="{{ route('admin.sections.villages', $u->section_id) }}" class="sb-item"><span class="sb-icon">🏘</span> روستاهای بخش</a></li>
                <li><a href="{{ route('admin.sections.council.index', $u->section_id) }}" class="sb-item"><span class="sb-icon">👥</span> شورای بخش</a></li>
                <li><a href="{{ route('admin.sections.requests.index', $u->section_id) }}" class="sb-item"><span class="sb-icon">📩</span> درخواست‌ها</a></li>
                <li><a href="{{ route('admin.sections.edit', $u->section_id) }}" class="sb-item"><span class="sb-icon">⚙️</span> تنظیمات بخش</a></li>
            @endif
        @endif

        @if($isSuper)
            <li class="sb-section">👥 کاربران</li>
            <li><a href="{{ route('admin.users.index') }}" class="sb-item"><span class="sb-icon">👤</span> کاربران</a></li>
            <li></li>
            <li></li>
        @endif

        @if($isSuper || $isSection)
            <li class="sb-section">📰 محتوا</li>
            <li><a href="{{ route('admin.section-news.index') }}" class="sb-item"><span class="sb-icon">📰</span> اخبار</a></li>
        @endif

        @if($isSuper || $isSection)
            <li class="sb-section">📊 گزارش‌ها</li>
            <li><a href="{{ route('admin.stats') }}" class="sb-item"><span class="sb-icon">📈</span> آمار سایت</a></li>
        @endif

        @if($isSuper || $isSection || $isVillage || $isOfficer)
            <li class="sb-section">💰 مالی و عوارض</li>
            <li>
                <a href="{{ route('admin.tariff-invoices.index') }}" class="sb-item">
                    <span class="sb-icon">📋</span> فیش‌های عوارض
                </a>
            </li>
            <li>
                <a href="{{ route('admin.tariff-invoices.account') }}" class="sb-item">
                    <span class="sb-icon">🏦</span> حساب بانکی دهیاری
                </a>
            </li>
            <li>
                <a href="{{ route('admin.tariffs.index') }}" class="sb-item">
                    <span class="sb-icon">📄</span> تعرفه‌ها
                </a>
            </li>
        @endif

        @if($isSuper)
            <li class="sb-section">⚙️ سیستم</li>
            <li><a href="/admin/settings" class="sb-item"><span class="sb-icon">⚙️</span> تنظیمات سایت</a></li>
            <li><a href="{{ route('admin.officials.index') }}" class="sb-item"><span class="sb-icon">🏛</span> مسئولین شهرستان</a></li>
        @endif

        <li class="sb-divider"></li>
        <li>
            <form method="POST" action="{{ route('logout') }}" style="margin:0">
                @csrf
                <button type="submit" class="sb-item" style="background:#ffffff;border:1px solid transparent;width:100%;text-align:right;cursor:pointer;font-family:inherit;color:#2e7d32">
                    <span class="sb-icon">🚪</span> خروج
                </button>
            </form>
        </li>
    </ul>
</aside>

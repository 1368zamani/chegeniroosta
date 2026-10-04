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
    border: 1px solid #e3f2fd;
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
    border-bottom: 2px solid #e3f2fd;
}
.unified-sidebar .sb-profile .avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #1565c0;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-size: 26px;
    font-weight: 900;
    box-shadow: 0 4px 14px rgba(21,101,192,0.3);
    border: 3px solid #e3f2fd;
}
.unified-sidebar .sb-profile .name {
    color: #1565c0;
    font-size: 14px;
    font-weight: 800;
    margin: 0;
}
.unified-sidebar .sb-profile .role {
    color: #1565c0;
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
    color: #1565c0;
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
    color: #1565c0;
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
    background: #1565c0;
    transition: width 0.22s;
}
.unified-sidebar .sb-item:hover {
    background: #e3f2fd;
    color: #0d47a1;
    padding-right: 22px;
    border-color: #e3f2fd;
}
.unified-sidebar .sb-item:hover::before { width: 4px; }
.unified-sidebar .sb-item.active {
    background: #1565c0;
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
    color: #1565c0;
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
    background: #e3f2fd;
    margin: 10px 14px;
}

/* فرم خروج — بدون حاشیه، زمینه سفید */
.unified-sidebar .sb-menu form button.sb-item {
    background: #ffffff;
    border: 1px solid transparent;
    color: #1565c0 !important;
}
.unified-sidebar .sb-menu form button.sb-item:hover {
    background: #e3f2fd;
    color: #0d47a1 !important;
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
        <li>
            <a href="/admin" class="sb-item {{ $path === 'admin' ? 'active' : '' }}">
                <span class="sb-icon">🏠</span> داشبورد
            </a>
        </li>

        @if($isSuper || $isSection || $isOfficer)
            <li class="sb-section">💰 عوارض و بهای خدمات</li>
            <li><a href="{{ route('admin.fees.dashboard') }}" class="sb-item {{ request()->routeIs('admin.fees.dashboard') ? 'active' : '' }}"><span class="sb-icon">📊</span> داشبورد عوارض</a></li>
            <li>
                <a href="{{ route('admin.fees.payments.index') }}" class="sb-item {{ request()->routeIs('admin.fees.payments.*') ? 'active' : '' }}">
                    <span class="sb-icon">💳</span> پرداخت‌ها
                    @php $pending = \App\Models\Payment::where('status','pending_review')->count(); @endphp
                    @if($pending > 0)<span class="sb-badge">{{ $pending }}</span>@endif
                </a>
            </li>
            <li><a href="{{ route('admin.fees.bills.index') }}" class="sb-item {{ request()->routeIs('admin.fees.bills.*') ? 'active' : '' }}"><span class="sb-icon">🧾</span> قبوض</a></li>
            <li><a href="{{ route('admin.fees.tariffs.index') }}" class="sb-item {{ request()->routeIs('admin.fees.tariffs.*') ? 'active' : '' }}"><span class="sb-icon">📋</span> تعرفه‌ها</a></li>
            <li><a href="{{ route('admin.fees.accounts.index') }}" class="sb-item {{ request()->routeIs('admin.fees.accounts.*') ? 'active' : '' }}"><span class="sb-icon">🏦</span> حساب‌ها</a></li>
            <li><a href="{{ route('admin.fees.categories.index') }}" class="sb-item {{ request()->routeIs('admin.fees.categories.*') ? 'active' : '' }}"><span class="sb-icon">🗂</span> دسته‌بندی</a></li>
            <li><a href="{{ route('admin.fees.types.index') }}" class="sb-item {{ request()->routeIs('admin.fees.types.*') ? 'active' : '' }}"><span class="sb-icon">📋</span> عناوین خدمت</a></li>
            <li><a href="{{ route('admin.fees.reports.index') }}" class="sb-item {{ request()->routeIs('admin.fees.reports.*') ? 'active' : '' }}"><span class="sb-icon">📈</span> گزارش‌ها</a></li>
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

        @if($isSuper || $isSection)
            <li class="sb-section">👥 کاربران</li>
            <li><a href="{{ route('admin.users.index') }}" class="sb-item"><span class="sb-icon">👤</span> کاربران</a></li>
            <li><a href="{{ route('admin.passwords.index') }}" class="sb-item {{ request()->routeIs('admin.passwords.*') ? 'active' : '' }}"><span class="sb-icon">🔑</span> مدیریت رمزها</a></li>
            <li><a href="{{ route('admin.fees.officers.index') }}" class="sb-item"><span class="sb-icon">🔐</span> کارشناسان</a></li>
        @endif

        @if($isSuper || $isSection)
            <li class="sb-section">📰 محتوا</li>
            <li><a href="{{ route('admin.section-news.index') }}" class="sb-item"><span class="sb-icon">📰</span> اخبار</a></li>
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
                <button type="submit" class="sb-item" style="background:#ffffff;border:1px solid transparent;width:100%;text-align:right;cursor:pointer;font-family:inherit;color:#1565c0">
                    <span class="sb-icon">🚪</span> خروج
                </button>
            </form>
        </li>
    </ul>
</aside>

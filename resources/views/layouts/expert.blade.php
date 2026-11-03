<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'پنل کارشناسان') — چگنی روستا</title>
    {{-- Bootstrap و آیکون‌ها به‌صورت محلی روی همین سرور میزبانی می‌شوند
         (قبلاً از cdn.jsdelivr.net گرفته می‌شدند که در ایران کند یا در دسترس نبود) --}}
    <link href="{{ asset('vendor/bootstrap/bootstrap.rtl.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <style>
        :root {
            --expert-primary: #2e7d32;
            --expert-dark: #0d3b2e;
            --expert-light: #e8f5e9;
        }
        body {
            font-family: 'Vazirmatn', 'Tahoma', sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
        }
        .expert-header {
            background: linear-gradient(135deg, var(--expert-primary), var(--expert-dark));
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .expert-nav {
            background: white;
            border-bottom: 1px solid #e0e6ed;
            padding: 0.5rem 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .expert-nav .nav-link {
            color: #4a5568;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            margin: 0 0.2rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .expert-nav .nav-link:hover {
            background: var(--expert-light);
            color: var(--expert-primary);
        }
        .expert-nav .nav-link.active {
            background: var(--expert-primary);
            color: white;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
        }
        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: white;
        }
        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--expert-dark);
            margin: 0.5rem 0 0.2rem;
        }
        .stat-label {
            color: #718096;
            font-size: 0.9rem;
        }
        .card-expert {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            border: none;
        }
        .card-expert .card-header {
            background: white;
            border-bottom: 2px solid var(--expert-light);
            padding: 1rem 1.5rem;
            font-weight: 600;
            color: var(--expert-dark);
        }
        .badge-draft { background: #fef3c7; color: #92400e; }
        .badge-published { background: #d1fae5; color: #065f46; }
        .badge-approved { background: #e2e8f0; color: #475569; }
    </style>
    @stack('styles')
</head>
<body>

<header class="expert-header">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-clipboard-data" style="font-size: 2rem;"></i>
            <div>
                <h5 class="mb-0 fw-bold">پنل کارشناسان فنی و مالی</h5>
                <small class="opacity-75">سامانه تعرفه‌های دهیاری چگنی روستا</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end">
                <div class="fw-bold">{{ auth()->user()->name ?? 'کاربر' }}</div>
                <small class="opacity-75">{{ auth()->user()->role ?? '-' }}</small>
            </div>
            <form method="POST" action="{{ route('expert.logout') }}" class="d-inline">
                @csrf
                <button class="btn btn-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> خروج
                </button>
            </form>
        </div>
    </div>
</header>

<nav class="expert-nav">
    <div class="container">
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('expert.dashboard') ? 'active' : '' }}"
                   href="{{ route('expert.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> داشبورد
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('expert.tariffs.*') ? 'active' : '' }}"
                   href="{{ route('expert.tariffs.index') }}">
                    <i class="bi bi-list-check"></i> تعرفه‌ها
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('expert.tariff-invoices.*') ? 'active' : '' }}"
                   href="{{ route('expert.tariff-invoices.index') }}">
                    <i class="bi bi-receipt"></i> 📋 فیش‌های عوارض
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('expert.parameters.*') ? 'active' : '' }}"
                   href="{{ route('expert.parameters.index') }}">
                    <i class="bi bi-sliders"></i> مفروضات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('expert.items.*') ? 'active' : '' }}"
                   href="{{ route('expert.items.index') }}">
                    <i class="bi bi-list-ol"></i> آیتم‌ها
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('expert.exemptions.*') ? 'active' : '' }}"
                   href="{{ route('expert.exemptions.index') }}">
                    <i class="bi bi-shield-check"></i> معافیت‌ها
                </a>
            </li>
        </ul>
    </div>
</nav>

<main class="container py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

<footer class="text-center text-muted py-4">
    <small>© {{ date('Y') }} سامانه تعرفه‌های دهیاری چگنی روستا — نسخه کارشناسی</small>
</footer>

<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
@stack('scripts')
</body>
</html>

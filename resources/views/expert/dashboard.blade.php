@extends('layouts.expert')

@section('title', 'داشبورد')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">خوش آمدید، {{ $user->name }}</h4>
    <p class="text-muted mb-0">نمای کلی سامانه تعرفه‌ها و آمار کلی</p>
</div>

{{-- کارت‌های آماری --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg,#2e7d32,#0d3b2e);">
                <i class="bi bi-list-check"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_tariffs']) }}</div>
            <div class="stat-label">ست تعرفه</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg,#388e3c,#1b5e20);">
                <i class="bi bi-list-ol"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_items']) }}</div>
            <div class="stat-label">آیتم تعرفه</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg,#10b981,#047857);">
                <i class="bi bi-sliders"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_parameters']) }}</div>
            <div class="stat-label">مفروضات</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg,#f59e0b,#b45309);">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_exemptions']) }}</div>
            <div class="stat-label">معافیت</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg,#8b5cf6,#6d28d9);">
                <i class="bi bi-house-door"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_villages']) }}</div>
            <div class="stat-label">روستا</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg,#ef4444,#b91c1c);">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
            <div class="stat-label">کاربر</div>
        </div>
    </div>
</div>

{{-- دو جدول --}}
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card card-expert h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history"></i> آخرین تعرفه‌ها</span>
                <a href="{{ route('expert.tariffs.index') }}" class="btn btn-sm btn-outline-primary">همه</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>روستا</th>
                                <th>سال</th>
                                <th>وضعیت</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTariffs as $t)
                                <tr>
                                    <td>{{ $t->village->name ?? '—' }}</td>
                                    <td>{{ $t->jalali_year }}</td>
                                    <td>
                                        <span class="badge {{ $t->status === 'published' ? 'badge-published' : 'badge-draft' }}">
                                            {{ $t->statusLabel() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">داده‌ای نیست</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card card-expert h-100">
            <div class="card-header">
                <i class="bi bi-exclamation-circle"></i> تعرفه‌های پیش‌نویس (نیاز به بررسی)
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>روستا</th>
                                <th>سال</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($draftTariffs as $t)
                                <tr>
                                    <td>{{ $t->village->name ?? '—' }}</td>
                                    <td>{{ $t->jalali_year }}</td>
                                    <td>
                                        <a href="{{ route('expert.tariffs.show', $t->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            بررسی
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">هیچ پیش‌نویسی نیست</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

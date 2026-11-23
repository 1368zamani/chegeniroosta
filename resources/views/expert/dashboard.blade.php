@extends('layouts.expert')

@section('title', 'داشبورد عوارض')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">خوش آمدید، {{ $user->name }}</h4>
    <p class="text-muted mb-0">نمای کلی سامانهٔ عوارض احداث بنا و عوارض پسماند</p>
</div>

{{-- کارت‌های آماری --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg,#c94a2b,#5c2313);">
                <i class="bi bi-folder2-open"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_files']) }}</div>
            <div class="stat-label">پروندهٔ عوارض احداث بنا</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg,#2e7d32,#1b5e20);">
                <i class="bi bi-trash"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['waste_titles']) }}</div>
            <div class="stat-label">قلم فعال پسماند</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg,#f5b841,#b45309);">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['pending_reqs']) }}</div>
            <div class="stat-label">درخواست در انتظار بررسی</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg,#1565c0,#0d47a1);">
                <i class="bi bi-house-door"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_villages']) }}</div>
            <div class="stat-label">روستا</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card card-expert h-100">
            <div class="card-header"><i class="bi bi-lightning-charge"></i> دسترسی سریع</div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @can('levy.view')
                        <a href="{{ route('admin.levy.dashboard') }}" class="btn btn-outline-primary text-start">
                            <i class="bi bi-speedometer2"></i> داشبورد عوارض و پروندهها
                        </a>
                    @endcan
                    @can('works-fees.view')
                        <a href="{{ route('admin.works-fees.index') }}" class="btn btn-outline-success text-start">
                            <i class="bi bi-cash-coin"></i> تعیین مبالغ و فعالسازی اقلام پسماند
                        </a>
                    @endcan
                    @can('levy.review')
                        <a href="{{ route('admin.levy.requests.index') }}" class="btn btn-outline-warning text-start">
                            <i class="bi bi-check2-square"></i> بررسی و تأیید درخواستهای مردمی
                        </a>
                    @endcan
                    @can('levy.manage')
                        <a href="{{ route('admin.levy.villages') }}" class="btn btn-outline-secondary text-start">
                            <i class="bi bi-sliders"></i> ضرایب و مبالغ روستاها (اکسل)
                        </a>
                    @endcan
                    @can('levy.report')
                        <a href="{{ route('admin.levy.reports.all') }}" class="btn btn-outline-dark text-start">
                            <i class="bi bi-bar-chart"></i> گزارشهای عوارض
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card card-expert h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history"></i> آخرین درخواستها</span>
                @can('levy.review')
                    <a href="{{ route('admin.levy.requests.index') }}" class="btn btn-sm btn-outline-primary">همه</a>
                @endcan
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>روستا</th>
                                <th>متقاضی</th>
                                <th>مبلغ</th>
                                <th>وضعیت</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRequests as $r)
                                <tr>
                                    <td>{{ $r->village->name ?? '—' }}</td>
                                    <td>{{ $r->payer_name }}</td>
                                    <td dir="ltr">{{ number_format($r->amount) }}</td>
                                    <td>{{ $r->statusLabel() }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">درخواستی ثبت نشده است</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

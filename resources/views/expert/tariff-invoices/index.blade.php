@extends('layouts.expert')
@section('title', 'فیش‌های عوارض')

@php
    use App\Support\Number;
    use Morilog\Jalali\Jalalian;
    $labels = [
        'pending_review'   => 'در انتظار بررسی',
        'issued'           => 'صادر شده',
        'receipt_uploaded' => 'رسید بارگذاری',
        'paid'             => 'پرداخت‌شده',
        'cancelled'        => 'ابطال‌شده',
    ];
@endphp

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">📋 فیش‌های عوارض</h4>
        <p class="text-muted mb-0">مدیریت فیش‌های عوارض و بهای خدمات دهیاری‌ها</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('expert.tariff-invoices.account') }}" class="btn btn-outline-primary">
            <i class="bi bi-bank"></i> حساب بانکی
        </a>
        <a href="{{ route('expert.tariffs.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-list-check"></i> تعرفه‌ها
        </a>
    </div>
</div>

{{-- تب‌های وضعیت --}}
<div class="d-flex flex-wrap gap-2 mb-3">
    <a href="{{ route('expert.tariff-invoices.index') }}"
       class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">
        همه ({{ Number::fa($counts['all']) }})
    </a>
    @foreach($labels as $k => $t)
        <a href="{{ route('expert.tariff-invoices.index', ['status' => $k]) }}"
           class="btn btn-sm {{ request('status') === $k ? 'btn-primary' : 'btn-outline-secondary' }}">
            {{ $t }} ({{ Number::fa($counts[$k] ?? 0) }})
        </a>
    @endforeach
</div>

{{-- جستجو --}}
<form method="GET" class="card card-expert mb-4">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-6">
                <input name="q" value="{{ request('q') }}" class="form-control"
                       placeholder="🔍 کد پیگیری / نام / موبایل / کد ملی">
            </div>
            <div class="col-md-3">
                <select name="village_id" class="form-select">
                    <option value="">همه روستاها</option>
                    @foreach($villages as $v)
                        <option value="{{ $v->id }}" @selected(request('village_id') == $v->id)>{{ $v->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary flex-grow-1">جستجو</button>
                @if(request('q') || request('status') || request('village_id'))
                    <a href="{{ route('expert.tariff-invoices.index') }}" class="btn btn-outline-secondary">×</a>
                @endif
            </div>
        </div>
    </div>
</form>

{{-- جدول --}}
<div class="card card-expert">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>کد پیگیری</th>
                    <th>روستا</th>
                    <th>پرداخت‌کننده</th>
                    <th>مبلغ (ریال)</th>
                    <th>وضعیت</th>
                    <th>تاریخ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr class="{{ in_array($inv->status, ['pending_review', 'receipt_uploaded']) ? 'table-warning' : '' }}
                           {{ $inv->status === 'paid' ? 'table-success' : '' }}
                           {{ $inv->status === 'cancelled' ? 'table-secondary' : '' }}">
                    <td class="font-monospace">
                        <a href="{{ route('expert.tariff-invoices.show', $inv->id) }}" class="fw-bold text-decoration-none">
                            {{ $inv->tracking_code }}
                        </a>
                    </td>
                    <td>{{ $inv->village->name ?? '-' }}</td>
                    <td>
                        <div>{{ $inv->payer_name }}</div>
                        <small class="text-muted font-monospace">{{ $inv->payer_mobile }}</small>
                    </td>
                    <td class="font-monospace">{{ Number::fa(number_format($inv->total)) }}</td>
                    <td>
                        <span class="badge {{ $inv->statusColor() }}">{{ $inv->statusLabel() }}</span>
                    </td>
                    <td><small>{{ $inv->issued_at ? Number::fa(Jalalian::fromCarbon($inv->issued_at)->format('Y/m/d')) : '-' }}</small></td>
                    <td>
                        <a href="{{ route('expert.tariff-invoices.show', $inv->id) }}" class="btn btn-sm btn-outline-primary">
                            بررسی
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <div style="font-size:2rem">📭</div>
                        هیچ فیشی یافت نشد.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($invoices->hasPages())
<div class="mt-3">{{ $invoices->links() }}</div>
@endif

@endsection

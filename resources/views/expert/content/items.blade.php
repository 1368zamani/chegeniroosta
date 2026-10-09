@extends('layouts.expert')
@section('title', 'آیتم‌های تعرفه')

@php use App\Support\Number; @endphp

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">📝 آیتم‌های تعرفه</h4>
        <p class="text-muted small mb-0">طبق فرم ۲ و ۳ آیین‌نامه عوارض و بهای خدمات دهیاری‌ها</p>
    </div>
    @if($set)
    <div class="text-muted small text-end">
        روستای <strong>{{ $set->village->name ?? '-' }}</strong> · سال {{ Number::fa($set->jalali_year) }}
    </div>
    @endif
</div>

<form method="GET" class="card card-expert mb-4">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-4">
                <select name="village_id" onchange="this.form.submit()" class="form-select">
                    @foreach($villages as $v)
                        <option value="{{ $v->id }}" @selected($villageId == $v->id)>{{ $v->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="type" onchange="this.form.submit()" class="form-select">
                    <option value="">همه انواع</option>
                    <option value="levy" @selected(request('type') === 'levy')>عوارض</option>
                    <option value="service_fee" @selected(request('type') === 'service_fee')>بهای خدمات</option>
                </select>
            </div>
            <div class="col-md-4">
                <input name="q" value="{{ request('q') }}" class="form-control" placeholder="🔍 جستجو">
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary w-100">🔍</button>
            </div>
        </div>
    </div>
</form>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="stat-card"><div class="stat-value">{{ Number::fa($stats['total']) }}</div><div class="stat-label">کل</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-value text-success">{{ Number::fa($stats['active']) }}</div><div class="stat-label">فعال</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-value text-primary">{{ Number::fa($stats['levy']) }}</div><div class="stat-label">عوارض</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-value text-warning">{{ Number::fa($stats['service']) }}</div><div class="stat-label">بهای خدمات</div></div></div>
</div>

<div class="card card-expert">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>عنوان</th>
                    <th>کد</th>
                    <th>نوع</th>
                    <th>مبلغ قبلی</th>
                    <th>افزایش</th>
                    <th>مبلغ نهایی</th>
                    <th>وضعیت</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $i => $item)
                <tr>
                    <td>{{ Number::fa($i + 1) }}</td>
                    <td>
                        <strong>{{ optional($item->title)->name_fa ?? 'نامشخص' }}</strong>
                        <div class="small text-muted">{{ optional($item->title)->code ?? '' }}</div>
                    </td>
                    <td>{{ optional($item->title)->code ?? '-' }}</td>
                    <td>
                        @php $t = optional($item->title)->type ?? 'levy'; @endphp
                        <span class="badge {{ $t === 'levy' ? 'bg-primary' : 'bg-warning text-dark' }}">
                            {{ $t === 'levy' ? 'عوارض' : 'خدمات' }}
                        </span>
                    </td>
                    <td class="font-monospace small">{{ Number::fa(number_format($item->previous_amount ?? 0)) }}</td>
                    <td class="font-monospace small">{{ Number::fa($item->increase_percent ?? 0) }}%</td>
                    <td class="font-monospace fw-bold">{{ Number::fa(number_format($item->final_amount ?? 0)) }}</td>
                    <td>
                        @if($item->is_active)
                            <span class="badge bg-success">فعال</span>
                        @else
                            <span class="badge bg-secondary">غیرفعال</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('expert.items.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">✏️</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-5">
                    <div style="font-size:3rem">📭</div>
                    <div class="mt-2">آیتمی یافت نشد</div>
                    @if($set)
                    <div class="small mt-2">set_id: {{ $set->id }} | items count در دیتابیس: باید 61 باشه</div>
                    @endif
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

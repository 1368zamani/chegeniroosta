@extends('layouts.expert')
@section('title', 'مفروضات و مقادیر پایه')

@php
    use App\Support\Number;

    $map = [
        'land_value_per_m2' => [
            'title' => 'ارزش معاملاتی هر متر مربع عرصه',
            'help'  => 'قیمت منطقه‌ای هر متر مربع زمین طبق آخرین دفترچه ارزش معاملاتی املاک',
            'unit'  => 'ریال بر متر مربع',
            'category' => 'ارزش پایه املاک',
            'icon' => '🏞️',
            'ref' => 'ماده ۶۴ قانون مالیات‌های مستقیم',
        ],
        'building_value_per_m2' => [
            'title' => 'ارزش معاملاتی هر متر مربع اعیانی',
            'help'  => 'ارزش پایه ساختمان در هر متر مربع طبق دفترچه ارزش معاملاتی',
            'unit'  => 'ریال بر متر مربع',
            'category' => 'ارزش پایه املاک',
            'icon' => '🏢',
            'ref' => 'تبصره ۲ ماده ۶۴',
        ],
        'adjustment_coefficient' => [
            'title' => 'ضریب تعدیل سالانه',
            'help'  => 'ضریب مصوب شورای اسلامی برای به‌روزرسانی عوارض (مثال: 0.115 یعنی 11.5% افزایش)',
            'unit'  => 'ضریب اعشاری',
            'category' => 'ضرایب تعدیل',
            'icon' => '📊',
            'ref' => 'ماده ۵۰ قانون ارزش افزوده',
        ],
        'usage_factors' => [
            'title' => 'ضرایب کاربری املاک',
            'help'  => 'ضریب اعمالی بر اساس نوع کاربری (مسکونی، تجاری، صنعتی، اداری، ورزشی و...)',
            'unit'  => 'ساختار JSON',
            'category' => 'ضرایب تعدیل',
            'icon' => '🏘️',
            'ref' => 'آیین‌نامه اجرایی ماده ۲',
        ],
        'rate_per_sqm' => [
            'title' => 'نرخ پایه هر متر مربع',
            'help'  => 'نرخ محاسبه عوارض/بهای خدمات برای هر متر مربع',
            'unit'  => 'ریال بر متر مربع',
            'category' => 'نرخ‌های پایه',
            'icon' => '💰',
            'ref' => 'مصوبه شورای اسلامی روستا',
        ],
        'rate_per_unit' => [
            'title' => 'نرخ پایه هر واحد',
            'help'  => 'نرخ محاسبه برای یک واحد ساختمانی',
            'unit'  => 'ریال بر واحد',
            'category' => 'نرخ‌های پایه',
            'icon' => '🏠',
            'ref' => 'مصوبه شورای اسلامی',
        ],
        'rate_per_person' => [
            'title' => 'نرخ پایه هر نفر',
            'help'  => 'نرخ محاسبه به ازای هر نفر',
            'unit'  => 'ریال بر نفر',
            'category' => 'نرخ‌های پایه',
            'icon' => '👥',
            'ref' => 'مصوبه شورای اسلامی',
        ],
        'waste_monthly_per_person' => [
            'title' => 'نرخ ماهانه پسماند هر نفر',
            'help'  => 'هزینه ماهانه جمع‌آوری زباله به ازای هر نفر',
            'unit'  => 'ریال در ماه',
            'category' => 'بهای خدمات ویژه',
            'icon' => '🗑️',
            'ref' => 'ماده ۱۶ قانون مدیریت پسماند',
        ],
    ];

    $grouped = [];
    foreach ($parameters as $p) {
        $info = $map[$p->key] ?? [
            'title' => $p->key,
            'help'  => 'پارامتر سفارشی',
            'unit'  => $p->unit ?? '—',
            'category' => 'سایر پارامترها',
            'icon' => '⚙️',
            'ref' => '—',
        ];
        $grouped[$info['category']][] = ['model' => $p, 'info' => $info];
    }
@endphp

@section('content')

<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1">⚙️ مفروضات و مقادیر پایه محاسبه</h4>
        <p class="text-muted mb-0 small">
            این مقادیر پایه‌ای هستند که فرمول‌های محاسبه عوارض و بهای خدمات بر اساس آن‌ها اجرا می‌شوند.
        </p>
    </div>
    @if($set)
    <div class="text-muted small text-end">
        <div class="fw-bold text-primary">روستای {{ $set->village->name ?? '-' }}</div>
        <div>سال مالی: {{ Number::fa($set->jalali_year) }}</div>
    </div>
    @endif
</div>

<form method="GET" class="card card-expert mb-4">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-bold">🏘️ انتخاب روستا</label>
                <select name="village_id" onchange="this.form.submit()" class="form-select">
                    @foreach($villages as $v)
                        <option value="{{ $v->id }}" @selected($villageId == $v->id)>{{ $v->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-7 text-md-end">
                <div class="alert alert-info mb-0 small py-2">
                    <i class="bi bi-info-circle-fill"></i>
                    برای ویرایش هر پارامتر، روی دکمه ✏️ کلیک کنید.
                </div>
            </div>
        </div>
    </div>
</form>

@if($parameters->isEmpty())
<div class="card card-expert">
    <div class="card-body text-center py-5 text-muted">
        <div style="font-size:3rem">📭</div>
        <h5 class="mt-3">هیچ مفروضاتی برای این روستا ثبت نشده است</h5>
    </div>
</div>
@else

@foreach($grouped as $categoryName => $items)
<div class="card card-expert mb-3">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <div class="fw-bold">{{ $items[0]['info']['icon'] }} {{ $categoryName }}</div>
        <span class="badge bg-primary">{{ Number::fa(count($items)) }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:30%">📌 عنوان</th>
                        <th style="width:20%" class="text-end">💵 مقدار</th>
                        <th style="width:12%">📐 واحد</th>
                        <th style="width:28%">📖 توضیحات</th>
                        <th style="width:10%" class="text-center">⚙️</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $gi)
                    @php $p = $gi['model']; $info = $gi['info']; @endphp
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $info['title'] }}</div>
                            
                            @if($info['ref'] !== '—')
                            <div class="text-info small mt-1" style="font-size:10px;">
                                <i class="bi bi-bookmark-fill"></i> {{ $info['ref'] }}
                            </div>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($p->key === 'usage_factors')
                            <button class="btn btn-sm btn-outline-info" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#js-{{ $p->id }}">
                                👁 نمایش
                            </button>
                            <div class="collapse mt-2" id="js-{{ $p->id }}">
                                <pre class="small bg-light p-2 rounded mb-0" style="max-height:200px; overflow:auto; direction:ltr;">{{ $p->value }}</pre>
                            </div>
                            @else
                            <span class="fw-bold text-primary fs-5 font-monospace">
                                {{ Number::fa(number_format((float) $p->value)) }}
                            </span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $info['unit'] }}</td>
                        <td class="small text-secondary">{{ $info['help'] }}</td>
                        <td class="text-center">
                            <a href="{{ route('expert.parameters.edit', $p->id) }}"
                               class="btn btn-sm btn-primary">✏️ ویرایش</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach

<div class="alert alert-warning small">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <strong>توجه:</strong> تغییر هر پارامتر فقط روی محاسبات آینده اثر می‌گذارد.
</div>

@endif

@endsection

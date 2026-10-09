@extends('layouts.expert')
@section('title', 'معافیت‌ها')

@php use App\Support\Number; @endphp

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">🛡️ معافیت‌ها</h4>
    @if($set)
    <div class="text-muted small">
        روستای <strong>{{ $set->village->name ?? '-' }}</strong> · سال {{ Number::fa($set->jalali_year) }}
    </div>
    @endif
</div>

<form method="GET" class="card card-expert mb-4">
    <div class="card-body">
        <select name="village_id" onchange="this.form.submit()" class="form-select">
            @foreach($villages as $v)
                <option value="{{ $v->id }}" @selected($villageId == $v->id)>{{ $v->name }}</option>
            @endforeach
        </select>
    </div>
</form>

<div class="card card-expert">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>نوع (kind)</th>
                    <th>درصد</th>
                    <th>توضیحات</th>
                    <th>یادداشت قانونی</th>
                    <th>وضعیت</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($exemptions as $i => $e)
                <tr>
                    <td>{{ Number::fa($i + 1) }}</td>
                    <td><strong>{{ $e->kind }}</strong></td>
                    <td>
                        <span class="badge {{ (float) $e->discount_percent == 100 ? 'bg-success' : 'bg-info' }}">
                            {{ Number::fa((int) $e->discount_percent) }}%
                        </span>
                    </td>
                    <td class="small">{{ $e->description }}</td>
                    <td class="small text-muted">{{ Str::limit($e->statutory_note ?? '-', 60) }}</td>
                    <td>
                        @if($e->is_active)
                            <span class="badge bg-success">فعال</span>
                        @else
                            <span class="badge bg-secondary">غیرفعال</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('expert.exemptions.edit', $e->id) }}" class="btn btn-sm btn-outline-primary">✏️</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-5">📭 معافیتی یافت نشد</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

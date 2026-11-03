@extends('layouts.expert')
@section('title', 'ویرایش آیتم تعرفه')

@php
    use App\Support\Number;
@endphp

@section('content')

<div class="mb-3">
    <a href="{{ route('expert.items.index') }}" class="text-decoration-none">
        <i class="bi bi-arrow-right"></i> بازگشت به لیست آیتم‌ها
    </a>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card card-expert">
            <div class="card-header">
                <strong>✏️ ویرایش آیتم تعرفه</strong>
                <div class="small text-muted mt-1">
                    روستای {{ $item->set->village->name ?? '-' }}
                    · سال {{ Number::fa($item->set->jalali_year ?? '') }}
                </div>
            </div>
            <div class="card-body">

                @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('expert.items.update', $item->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">عنوان تعرفه</label>
                        <input type="text" value="{{ $item->title->name_fa ?? '-' }}" class="form-control" readonly>
                    </div>

                    @if($item->title?->formula_display || $item->formula_display)
                    <div class="mb-3">
                        <label class="form-label">فرمول (غیرقابل ویرایش)</label>
                        <input type="text" value="{{ $item->formula_display ?? '-' }}" class="form-control font-monospace" readonly>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">مبلغ قبلی (ریال) *</label>
                        <input type="number" name="previous_amount" 
                               value="{{ old('previous_amount', $item->previous_amount) }}"
                               min="0" required class="form-control font-monospace">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">درصد افزایش *</label>
                        <input type="number" name="increase_percent" step="0.01"
                               value="{{ old('increase_percent', $item->increase_percent ?? 0) }}"
                               min="0" max="1000" required class="form-control font-monospace">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">مبلغ نهایی (ریال) *</label>
                        <input type="number" name="final_amount" 
                               value="{{ old('final_amount', $item->final_amount) }}"
                               min="0" required class="form-control font-monospace">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">یادداشت</label>
                        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $item->notes) }}</textarea>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input"
                               id="is_active" @checked(old('is_active', $item->is_active))>
                        <label class="form-check-label" for="is_active">فعال باشد</label>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-primary">💾 ذخیره</button>
                        <a href="{{ route('expert.items.index') }}" class="btn btn-outline-secondary">انصراف</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

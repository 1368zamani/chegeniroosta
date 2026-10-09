@extends('layouts.expert')
@section('title', 'ویرایش معافیت')

@section('content')
<div class="mb-3">
    <a href="{{ route('expert.exemptions.index', ['village_id' => $set->village_id]) }}" class="text-decoration-none">← بازگشت</a>
</div>

<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card card-expert">
            <div class="card-header"><strong>✏️ ویرایش معافیت</strong></div>
            <div class="card-body">
                <form method="POST" action="{{ route('expert.exemptions.update', $exemption->id) }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">نوع معافیت *</label>
                        <input type="text" name="kind" value="{{ old('kind', $exemption->kind) }}" required class="form-control">
                        <small class="text-muted">مثال: مددجویان، ایثارگران، اماکن مذهبی</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">درصد تخفیف *</label>
                        <input type="number" name="discount_percent" step="0.01" min="0" max="100"
                               value="{{ old('discount_percent', $exemption->discount_percent) }}" required class="form-control font-monospace">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">توضیحات *</label>
                        <input type="text" name="description" value="{{ old('description', $exemption->description) }}" required class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">یادداشت قانونی</label>
                        <textarea name="statutory_note" rows="3" class="form-control">{{ old('statutory_note', $exemption->statutory_note) }}</textarea>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $exemption->is_active))>
                        <label class="form-check-label">فعال</label>
                    </div>

                    <button class="btn btn-primary">💾 ذخیره</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

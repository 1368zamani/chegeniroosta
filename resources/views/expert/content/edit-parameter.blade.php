@extends('layouts.expert')
@section('title', 'ویرایش پارامتر')

@section('content')
<div class="mb-3">
    <a href="{{ route('expert.parameters.index', ['village_id' => $set->village_id]) }}" class="text-decoration-none">← بازگشت</a>
</div>

<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card card-expert">
            <div class="card-header"><strong>✏️ ویرایش پارامتر</strong></div>
            <div class="card-body">
                <form method="POST" action="{{ route('expert.parameters.update', $parameter->id) }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">کلید</label>
                        <input type="text" value="{{ $parameter->key }}" class="form-control font-monospace" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">مقدار *</label>
                        <input type="text" name="value" value="{{ old('value', $parameter->value) }}" required class="form-control font-monospace">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="description" rows="3" class="form-control">{{ old('description', $parameter->description) }}</textarea>
                    </div>

                    <button class="btn btn-primary">💾 ذخیره</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

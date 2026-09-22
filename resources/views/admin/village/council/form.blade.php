@extends('admin.village.layout')

@section('title', isset($member) ? 'ویرایش عضو' : 'عضو جدید')
@section('content')

<div class="page-title">
    <h2>{{ isset($member) ? '✏️ ویرایش عضو' : '➕ عضو جدید' }}</h2>
</div>

<form method="POST" action="{{ isset($member) ? route('admin.village.council.update', [$village->id, $member->id]) : route('admin.village.council.store', $village->id) }}" enctype="multipart/form-data">
    @csrf
    @if(isset($member)) @method('PUT') @endif

    <div class="card">
        <h3>👥 اطلاعات عضو شورا</h3>

        <div class="row-2">
            <div class="field">
                <label>نام و نام خانوادگی *</label>
                <input type="text" name="name" value="{{ old('name', $member->name ?? '') }}" required>
            </div>
            <div class="field">
                <label>سمت *</label>
                <input type="text" name="position" value="{{ old('position', $member->position ?? '') }}" required placeholder="رئیس شورا، نایب رئیس، عضو، ...">
            </div>
        </div>

        <div class="row-2">
            <div class="field">
                <label>شماره تماس</label>
                <input type="text" name="phone" value="{{ old('phone', $member->phone ?? '') }}" dir="ltr">
            </div>
            <div class="field">
                <label>ترتیب نمایش</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $member->sort_order ?? 0) }}">
            </div>
        </div>

        <div class="field">
            <label>عکس</label>
            <input type="file" name="photo" accept="image/*">
            @if(isset($member) && $member->photo)
                <img src="{{ asset('storage/' . $member->photo) }}" style="max-width:120px; margin-top:10px; border-radius:50%;">
            @endif
        </div>

        <label class="checkbox-field">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $member->is_active ?? true) ? 'checked' : '' }}>
            <span>✅ عضو فعال</span>
        </label>
    </div>

    <div style="display:flex; gap:12px;">
        <button type="submit" class="btn btn-success">💾 ذخیره</button>
        <a href="{{ route('admin.village.council.index', $village->id) }}" class="btn btn-secondary">انصراف</a>
    </div>
</form>

@endsection

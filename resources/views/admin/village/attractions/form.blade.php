@extends('admin.village.layout')

@section('title', isset($attraction) ? 'ویرایش جاذبه' : 'جاذبه جدید')
@section('content')

<div class="page-title">
    <h2>{{ isset($attraction) ? '✏️ ویرایش' : '➕ جاذبه جدید' }}</h2>
</div>

<form method="POST" action="{{ isset($attraction) ? route('admin.village.attractions.update', [$village->id, $attraction->id]) : route('admin.village.attractions.store', $village->id) }}" enctype="multipart/form-data">
    @csrf
    @if(isset($attraction)) @method('PUT') @endif

    <div class="card">
        <h3>🏞 اطلاعات جاذبه</h3>

        <div class="row-2">
            <div class="field">
                <label>نام جاذبه *</label>
                <input type="text" name="name" value="{{ old('name', $attraction->name ?? '') }}" required>
            </div>
            <div class="field">
                <label>دسته</label>
                <input type="text" name="category" value="{{ old('category', $attraction->category ?? '') }}" placeholder="تاریخی، طبیعی، مذهبی، ...">
            </div>
        </div>

        <div class="field">
            <label>توضیحات</label>
            <textarea name="description">{{ old('description', $attraction->description ?? '') }}</textarea>
        </div>

        <div class="field">
            <label>آدرس / موقعیت</label>
            <input type="text" name="address" value="{{ old('address', $attraction->address ?? '') }}">
        </div>

        <div class="row-2">
            <div class="field">
                <label>تصویر</label>
                <input type="file" name="image" accept="image/*">
                @if(isset($attraction) && $attraction->image)
                    <img src="{{ asset('storage/' . $attraction->image) }}" style="max-width:150px; margin-top:10px; border-radius:8px;">
                @endif
            </div>
            <div class="field">
                <label>ترتیب نمایش</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $attraction->sort_order ?? 0) }}">
            </div>
        </div>
    </div>

    <div style="display:flex; gap:12px;">
        <button type="submit" class="btn btn-success">💾 ذخیره</button>
        <a href="{{ route('admin.village.attractions.index', $village->id) }}" class="btn btn-secondary">انصراف</a>
    </div>
</form>

@endsection

@extends('admin.village.layout')

@section('title', isset($facility) ? 'ویرایش امکانات' : 'امکانات جدید')
@section('content')

<div class="page-title">
    <h2>{{ isset($facility) ? '✏️ ویرایش' : '➕ امکانات جدید' }}</h2>
</div>

<form method="POST" action="{{ isset($facility) ? route('admin.village.facilities.update', [$village->id, $facility->id]) : route('admin.village.facilities.store', $village->id) }}">
    @csrf
    @if(isset($facility)) @method('PUT') @endif

    <div class="card">
        <h3>🏛 اطلاعات امکانات</h3>

        <div class="row-2">
            <div class="field">
                <label>نام *</label>
                <input type="text" name="name" value="{{ old('name', $facility->name ?? '') }}" required placeholder="مثلاً: مدرسه، مسجد، ...">
            </div>
            <div class="field">
                <label>آیکون (ایموجی)</label>
                <input type="text" name="icon" value="{{ old('icon', $facility->icon ?? '') }}" placeholder="🏫 🏥 🕌 ⚽ ...">
                <div class="hint">مثلاً 🏫 برای مدرسه، 🏥 برای بهداشت</div>
            </div>
        </div>

        <div class="row-2">
            <div class="field">
                <label>دسته</label>
                <input type="text" name="category" value="{{ old('category', $facility->category ?? '') }}" placeholder="آموزشی، بهداشتی، مذهبی، ...">
            </div>
            <div class="field">
                <label>ترتیب نمایش</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $facility->sort_order ?? 0) }}">
            </div>
        </div>

        <div class="field">
            <label>توضیحات</label>
            <textarea name="description">{{ old('description', $facility->description ?? '') }}</textarea>
        </div>

        <label class="checkbox-field">
            <input type="checkbox" name="is_available" value="1" {{ old('is_available', $facility->is_available ?? true) ? 'checked' : '' }}>
            <span>✅ این امکان موجود است</span>
        </label>
    </div>

    <div style="display:flex; gap:12px;">
        <button type="submit" class="btn btn-success">💾 ذخیره</button>
        <a href="{{ route('admin.village.facilities.index', $village->id) }}" class="btn btn-secondary">انصراف</a>
    </div>
</form>

@endsection

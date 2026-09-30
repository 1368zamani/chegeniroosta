@extends('admin.sections.layout')
@section('title', isset($category) ? 'ویرایش دسته' : 'دسته جدید')
@section('content')

<div class="page-title"><h2>{{ isset($category) ? '✏️ ویرایش دسته' : '➕ دسته جدید' }}</h2></div>

<form method="POST" action="{{ isset($category) ? route('admin.fees.categories.update', $category->id) : route('admin.fees.categories.store') }}">
    @csrf
    @if(isset($category)) @method('PUT') @endif

    <div class="card">
        <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:16px;">
            <div class="field">
                <label>نام دسته *</label>
                <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required>
            </div>
            <div class="field">
                <label>آیکون (ایموجی)</label>
                <input type="text" name="icon" value="{{ old('icon', $category->icon ?? '') }}" placeholder="🏗">
            </div>
            <div class="field">
                <label>ترتیب</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
            </div>
        </div>

        <div class="field">
            <label>اسلاگ (انگلیسی، یکتا) *</label>
            <input type="text" name="slug" value="{{ old('slug', $category->slug ?? '') }}" required dir="ltr" placeholder="construction">
        </div>

        <div class="field">
            <label>توضیحات</label>
            <textarea name="description" style="min-height:100px;">{{ old('description', $category->description ?? '') }}</textarea>
        </div>

        <label class="checkbox-field">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
            <span>✅ فعال</span>
        </label>
    </div>

    <div style="display:flex;gap:12px;">
        <button type="submit" class="btn" style="background:#2e7d32;color:white;padding:11px 24px;border:none;border-radius:8px;cursor:pointer;font-family:inherit;font-weight:600;">💾 ذخیره</button>
        <a href="{{ route('admin.fees.categories.index') }}" style="padding:11px 20px;background:#cfd8dc;color:#37474f;border-radius:8px;text-decoration:none;">انصراف</a>
    </div>
</form>
@endsection

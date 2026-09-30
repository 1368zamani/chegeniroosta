@extends('admin.sections.layout')
@section('title', isset($type) ? 'ویرایش عنوان' : 'عنوان جدید')
@section('content')

<div class="page-title"><h2>{{ isset($type) ? '✏️ ویرایش عنوان خدمت' : '➕ عنوان خدمت جدید' }}</h2></div>

<form method="POST" action="{{ isset($type) ? route('admin.fees.types.update', $type->id) : route('admin.fees.types.store') }}">
    @csrf
    @if(isset($type)) @method('PUT') @endif

    <div class="card">
        <div class="field">
            <label>دسته‌بندی *</label>
            <select name="category_id" required>
                <option value="">— انتخاب کنید —</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(old('category_id', $type->category_id ?? '') == $cat->id)>{{ $cat->icon }} {{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;">
            <div class="field">
                <label>عنوان خدمت *</label>
                <input type="text" name="name" value="{{ old('name', $type->name ?? '') }}" required placeholder="مثال: احداث بنا">
            </div>
            <div class="field">
                <label>ترتیب</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $type->sort_order ?? 0) }}">
            </div>
        </div>

        <div class="field">
            <label>اسلاگ (اختیاری، انگلیسی)</label>
            <input type="text" name="slug" value="{{ old('slug', $type->slug ?? '') }}" dir="ltr" placeholder="build">
        </div>

        <div class="field">
            <label>توضیحات</label>
            <textarea name="description" style="min-height:100px;">{{ old('description', $type->description ?? '') }}</textarea>
        </div>

        <label class="checkbox-field">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $type->is_active ?? true) ? 'checked' : '' }}>
            <span>✅ فعال</span>
        </label>
    </div>

    <div style="display:flex;gap:12px;">
        <button type="submit" class="btn" style="background:#2e7d32;color:white;padding:11px 24px;border:none;border-radius:8px;cursor:pointer;font-family:inherit;font-weight:600;">💾 ذخیره</button>
        <a href="{{ route('admin.fees.types.index') }}" style="padding:11px 20px;background:#cfd8dc;color:#37474f;border-radius:8px;text-decoration:none;">انصراف</a>
    </div>
</form>
@endsection

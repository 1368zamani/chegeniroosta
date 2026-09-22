@extends('admin.section-gallery.layout')
@section('title', 'آپلود تصاویر ' . $section->name)
@section('header', '📤 آپلود تصاویر: ' . $section->name)

@section('content')
<div class="card">
    <form method="POST" action="{{ route('admin.sections.gallery.store', $section->id) }}" enctype="multipart/form-data">
        @csrf
        <div class="field">
            <label>انتخاب تصاویر *</label>
            <input type="file" name="images[]" accept="image/*" multiple required>
            <div class="hint">می‌تونی همزمان چند تصویر انتخاب کنی. حداکثر ۵ مگابایت هرکدوم.</div>
        </div>
        <div class="field">
            <label>توضیح برای همه تصاویر (اختیاری)</label>
            <input type="text" name="caption" value="{{ old('caption') }}">
        </div>
        <div style="display:flex; gap:12px;">
            <button type="submit" class="btn btn-primary">📤 آپلود</button>
            <a href="{{ route('admin.sections.gallery.index', $section->id) }}" class="btn btn-secondary">انصراف</a>
        </div>
    </form>
</div>
@endsection

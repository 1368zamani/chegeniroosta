@extends('admin.section-news.layout')

@section('title', 'ویرایش خبر')
@section('header', 'ویرایش خبر')

@section('content')

<form method="POST" action="{{ route('admin.section-news.update', $news->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card">

        <div class="field">
            <label>بخش</label>
            <input type="text" value="{{ $news->section->name }}" disabled style="background:#f5f7fa; cursor:not-allowed;">
        </div>

        <div class="field">
            <label>عنوان خبر *</label>
            <input type="text" name="title" value="{{ old('title', $news->title) }}" required>
        </div>

        <div class="field">
            <label>خلاصه خبر</label>
            <textarea name="excerpt" style="min-height:80px;">{{ old('excerpt', $news->excerpt) }}</textarea>
        </div>

        <div class="field">
            <label>متن کامل خبر *</label>
            <textarea name="content" required>{{ old('content', $news->content) }}</textarea>
        </div>

        <div class="field">
            <label>تصویر فعلی</label>
            @if($news->image)
                <img src="{{ asset('storage/' . $news->image) }}" style="max-width:300px; border-radius:8px; margin-bottom:8px;">
            @else
                <p style="color:#90a4ae; font-size:13px;">تصویری ندارد.</p>
            @endif
        </div>

        <div class="field">
            <label>تصویر جدید (اختیاری)</label>
            <input type="file" name="image" accept="image/*">
            <div class="hint">اگر تصویر جدید آپلود کنی، تصویر قبلی جایگزین می‌شود.</div>
        </div>

        <div class="checkbox-field">
            <input type="checkbox" name="is_pinned" value="1" id="is_pinned" {{ old('is_pinned', $news->is_pinned) ? 'checked' : '' }}>
            <label for="is_pinned">📌 سنجاق در بالای لیست</label>
        </div>

        <div class="checkbox-field">
            <input type="checkbox" name="is_published" value="1" id="is_published" {{ old('is_published', $news->is_published) ? 'checked' : '' }}>
            <label for="is_published">✅ منتشر شده</label>
        </div>

        <div style="display:flex; gap:12px; margin-top:24px;">
            <button type="submit" class="btn btn-primary">💾 ذخیره تغییرات</button>
            <a href="{{ route('admin.section-news.index') }}" class="btn btn-secondary">انصراف</a>
        </div>

    </div>

</form>

@endsection

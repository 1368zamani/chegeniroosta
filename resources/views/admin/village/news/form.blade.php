@extends('admin.village.layout')

@section('title', isset($news) ? 'ویرایش خبر' : 'خبر جدید')
@section('content')

<div class="page-title">
    <h2>{{ isset($news) ? '✏️ ویرایش خبر' : '➕ خبر جدید' }}</h2>
</div>

<form method="POST" action="{{ isset($news) ? route('admin.village.news.update', [$village->id, $news->id]) : route('admin.village.news.store', $village->id) }}" enctype="multipart/form-data">
    @csrf
    @if(isset($news)) @method('PUT') @endif

    <div class="card">
        <h3>📝 اطلاعات خبر</h3>

        <div class="field">
            <label>عنوان خبر *</label>
            <input type="text" name="title" value="{{ old('title', $news->title ?? '') }}" required>
        </div>

        <div class="field">
            <label>خلاصه خبر</label>
            <textarea name="excerpt" style="min-height:80px;">{{ old('excerpt', $news->excerpt ?? '') }}</textarea>
            <div class="hint">اختیاری — در لیست اخبار نمایش داده می‌شود (حداکثر ۵۰۰ کاراکتر)</div>
        </div>

        <div class="field">
            <label>متن کامل خبر *</label>
            <textarea name="body" required style="min-height:240px;">{{ old('body', $news->body ?? '') }}</textarea>
        </div>

        <div class="field">
            <label>تصویر خبر</label>
            <input type="file" name="image" accept="image/*">
            @if(isset($news) && $news->image)
                <img src="{{ asset('storage/' . $news->image) }}" style="max-width:200px; margin-top:10px; border-radius:8px;">
            @endif
            <div class="hint">فرمت JPG/PNG، حداکثر ۵ مگابایت</div>
        </div>
    </div>

    <div class="card">
        <h3>⚙️ تنظیمات انتشار</h3>

        <label class="checkbox-field">
            <input type="checkbox" name="is_pinned" value="1" {{ old('is_pinned', $news->is_pinned ?? false) ? 'checked' : '' }}>
            <span>📌 سنجاق در بالای لیست</span>
        </label>

        <label class="checkbox-field">
            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $news->is_published ?? true) ? 'checked' : '' }}>
            <span>✅ منتشر شده (اگر تیک نخورد، پیش‌نویس ذخیره می‌شود)</span>
        </label>
    </div>

    <div style="display:flex; gap:12px;">
        <button type="submit" class="btn btn-success">💾 ذخیره</button>
        <a href="{{ route('admin.village.news.index', $village->id) }}" class="btn btn-secondary">انصراف</a>
    </div>
</form>

@endsection

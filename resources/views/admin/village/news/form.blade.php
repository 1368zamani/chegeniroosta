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
            <label>تصویر اصلی خبر (کاور)</label>
            <input type="file" name="image" accept="image/*">
            @if(isset($news) && $news->image)
                <img src="{{ asset('storage/' . $news->image) }}" style="max-width:200px; margin-top:10px; border-radius:8px;">
            @endif
            <div class="hint">فرمت JPG/PNG، حداکثر ۲۵ مگابایت</div>
        </div>

        <div class="field">
            <label>گالری عکس‌ها (چند عکس)</label>
            <input type="file" name="images[]" accept="image/*" multiple>
            <div class="hint">می‌توانید چند عکس انتخاب کنید (Ctrl+کلیک یا Shift+کلیک). حداکثر ۲۵ مگابایت هر عکس</div>

            @if(isset($news) && $news->images->count() > 0)
                <div style="margin-top:14px;">
                    <label style="font-size:13px; color:#546e7a; margin-bottom:8px; display:block;">🖼 عکس‌های فعلی ({{ $news->images->count() }})</label>
                    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap:10px;">
                        @foreach($news->images as $img)
                            <div style="position:relative; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.08);">
                                <img src="{{ asset('storage/' . $img->image) }}" style="width:100%; height:100px; object-fit:cover; display:block;">
                                <form method="POST" action="{{ route('admin.village.news.image.destroy', [$village->id, $news->id, $img->id]) }}"
                                      onsubmit="return confirm('حذف این عکس؟');"
                                      style="position:absolute; top:5px; left:5px;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:rgba(229,57,53,.95); color:white; border:none; width:26px; height:26px; border-radius:50%; cursor:pointer; font-size:14px; line-height:1; padding:0;">×</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
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

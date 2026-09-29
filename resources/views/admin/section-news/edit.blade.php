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

        <div class="field">
            <label>گالری عکس‌ها (چند عکس)</label>
            <input type="file" name="images[]" accept="image/*" multiple>
            <div class="hint">
                می‌توانید چند عکس انتخاب کنید. حداکثر ۲۵ مگابایت هر عکس.
            </div>

            @if($news->images->count() > 0)
                <div style="margin-top:14px;">
                    <label style="font-size:13px;color:#546e7a;margin-bottom:8px;display:block;">
                        🖼 عکس‌های فعلی ({{ $news->images->count() }})
                    </label>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:10px;">
                        @foreach($news->images as $img)
                            <div style="position:relative;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);">
                                <img src="{{ asset('storage/' . $img->image) }}"
                                     style="width:100%;height:100px;object-fit:cover;display:block;">
                                <form method="POST"
                                      action="{{ route('admin.section-news.image.destroy', [$news->id, $img->id]) }}"
                                      onsubmit="return confirm('حذف این عکس؟');"
                                      style="position:absolute;top:5px;left:5px;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            style="background:rgba(229,57,53,.95);color:white;border:none;width:26px;height:26px;border-radius:50%;cursor:pointer;font-size:14px;line-height:1;padding:0;">×</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
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

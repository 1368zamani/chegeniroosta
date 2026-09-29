@extends('admin.section-news.layout')

@section('title', 'خبر جدید')
@section('header', 'انتشار خبر جدید')

@section('content')

<form method="POST" action="{{ route('admin.section-news.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="card">

        <div class="field">
            <label>بخش مربوطه *</label>
            <select name="section_id" required>
                <option value="">— انتخاب بخش —</option>
                @foreach($sections as $s)
                    <option value="{{ $s->id }}" {{ old('section_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
            <div class="hint">خبر زیر نظر این بخش منتشر می‌شود.</div>
        </div>

        <div class="field">
            <label>عنوان خبر *</label>
            <input type="text" name="title" value="{{ old('title') }}" required>
        </div>

        <div class="field">
            <label>خلاصه خبر</label>
            <textarea name="excerpt" style="min-height:80px;">{{ old('excerpt') }}</textarea>
            <div class="hint">اختیاری — در لیست اخبار نمایش داده می‌شود (حداکثر ۵۰۰ کاراکتر).</div>
        </div>

        <div class="field">
            <label>متن کامل خبر *</label>
            <textarea name="content" required>{{ old('content') }}</textarea>
        </div>

        <div class="field">
            <label>تصویر اصلی خبر (کاور)</label>
            <input type="file" name="image" accept="image/*">
            <div class="hint">اختیاری — فرمت JPG یا PNG، حداکثر ۲۵ مگابایت.</div>
        </div>

        <div class="field">
            <label>گالری عکس‌های خبر (چند عکس)</label>
            <input type="file" name="images[]" accept="image/*" multiple>
            <div class="hint">
                می‌توانید چند عکس انتخاب کنید (Ctrl+کلیک یا Shift+کلیک). حداکثر ۲۵ مگابایت هر عکس.
            </div>
        </div>

        <div class="checkbox-field">
            <input type="checkbox" name="is_pinned" value="1" id="is_pinned" {{ old('is_pinned') ? 'checked' : '' }}>
            <label for="is_pinned">📌 این خبر در بالای لیست سنجاق شود</label>
        </div>

        <div class="checkbox-field">
            <input type="checkbox" name="is_published" value="1" id="is_published" {{ old('is_published', true) ? 'checked' : '' }}>
            <label for="is_published">✅ انتشار فوری (اگر تیک نخورد، پیش‌نویس ذخیره می‌شود)</label>
        </div>

        <div style="display:flex; gap:12px; margin-top:24px;">
            <button type="submit" class="btn btn-primary">💾 ذخیره و انتشار</button>
            <a href="{{ route('admin.section-news.index') }}" class="btn btn-secondary">انصراف</a>
        </div>

    </div>

</form>

@endsection

@extends('admin.village.layout')

@section('title', isset($resolution) ? 'ویرایش مصوبه' : 'مصوبه جدید')
@section('content')

<div class="page-title">
    <h2>{{ isset($resolution) ? '✏️ ویرایش مصوبه' : '➕ مصوبه جدید' }}</h2>
</div>

<form method="POST" action="{{ isset($resolution) ? route('admin.village.resolutions.update', [$village->id, $resolution->id]) : route('admin.village.resolutions.store', $village->id) }}" enctype="multipart/form-data">
    @csrf
    @if(isset($resolution)) @method('PUT') @endif

    <div class="card">
        <h3>📋 اطلاعات جلسه</h3>

        <div class="row-2">
            <div class="field">
                <label>شماره جلسه</label>
                <input type="text" name="session_number" value="{{ old('session_number', $resolution->session_number ?? '') }}" placeholder="مثال: ۱۲">
            </div>
            <div class="field">
                <label>تاریخ جلسه</label>
                <input type="date" name="session_date" value="{{ old('session_date', isset($resolution) && $resolution->session_date ? $resolution->session_date->format('Y-m-d') : '') }}" dir="ltr">
            </div>
        </div>

        <div class="field">
            <label>عنوان مصوبه *</label>
            <input type="text" name="title" value="{{ old('title', $resolution->title ?? '') }}" required placeholder="مثال: تعمیر پل ورودی روستا">
        </div>

        <div class="field">
            <label>متن کامل مصوبه</label>
            <textarea name="description" style="min-height:140px;">{{ old('description', $resolution->description ?? '') }}</textarea>
        </div>
    </div>

    <div class="card">
        <h3>📊 وضعیت پیگیری</h3>

        <div class="row-2">
            <div class="field">
                <label>وضعیت اجرا *</label>
                <select name="status" required>
                    <option value="approved"    @selected(old('status', $resolution->status ?? 'approved') == 'approved')>تصویب شده</option>
                    <option value="in_progress" @selected(old('status', $resolution->status ?? '') == 'in_progress')>در حال اجرا</option>
                    <option value="completed"   @selected(old('status', $resolution->status ?? '') == 'completed')>اجرا شده</option>
                    <option value="suspended"   @selected(old('status', $resolution->status ?? '') == 'suspended')>متوقف شده</option>
                    <option value="rejected"    @selected(old('status', $resolution->status ?? '') == 'rejected')>رد شده</option>
                </select>
            </div>
            <div class="field">
                <label>درصد پیشرفت (۰ تا ۱۰۰)</label>
                <input type="number" name="progress_percent" value="{{ old('progress_percent', $resolution->progress_percent ?? 0) }}" min="0" max="100">
            </div>
        </div>

        <div class="row-2">
            <div class="field">
                <label>بودجه مصوب (ریال)</label>
                <input type="number" name="budget" value="{{ old('budget', $resolution->budget ?? '') }}" min="0" dir="ltr" placeholder="اختیاری">
            </div>
            <div class="field">
                <label>مهلت اجرا</label>
                <input type="date" name="deadline" value="{{ old('deadline', isset($resolution) && $resolution->deadline ? $resolution->deadline->format('Y-m-d') : '') }}" dir="ltr">
            </div>
        </div>

        <div class="field">
            <label>توضیحات پیگیری (دهیار)</label>
            <textarea name="admin_note" style="min-height:100px;" placeholder="پیشرفت‌ها، موانع، اقدامات انجام شده...">{{ old('admin_note', $resolution->admin_note ?? '') }}</textarea>
        </div>

        <div class="field">
            <label>فایل پیوست (اختیاری)</label>
            <input type="file" name="attachment">
            @if(isset($resolution) && $resolution->attachment)
                <div style="margin-top:8px;">
                    <a href="{{ asset('storage/' . $resolution->attachment) }}" target="_blank" style="color:#1565c0;text-decoration:none;font-size:13px;">📎 دانلود فایل فعلی</a>
                </div>
            @endif
            <div class="hint">حداکثر ۲۵ مگابایت — PDF، تصویر، Word</div>
        </div>
    </div>

    <div class="card">
        <h3>⚙️ تنظیمات نمایش</h3>

        <label class="checkbox-field">
            <input type="checkbox" name="is_public" value="1" {{ old('is_public', $resolution->is_public ?? true) ? 'checked' : '' }}>
            <span>👁 نمایش در سایت عمومی روستا</span>
        </label>

        <div class="field">
            <label>ترتیب نمایش</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $resolution->sort_order ?? 0) }}">
            <div class="hint">عدد کمتر = بالاتر</div>
        </div>
    </div>

    <div style="display:flex; gap:12px;">
        <button type="submit" class="btn btn-success">💾 ذخیره</button>
        <a href="{{ route('admin.village.resolutions.index', $village->id) }}" class="btn btn-secondary">انصراف</a>
    </div>
</form>

@endsection

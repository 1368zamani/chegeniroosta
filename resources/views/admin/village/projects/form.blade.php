@extends('admin.village.layout')

@section('title', isset($project) ? 'ویرایش پروژه' : 'پروژه جدید')
@section('content')

<div class="page-title">
    <h2>{{ isset($project) ? '✏️ ویرایش پروژه' : '➕ پروژه جدید' }}</h2>
</div>

<form method="POST" action="{{ isset($project) ? route('admin.village.projects.update', [$village->id, $project->id]) : route('admin.village.projects.store', $village->id) }}" enctype="multipart/form-data">
    @csrf
    @if(isset($project)) @method('PUT') @endif

    <div class="card">
        <h3>📋 اطلاعات پروژه</h3>

        <div class="field">
            <label>عنوان پروژه *</label>
            <input type="text" name="title" value="{{ old('title', $project->title ?? '') }}" required>
        </div>

        <div class="field">
            <label>توضیحات</label>
            <textarea name="description">{{ old('description', $project->description ?? '') }}</textarea>
        </div>

        <div class="row-2">
            <div class="field">
                <label>وضعیت *</label>
                <select name="status" required>
                    <option value="planned" {{ old('status', $project->status ?? '') == 'planned' ? 'selected' : '' }}>در برنامه</option>
                    <option value="in_progress" {{ old('status', $project->status ?? '') == 'in_progress' ? 'selected' : '' }}>در حال اجرا</option>
                    <option value="done" {{ old('status', $project->status ?? '') == 'done' ? 'selected' : '' }}>انجام شده</option>
                    <option value="paused" {{ old('status', $project->status ?? '') == 'paused' ? 'selected' : '' }}>متوقف</option>
                </select>
            </div>

            <div class="field">
                <label>درصد پیشرفت (0-100)</label>
                <input type="number" name="progress" min="0" max="100" value="{{ old('progress', $project->progress ?? 0) }}">
            </div>
        </div>

        <div class="row-2">
            <div class="field">
                <label>پیمانکار / دستگاه مجری</label>
                <input type="text" name="contractor" value="{{ old('contractor', $project->contractor ?? '') }}">
            </div>
            <div class="field">
                <label>اعتبار / بودجه</label>
                <input type="text" name="budget" value="{{ old('budget', $project->budget ?? '') }}" placeholder="مثلاً: 1,500,000,000 ریال">
            </div>
        </div>

        <div class="row-2">
            <div class="field">
                <label>تاریخ شروع</label>
                <input type="date" name="start_date" value="{{ old('start_date', optional($project->start_date ?? null)->format('Y-m-d')) }}">
            </div>
            <div class="field">
                <label>تاریخ پایان</label>
                <input type="date" name="end_date" value="{{ old('end_date', optional($project->end_date ?? null)->format('Y-m-d')) }}">
            </div>
        </div>
    </div>

    <div class="card">
        <h3>📷 تصاویر پروژه</h3>

        <div class="field">
            <label>تصویر شاخص (Cover)</label>
            <input type="file" name="cover_image" accept="image/*">
            @if(isset($project) && $project->cover_image)
                <img src="{{ asset('storage/' . $project->cover_image) }}" style="max-width:150px; margin-top:10px; border-radius:8px;">
            @endif
        </div>

        <div class="row-2">
            <div class="field">
                <label>تصویر قبل (Before)</label>
                <input type="file" name="before_image" accept="image/*">
                @if(isset($project) && $project->before_image)
                    <img src="{{ asset('storage/' . $project->before_image) }}" style="max-width:120px; margin-top:8px; border-radius:6px;">
                @endif
            </div>
            <div class="field">
                <label>تصویر بعد (After)</label>
                <input type="file" name="after_image" accept="image/*">
                @if(isset($project) && $project->after_image)
                    <img src="{{ asset('storage/' . $project->after_image) }}" style="max-width:120px; margin-top:8px; border-radius:6px;">
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <h3>⚙️ تنظیمات</h3>
        <div class="field">
            <label>ترتیب نمایش</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $project->sort_order ?? 0) }}">
        </div>
    </div>

    <div style="display:flex; gap:12px;">
        <button type="submit" class="btn btn-success">💾 ذخیره</button>
        <a href="{{ route('admin.village.projects.index', $village->id) }}" class="btn btn-secondary">انصراف</a>
    </div>
</form>

@endsection

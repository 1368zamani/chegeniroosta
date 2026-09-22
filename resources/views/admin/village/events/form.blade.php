@extends('admin.village.layout')

@section('title', isset($event) ? 'ویرایش رویداد' : 'رویداد جدید')
@section('content')

<div class="page-title">
    <h2>{{ isset($event) ? '✏️ ویرایش رویداد' : '➕ رویداد جدید' }}</h2>
</div>

<form method="POST" action="{{ isset($event) ? route('admin.village.events.update', [$village->id, $event->id]) : route('admin.village.events.store', $village->id) }}">
    @csrf
    @if(isset($event)) @method('PUT') @endif

    <div class="card">
        <h3>📅 اطلاعات رویداد</h3>

        <div class="field">
            <label>عنوان *</label>
            <input type="text" name="title" value="{{ old('title', $event->title ?? '') }}" required>
        </div>

        <div class="field">
            <label>توضیحات</label>
            <textarea name="description">{{ old('description', $event->description ?? '') }}</textarea>
        </div>

        <div class="row-2">
            <div class="field">
                <label>تاریخ رویداد *</label>
                <input type="date" name="event_date" value="{{ old('event_date', optional($event->event_date ?? null)->format('Y-m-d')) }}" required>
            </div>
            <div class="field">
                <label>ساعت</label>
                <input type="text" name="event_time" value="{{ old('event_time', $event->event_time ?? '') }}" placeholder="مثلاً 17:00">
            </div>
        </div>

        <div class="row-2">
            <div class="field">
                <label>محل برگزاری</label>
                <input type="text" name="location" value="{{ old('location', $event->location ?? '') }}">
            </div>
            <div class="field">
                <label>نوع رویداد</label>
                <select name="event_type">
                    <option value="general">عمومی</option>
                    <option value="religious">مذهبی</option>
                    <option value="sport">ورزشی</option>
                    <option value="cultural">فرهنگی</option>
                    <option value="meeting">جلسه</option>
                </select>
            </div>
        </div>

        <label class="checkbox-field">
            <input type="checkbox" name="is_important" value="1" {{ old('is_important', $event->is_important ?? false) ? 'checked' : '' }}>
            <span>⭐ رویداد مهم (با تأکید نمایش داده شود)</span>
        </label>
    </div>

    <div style="display:flex; gap:12px;">
        <button type="submit" class="btn btn-success">💾 ذخیره</button>
        <a href="{{ route('admin.village.events.index', $village->id) }}" class="btn btn-secondary">انصراف</a>
    </div>
</form>

@endsection

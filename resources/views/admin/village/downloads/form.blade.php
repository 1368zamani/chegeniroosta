@extends('admin.village.layout')

@section('title', $download->exists ? 'ویرایش فایل' : 'بارگذاری فایل جدید')

@section('content')

@php
    $isEdit = $download->exists;
@endphp

<div class="page-title">
    <h2>{{ $isEdit ? '✏️ ویرایش فایل' : '➕ بارگذاری فایل جدید' }} — {{ $village->name }}</h2>
    <a href="{{ route('admin.village.downloads.index', $village->id) }}" class="btn"
       style="background:#eceff1; color:#37474f;">← بازگشت</a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
@endif

<form method="POST"
      action="{{ $isEdit
            ? route('admin.village.downloads.update', [$village->id, $download->id])
            : route('admin.village.downloads.store', $village->id) }}"
      enctype="multipart/form-data">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="card" style="background:#fff; border-radius:12px; padding:24px;
                             box-shadow:0 4px 16px rgba(0,0,0,.05); margin-bottom:18px;">

        <div style="margin-bottom:18px;">
            <label style="display:block; font-size:13px; font-weight:bold; color:#37474f; margin-bottom:6px;">
                عنوان فایل *
            </label>
            <input type="text" name="title" required maxlength="200"
                   value="{{ old('title', $download->title) }}"
                   placeholder="مثلاً: فرم درخواست کتبی"
                   style="width:100%; padding:11px 14px; border:1.5px solid #d7dee3; border-radius:8px;
                          font-family:inherit; font-size:14px;">
            <div style="font-size:11.5px; color:#90a4ae; margin-top:5px;">
                این عنوان به شهروند نمایش داده می‌شود.
            </div>
        </div>

        <div style="margin-bottom:18px;">
            <label style="display:block; font-size:13px; font-weight:bold; color:#37474f; margin-bottom:6px;">
                توضیح کوتاه (اختیاری)
            </label>
            <input type="text" name="description" maxlength="500"
                   value="{{ old('description', $download->description) }}"
                   placeholder="مثلاً: برای درخواست‌های عمرانی این فرم را تکمیل کنید"
                   style="width:100%; padding:11px 14px; border:1.5px solid #d7dee3; border-radius:8px;
                          font-family:inherit; font-size:14px;">
        </div>

        <div style="margin-bottom:18px;">
            <label style="display:block; font-size:13px; font-weight:bold; color:#37474f; margin-bottom:6px;">
                فایل {{ $isEdit ? '(اگر خالی بماند، فایل فعلی حفظ می‌شود)' : '*' }}
            </label>
            <input type="file" name="file" {{ $isEdit ? '' : 'required' }}
                   style="width:100%; padding:11px 14px; border:1.5px solid #d7dee3; border-radius:8px;
                          font-family:inherit; font-size:13px; background:#fafbfc;">
            <div style="font-size:11.5px; color:#90a4ae; margin-top:5px;">
                حداکثر حجم: <strong>{{ \App\Support\Number::fa(number_format($maxKb / 1024)) }} مگابایت</strong>
                — همه‌ی انواع فایل مجازند (PDF، Word، Excel، تصویر، ZIP و…)
            </div>

            @if($isEdit && $download->file_path)
                <div style="margin-top:12px; background:#f5f7fa; border-radius:8px; padding:12px 14px;
                            display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                    <span style="font-size:18px;">{{ $download->icon() }}</span>
                    <span style="font-size:12.5px; color:#37474f; word-break:break-all;">
                        {{ $download->file_name }}
                    </span>
                    <span style="font-size:11.5px; color:#90a4ae;">({{ $download->sizeLabel() }})</span>
                </div>
            @endif
        </div>

        <div style="display:flex; gap:18px; flex-wrap:wrap; margin-bottom:18px;">
            <div style="flex:1; min-width:180px;">
                <label style="display:block; font-size:13px; font-weight:bold; color:#37474f; margin-bottom:6px;">
                    ترتیب نمایش
                </label>
                <input type="number" name="sort_order" min="0" max="9999"
                       value="{{ old('sort_order', $download->sort_order ?? 0) }}"
                       style="width:100%; padding:11px 14px; border:1.5px solid #d7dee3; border-radius:8px;
                              font-family:inherit; font-size:14px;" dir="ltr">
                <div style="font-size:11.5px; color:#90a4ae; margin-top:5px;">عدد کوچکتر = بالاتر</div>
            </div>

            <div style="flex:1; min-width:180px; display:flex; align-items:flex-end; padding-bottom:6px;">
                <label style="display:flex; align-items:center; gap:9px; cursor:pointer; font-size:13.5px;">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1"
                           @checked(old('is_active', $download->is_active ?? true))
                           style="width:20px; height:20px;">
                    <span style="font-weight:bold; color:#37474f;">فعال (نمایش به شهروند)</span>
                </label>
            </div>
        </div>

        <div style="display:flex; gap:10px; justify-content:flex-end; padding-top:16px;
                    border-top:1px solid #eceff1;">
            <a href="{{ route('admin.village.downloads.index', $village->id) }}"
               class="btn" style="background:#eceff1; color:#37474f;">انصراف</a>
            <button type="submit" class="btn btn-primary">
                {{ $isEdit ? '💾 ذخیره‌ی تغییرات' : '📤 بارگذاری فایل' }}
            </button>
        </div>
    </div>
</form>

@endsection

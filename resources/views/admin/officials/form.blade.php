@extends('admin.village.layout')

@section('title', isset($official) ? 'ویرایش مسئول' : 'افزودن مسئول')
@section('content')

<div class="page-title">
    <h2>{{ isset($official) ? '✏️ ویرایش مسئول' : '➕ افزودن مسئول جدید' }}</h2>
</div>

<form method="POST" action="{{ isset($official) ? route('admin.officials.update', $official->id) : route('admin.officials.store') }}" enctype="multipart/form-data">
    @csrf
    @if(isset($official)) @method('PUT') @endif

    <div class="card">
        <h3>🏛 اطلاعات مسئول</h3>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="field">
                <label>نوع مسئول *</label>
                <select name="type" required style="width:100%;padding:11px 14px;border:1px solid #cfd8dc;border-radius:8px;font-family:inherit;">
                    <option value="governor" @selected(old('type', $official->type ?? '') == 'governor')>فرماندار</option>
                    <option value="deputy" @selected(old('type', $official->type ?? 'deputy') == 'deputy')>معاون فرماندار</option>
                </select>
            </div>
            <div class="field">
                <label>ترتیب نمایش</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $official->sort_order ?? 0) }}" style="width:100%;padding:11px 14px;border:1px solid #cfd8dc;border-radius:8px;font-family:inherit;">
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="field">
                <label>نام و نام خانوادگی *</label>
                <input type="text" name="name" value="{{ old('name', $official->name ?? '') }}" required style="width:100%;padding:11px 14px;border:1px solid #cfd8dc;border-radius:8px;font-family:inherit;">
            </div>
            <div class="field">
                <label>سمت *</label>
                <input type="text" name="position" value="{{ old('position', $official->position ?? '') }}" required placeholder="فرماندار / معاون سیاسی / ..." style="width:100%;padding:11px 14px;border:1px solid #cfd8dc;border-radius:8px;font-family:inherit;">
            </div>
        </div>

        <div class="field">
            <label>عکس</label>
            <input type="file" name="photo" accept="image/*">
            @if(isset($official) && $official->photo)
                <img src="{{ asset('storage/' . $official->photo) }}" style="max-width:150px;margin-top:10px;border-radius:50%;">
            @endif
        </div>
    </div>

    <div class="card">
        <h3>📝 اطلاعات تفصیلی</h3>

        <div class="field">
            <label>بیوگرافی</label>
            <textarea name="bio" style="width:100%;min-height:100px;padding:11px 14px;border:1px solid #cfd8dc;border-radius:8px;font-family:inherit;resize:vertical;">{{ old('bio', $official->bio ?? '') }}</textarea>
        </div>

        <div class="field">
            <label>تحصیلات</label>
            <textarea name="education" style="width:100%;min-height:80px;padding:11px 14px;border:1px solid #cfd8dc;border-radius:8px;font-family:inherit;resize:vertical;">{{ old('education', $official->education ?? '') }}</textarea>
        </div>

        <div class="field">
            <label>سوابق شغلی</label>
            <textarea name="experience" style="width:100%;min-height:100px;padding:11px 14px;border:1px solid #cfd8dc;border-radius:8px;font-family:inherit;resize:vertical;">{{ old('experience', $official->experience ?? '') }}</textarea>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="field">
                <label>تلفن</label>
                <input type="text" name="phone" value="{{ old('phone', $official->phone ?? '') }}" dir="ltr" style="width:100%;padding:11px 14px;border:1px solid #cfd8dc;border-radius:8px;font-family:inherit;">
            </div>
            <div class="field">
                <label>ایمیل</label>
                <input type="email" name="email" value="{{ old('email', $official->email ?? '') }}" dir="ltr" style="width:100%;padding:11px 14px;border:1px solid #cfd8dc;border-radius:8px;font-family:inherit;">
            </div>
        </div>

        <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#f8fafc;border-radius:8px;cursor:pointer;">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $official->is_active ?? true) ? 'checked' : '' }}>
            <span>✅ فعال (نمایش در سایت)</span>
        </label>
    </div>

    <div style="display:flex;gap:12px;margin-top:20px;">
        <button type="submit" class="btn btn-primary" style="background:#2e7d32;color:#fff;padding:11px 24px;border:none;border-radius:8px;cursor:pointer;font-family:inherit;font-weight:600;">💾 ذخیره</button>
        <a href="{{ route('admin.officials.index') }}" class="btn btn-secondary" style="background:#cfd8dc;color:#37474f;padding:11px 20px;border-radius:8px;text-decoration:none;">انصراف</a>
    </div>
</form>

@endsection

@extends('admin.sections.layout')

@section('title', isset($member) ? 'ویرایش عضو شورا' : 'عضو جدید شورا')
@section('content')

<div class="page-title">
    <h2>{{ isset($member) ? '✏️ ویرایش عضو شورای بخش' : '➕ عضو جدید شورای بخش' }}</h2>
</div>

<form method="POST" action="{{ isset($member) ? route('admin.sections.council.update', [$section->id, $member->id]) : route('admin.sections.council.store', $section->id) }}" enctype="multipart/form-data">
    @csrf
    @if(isset($member)) @method('PUT') @endif

    <div class="card">
        <h3>👥 اطلاعات عضو شورا</h3>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="field">
                <label>نام و نام خانوادگی *</label>
                <input type="text" name="name" value="{{ old('name', $member->name ?? '') }}" required style="width:100%;padding:11px 14px;border:1px solid #cfd8dc;border-radius:8px;font-family:inherit;">
            </div>
            <div class="field">
                <label>سمت *</label>
                <input type="text" name="position" value="{{ old('position', $member->position ?? '') }}" required placeholder="رئیس، نایب رئیس، عضو، ..." style="width:100%;padding:11px 14px;border:1px solid #cfd8dc;border-radius:8px;font-family:inherit;">
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="field">
                <label>شماره تماس</label>
                <input type="text" name="phone" value="{{ old('phone', $member->phone ?? '') }}" dir="ltr" style="width:100%;padding:11px 14px;border:1px solid #cfd8dc;border-radius:8px;font-family:inherit;">
            </div>
            <div class="field">
                <label>ترتیب نمایش</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $member->sort_order ?? 0) }}" style="width:100%;padding:11px 14px;border:1px solid #cfd8dc;border-radius:8px;font-family:inherit;">
            </div>
        </div>

        <div class="field">
            <label>بیوگرافی کوتاه (اختیاری)</label>
            <textarea name="bio" style="width:100%;min-height:100px;padding:11px 14px;border:1px solid #cfd8dc;border-radius:8px;font-family:inherit;resize:vertical;">{{ old('bio', $member->bio ?? '') }}</textarea>
        </div>

        <div class="field">
            <label>عکس</label>
            <input type="file" name="photo" accept="image/*">
            @if(isset($member) && $member->photo)
                <div style="margin-top:10px;">
                    <img src="{{ asset('storage/' . $member->photo) }}" style="width:100px;height:100px;border-radius:50%;object-fit:cover;">
                </div>
            @endif
        </div>

        <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#f8fafc;border-radius:8px;cursor:pointer;">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $member->is_active ?? true) ? 'checked' : '' }}>
            <span>✅ عضو فعال (نمایش در سایت)</span>
        </label>
    </div>

    <div style="display:flex;gap:12px;margin-top:20px;">
        <button type="submit" class="btn btn-primary" style="background:#2e7d32;color:#fff;padding:11px 24px;border:none;border-radius:8px;cursor:pointer;font-family:inherit;font-weight:600;">💾 ذخیره</button>
        <a href="{{ route('admin.sections.council.index', $section->id) }}" class="btn btn-secondary" style="background:#cfd8dc;color:#37474f;padding:11px 20px;border-radius:8px;text-decoration:none;">انصراف</a>
    </div>
</form>

@endsection

@extends('admin.sections.layout')
@section('title', 'افزودن همکار')
@section('content')
<div class="page-title"><h2>➕ افزودن همکار جدید</h2></div>
<div class="card">
    <form method="POST" action="{{ route('admin.sections.staff.store', $section->id) }}" enctype="multipart/form-data">
        @csrf
        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:8px; font-weight:bold;">نام و نام خانوادگی *</label>
            <input type="text" name="name" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:8px;">
        </div>
        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:8px; font-weight:bold;">سمت *</label>
            <input type="text" name="position" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:8px;">
        </div>
        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:8px; font-weight:bold;">شماره همراه</label>
            <input type="text" name="phone" dir="ltr" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:8px;">
        </div>
        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:8px; font-weight:bold;">عکس پروفایل (اختیاری)</label>
            <input type="file" name="photo" accept="image/*" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:8px;">
        </div>
        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:8px; font-weight:bold;">ترتیب نمایش</label>
            <input type="number" name="sort_order" value="0" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:8px;">
        </div>
        <button type="submit" class="btn btn-primary" style="background:#2e7d32; color:white; padding:12px 24px; border:none; border-radius:8px; cursor:pointer;">💾 ذخیره</button>
        <a href="{{ route('admin.sections.staff.index', $section->id) }}" style="margin-right:10px; text-decoration:none; color:#555;">انصراف</a>
    </form>
</div>
@endsection

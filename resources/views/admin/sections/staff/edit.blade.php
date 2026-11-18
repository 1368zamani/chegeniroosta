@extends('admin.sections.layout')
@section('title', 'ویرایش همکار')
@section('content')
<div class="page-title"><h2>✏️ ویرایش همکار</h2></div>
<div class="card">
    <form method="POST" action="{{ route('admin.sections.staff.update', [$section->id, $staff->id]) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:8px; font-weight:bold;">نام و نام خانوادگی *</label>
            <input type="text" name="name" value="{{ $staff->name }}" required style="width:100%; padding:10px; border:1px solid #e6d8c3; border-radius:8px;">
        </div>
        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:8px; font-weight:bold;">سمت *</label>
            <input type="text" name="position" value="{{ $staff->position }}" required style="width:100%; padding:10px; border:1px solid #e6d8c3; border-radius:8px;">
        </div>
        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:8px; font-weight:bold;">شماره همراه</label>
            <input type="text" name="phone" value="{{ $staff->phone }}" dir="ltr" style="width:100%; padding:10px; border:1px solid #e6d8c3; border-radius:8px;">
        </div>
        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:8px; font-weight:bold;">عکس پروفایل</label>
            @if($staff->photo)
                <img src="{{ asset('storage/' . $staff->photo) }}" style="width:80px; height:80px; border-radius:50%; object-fit:cover; margin-bottom:10px; border:2px solid #ddd;">
            @endif
            <input type="file" name="photo" accept="image/*" style="width:100%; padding:10px; border:1px solid #e6d8c3; border-radius:8px;">
        </div>
        <div style="margin-bottom:15px;">
            <label style="display:block; margin-bottom:8px; font-weight:bold;">ترتیب نمایش</label>
            <input type="number" name="sort_order" value="{{ $staff->sort_order }}" style="width:100%; padding:10px; border:1px solid #e6d8c3; border-radius:8px;">
        </div>
        <button type="submit" class="btn btn-primary" style="background:#c94a2b; color:white; padding:12px 24px; border:none; border-radius:8px; cursor:pointer;">💾 ذخیره تغییرات</button>
        <a href="{{ route('admin.sections.staff.index', $section->id) }}" style="margin-right:10px; text-decoration:none; color:#555;">انصراف</a>
    </form>
</div>
@endsection

@extends('admin.section-gallery.layout')
@section('title', 'گالری ' . $section->name)
@section('header', '🖼 گالری تصاویر: ' . $section->name)

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h2 style="margin:0; color:#2e7d32;">{{ $images->count() }} تصویر</h2>
    <a href="{{ route('admin.sections.gallery.create', $section->id) }}" class="btn btn-primary">➕ افزودن تصاویر</a>
</div>

@if($images->count() > 0)
    <div class="gallery-grid">
        @foreach($images as $image)
            <div class="gallery-item">
                <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $image->caption }}">
                @if($image->caption)<div class="gallery-caption">{{ $image->caption }}</div>@endif
                <div class="gallery-actions">
                    <form method="POST" action="{{ route('admin.sections.gallery.destroy', [$section->id, $image->id]) }}" onsubmit="return confirm('حذف این تصویر؟');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">🗑 حذف</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="empty">
        <div class="icon">📷</div>
        <p>هنوز تصویری برای این بخش آپلود نشده است.</p>
        <a href="{{ route('admin.sections.gallery.create', $section->id) }}" class="btn btn-primary" style="margin-top:16px;">➕ آپلود اولین تصاویر</a>
    </div>
@endif
@endsection

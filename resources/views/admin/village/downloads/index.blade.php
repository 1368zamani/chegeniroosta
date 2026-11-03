@extends('admin.village.layout')

@section('title', 'فایل‌های قابل دانلود')

@section('content')

<div class="page-title">
    <h2>📥 فایل‌های قابل دانلود — {{ $village->name }}</h2>
    <a href="{{ route('admin.village.downloads.create', $village->id) }}" class="btn btn-primary">
        ➕ بارگذاری فایل جدید
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
@endif

<div class="card" style="background:#f1f8e9; border-right:4px solid #2e7d32; margin-bottom:18px;">
    <p style="margin:0; font-size:13px; color:#33691e; line-height:1.9;">
        این فایل‌ها در <strong>پایین صفحه‌ی «ثبت درخواست»</strong> سایت روستا نمایش داده می‌شوند
        و شهروندان می‌توانند دانلود کنند.
        <br>
        نمونه‌ها: فرم درخواست کتبی، آیین‌نامه، اطلاعیه، نقشه، جدول عوارض و…
    </p>
</div>

@if($downloads->count() > 0)

<table>
    <thead>
        <tr>
            <th style="width:50px;">#</th>
            <th>عنوان</th>
            <th style="width:130px;">نام فایل</th>
            <th style="width:100px;">حجم</th>
            <th style="width:90px;">دانلود</th>
            <th style="width:90px;">وضعیت</th>
            <th style="width:80px;">ترتیب</th>
            <th style="width:180px;">عملیات</th>
        </tr>
    </thead>
    <tbody>
        @foreach($downloads as $i => $d)
            <tr>
                <td>{{ $downloads->firstItem() + $i }}</td>
                <td>
                    <strong style="font-size:14px;">{{ $d->title }}</strong>
                    @if($d->description)
                        <div style="font-size:12px; color:#78909c; margin-top:3px;">{{ $d->description }}</div>
                    @endif
                </td>
                <td style="font-size:11.5px; color:#607d8b; word-break:break-all;">
                    {{ $d->icon() }} {{ \Illuminate\Support\Str::limit($d->file_name, 24) }}
                </td>
                <td style="font-size:12.5px;">{{ $d->sizeLabel() }}</td>
                <td style="font-size:12.5px;">{{ \App\Support\Number::fa(number_format($d->download_count)) }}</td>
                <td>
                    @if($d->is_active)
                        <span class="badge" style="background:#c8e6c9;color:#2e7d32;">فعال</span>
                    @else
                        <span class="badge" style="background:#ffcdd2;color:#c62828;">غیرفعال</span>
                    @endif
                </td>
                <td style="font-size:12.5px;">{{ \App\Support\Number::fa($d->sort_order) }}</td>
                <td style="white-space:nowrap;">
                    <a href="{{ route('admin.village.downloads.edit', [$village->id, $d->id]) }}"
                       class="btn btn-primary" style="font-size:12px; padding:6px 12px;">✏️ ویرایش</a>

                    <form method="POST"
                          action="{{ route('admin.village.downloads.destroy', [$village->id, $d->id]) }}"
                          style="display:inline;"
                          onsubmit="return confirm('فایل «{{ $d->title }}» حذف شود؟\n\nاین کار قابل بازگشت نیست.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn"
                                style="background:#e53935; color:#fff; font-size:12px; padding:6px 12px;">
                            🗑 حذف
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div style="margin-top:16px;">
    {{ $downloads->links() }}
</div>

@else
    <div style="background:#fff; border-radius:12px; padding:60px 20px; text-align:center;
                box-shadow:0 4px 16px rgba(0,0,0,.05);">
        <div style="font-size:44px; margin-bottom:12px; opacity:.5;">📥</div>
        <p style="color:#90a4ae; margin:0 0 16px; font-size:14px;">
            هنوز فایلی بارگذاری نشده است.
        </p>
        <a href="{{ route('admin.village.downloads.create', $village->id) }}"
           class="btn btn-primary">➕ اولین فایل را بارگذاری کنید</a>
    </div>
@endif

@endsection

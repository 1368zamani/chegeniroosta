@extends('admin.sections.layout')
@section('title', 'مدیریت همکاران')
@section('content')
<div class="page-title" style="display:flex; justify-content:space-between; align-items:center;">
    <h2>👥 کارکنان بخشداری {{ $section->name }}</h2>
    <div>
        <a href="{{ route('admin.sections.staff.create', $section->id) }}" class="btn btn-primary" style="background:#2e7d32; padding:10px 15px; border-radius:8px; text-decoration:none; color:white;">➕ افزودن همکار</a>
        <a href="{{ route('admin.sections.edit', $section->id) }}" class="btn btn-secondary" style="background:#cfd8dc; padding:10px 15px; border-radius:8px; text-decoration:none; color:#333; margin-right:10px;">← بازگشت به تنظیمات</a>
    </div>
</div>

@if(session('success'))
    <div style="background:#c8e6c9; color:#2e7d32; padding:15px; border-radius:8px; margin-bottom:20px;">{{ session('success') }}</div>
@endif

<div class="card">
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th style="padding:12px; text-align:right; border-bottom:2px solid #eee;">ترتیب</th>
                <th style="padding:12px; text-align:right; border-bottom:2px solid #eee;">نام و نام خانوادگی</th>
                <th style="padding:12px; text-align:right; border-bottom:2px solid #eee;">سمت</th>
                <th style="padding:12px; text-align:right; border-bottom:2px solid #eee;">شماره همراه</th>
                <th style="padding:12px; text-align:right; border-bottom:2px solid #eee;">عملیات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($staffs as $staff)
            <tr>
                <td style="padding:12px; border-bottom:1px solid #eee;">{{ $staff->sort_order }}</td>
                <td style="padding:12px; border-bottom:1px solid #eee;"><strong>{{ $staff->name }}</strong></td>
                <td style="padding:12px; border-bottom:1px solid #eee;">{{ $staff->position }}</td>
                <td style="padding:12px; border-bottom:1px solid #eee;" dir="ltr">{{ $staff->phone ?? '—' }}</td>
                <td style="padding:12px; border-bottom:1px solid #eee;">
                    <a href="{{ route('admin.sections.staff.edit', [$section->id, $staff->id]) }}" class="btn" style="background:#2e7d32; color:white; padding:5px 10px; border-radius:5px; text-decoration:none; font-size:12px;">ویرایش</a>
                    <form method="POST" action="{{ route('admin.sections.staff.destroy', [$section->id, $staff->id]) }}" style="display:inline;" onsubmit="return confirm('حذف این همکار؟');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn" style="background:#e53935; color:white; padding:5px 10px; border-radius:5px; border:none; font-size:12px; cursor:pointer;">حذف</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="padding:20px; text-align:center; color:#78909c;">هیچ همکاری ثبت نشده است.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

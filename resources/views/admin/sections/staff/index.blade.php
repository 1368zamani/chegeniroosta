@extends('admin.sections.layout')
@section('title', 'مدیریت همکاران')
@section('content')
<div class="page-title" style="display:flex; justify-content:space-between; align-items:center;">
    <h2>👥 کارکنان بخشداری {{ $section->name }}</h2>
    <div>
        <a href="{{ route('admin.sections.staff.create', $section->id) }}" class="btn btn-primary" style="background:#c94a2b; padding:10px 15px; border-radius:8px; text-decoration:none; color:white;">➕ افزودن همکار</a>
        <a href="{{ route('admin.sections.edit', $section->id) }}" class="btn btn-secondary" style="background:#e6d8c3; padding:10px 15px; border-radius:8px; text-decoration:none; color:#333; margin-right:10px;">← بازگشت به تنظیمات</a>
    </div>
</div>

@if(session('success'))
    <div style="background:#fbdcd0; color:#c94a2b; padding:15px; border-radius:8px; margin-bottom:20px;">{{ session('success') }}</div>
@endif

<div class="card">
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th style="padding:12px; text-align:right; border-bottom:2px solid #f5efe4;">ترتیب</th>
                <th style="padding:12px; text-align:right; border-bottom:2px solid #f5efe4;">نام و نام خانوادگی</th>
                <th style="padding:12px; text-align:right; border-bottom:2px solid #f5efe4;">سمت</th>
                <th style="padding:12px; text-align:right; border-bottom:2px solid #f5efe4;">شماره همراه</th>
                <th style="padding:12px; text-align:right; border-bottom:2px solid #f5efe4;">عملیات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($staffs as $staff)
            <tr>
                <td style="padding:12px; border-bottom:1px solid #f5efe4;">{{ $staff->sort_order }}</td>
                <td style="padding:12px; border-bottom:1px solid #f5efe4;"><strong>{{ $staff->name }}</strong></td>
                <td style="padding:12px; border-bottom:1px solid #f5efe4;">{{ $staff->position }}</td>
                <td style="padding:12px; border-bottom:1px solid #f5efe4;" dir="ltr">{{ $staff->phone ?? '—' }}</td>
                <td style="padding:12px; border-bottom:1px solid #f5efe4;">
                    <a href="{{ route('admin.sections.staff.edit', [$section->id, $staff->id]) }}" class="btn" style="background:#c94a2b; color:white; padding:5px 10px; border-radius:5px; text-decoration:none; font-size:12px;">ویرایش</a>
                    <form method="POST" action="{{ route('admin.sections.staff.destroy', [$section->id, $staff->id]) }}" style="display:inline;" onsubmit="return confirm('حذف این همکار؟');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn" style="background:#b3261e; color:white; padding:5px 10px; border-radius:5px; border:none; font-size:12px; cursor:pointer;">حذف</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="padding:20px; text-align:center; color:#8a7a6a;">هیچ همکاری ثبت نشده است.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

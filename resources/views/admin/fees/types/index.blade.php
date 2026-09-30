@extends('admin.sections.layout')
@section('title', 'عناوین خدمت')

@section('content')
<div class="page-title">
    <h2>📋 عناوین خدمات عوارض</h2>
    <a href="{{ route('admin.fees.types.create') }}" class="btn btn-primary">➕ عنوان جدید</a>
</div>

@if(session('success'))<div class="success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="error">{{ $errors->first() }}</div>@endif

<div class="card" style="padding:14px;">
    <form method="GET" style="display:flex;gap:10px;align-items:end;">
        <div style="flex:1;">
            <label style="display:block;font-size:12px;color:#546e7a;margin-bottom:6px;">فیلتر بر اساس دسته</label>
            <select name="category_id" style="width:100%;padding:10px;border:1px solid #cfd8dc;border-radius:8px;font-family:inherit;">
                <option value="">همه دسته‌ها</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->icon }} {{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary" style="padding:10px 24px;">فیلتر</button>
    </form>
</div>

@if($types->count() > 0)
<table>
    <thead>
        <tr>
            <th>دسته</th>
            <th>نام خدمت</th>
            <th>اسلاگ</th>
            <th>ترتیب</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @foreach($types as $t)
        <tr>
            <td>{{ $t->category->icon ?? '' }} {{ $t->category->name ?? '—' }}</td>
            <td><strong>{{ $t->name }}</strong></td>
            <td style="font-family:monospace;font-size:12px;direction:ltr;">{{ $t->slug ?? '—' }}</td>
            <td>{{ $t->sort_order }}</td>
            <td>
                @if($t->is_active)
                    <span class="badge" style="background:#c8e6c9;color:#2e7d32;">فعال</span>
                @else
                    <span class="badge" style="background:#ffcdd2;color:#c62828;">غیرفعال</span>
                @endif
            </td>
            <td style="white-space:nowrap;">
                <a href="{{ route('admin.fees.types.edit', $t->id) }}" class="btn btn-primary" style="padding:6px 12px;border-radius:6px;text-decoration:none;font-size:12px;">ویرایش</a>
                <form method="POST" action="{{ route('admin.fees.types.destroy', $t->id) }}" style="display:inline;" onsubmit="return confirm('حذف؟');">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger" style="background:#c62828;color:white;padding:6px 12px;border:none;border-radius:6px;cursor:pointer;font-family:inherit;font-size:12px;">حذف</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="empty-box"><span class="icon">📋</span> عنوانی یافت نشد.</div>
@endif
@endsection

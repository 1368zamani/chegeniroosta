@extends('admin.sections.layout')

@section('title', 'دسته‌بندی عوارض')

@section('content')
<div class="page-title">
    <h2>🗂 دسته‌بندی عوارض و بهای خدمات</h2>
    <a href="{{ route('admin.fees.categories.create') }}" class="btn btn-primary">➕ دسته جدید</a>
</div>

@if(session('success'))<div class="success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="error">{{ $errors->first() }}</div>@endif

@if($categories->count() > 0)
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>آیکون</th>
            <th>نام</th>
            <th>اسلاگ</th>
            <th>تعداد عناوین</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categories as $c)
        <tr>
            <td>{{ $c->sort_order }}</td>
            <td style="font-size:22px;">{{ $c->icon }}</td>
            <td><strong>{{ $c->name }}</strong></td>
            <td style="font-family:monospace;direction:ltr;">{{ $c->slug }}</td>
            <td>{{ $c->feeTypes()->count() }}</td>
            <td>
                @if($c->is_active)
                    <span class="badge" style="background:#c8e6c9;color:#2e7d32;">فعال</span>
                @else
                    <span class="badge" style="background:#ffcdd2;color:#c62828;">غیرفعال</span>
                @endif
            </td>
            <td style="white-space:nowrap;">
                <a href="{{ route('admin.fees.types.index', ['category_id' => $c->id]) }}" class="btn" style="background:#7b1fa2;color:white;padding:6px 12px;border-radius:6px;text-decoration:none;font-size:12px;">📋 عناوین</a>
                <a href="{{ route('admin.fees.categories.edit', $c->id) }}" class="btn btn-primary" style="padding:6px 12px;border-radius:6px;text-decoration:none;font-size:12px;">ویرایش</a>
                <form method="POST" action="{{ route('admin.fees.categories.destroy', $c->id) }}" style="display:inline;" onsubmit="return confirm('حذف دسته؟');">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger" style="background:#c62828;color:white;padding:6px 12px;border:none;border-radius:6px;cursor:pointer;font-family:inherit;font-size:12px;">حذف</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="empty-box"><span class="icon">🗂</span> هنوز دسته‌بندی ثبت نشده است.</div>
@endif
@endsection

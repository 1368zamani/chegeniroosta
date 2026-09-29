@extends('admin.village.layout')

@section('title', 'مسئولین شهرستان')
@section('content')

<div class="page-title">
    <h2>🏛 مسئولین شهرستان چگنی</h2>
    <a href="{{ route('admin.officials.create') }}" class="btn btn-primary">➕ افزودن مسئول</a>
</div>

@if(session('success'))
    <div class="success">{{ session('success') }}</div>
@endif

@if($officials->count() > 0)
<table>
    <thead>
        <tr>
            <th>عکس</th>
            <th>نام</th>
            <th>سمت</th>
            <th>نوع</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @foreach($officials as $o)
        <tr>
            <td>
                @if($o->photo)
                    <img src="{{ asset('storage/' . $o->photo) }}" style="width:50px;height:50px;border-radius:50%;object-fit:cover;">
                @else
                    <div style="width:50px;height:50px;border-radius:50%;background:#f0f3f7;display:inline-flex;align-items:center;justify-content:center;color:#90a4ae;">👤</div>
                @endif
            </td>
            <td><strong>{{ $o->name }}</strong></td>
            <td>{{ $o->position }}</td>
            <td>
                @if($o->type === 'governor')
                    <span class="badge" style="background:#c8e6c9;color:#2e7d32;">فرماندار</span>
                @else
                    <span class="badge" style="background:#dbeafe;color:#1e40af;">معاون</span>
                @endif
            </td>
            <td>
                @if($o->is_active)
                    <span class="badge badge-active">فعال</span>
                @else
                    <span class="badge badge-inactive">غیرفعال</span>
                @endif
            </td>
            <td style="white-space:nowrap;">
                <a href="{{ route('admin.officials.edit', $o->id) }}" class="btn btn-primary btn-sm">ویرایش</a>
                <form method="POST" action="{{ route('admin.officials.destroy', $o->id) }}" style="display:inline;" onsubmit="return confirm('حذف؟');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" style="background:#c62828;color:#fff;padding:6px 12px;border:none;border-radius:6px;cursor:pointer;font-family:inherit;font-size:12px;">حذف</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="empty-box">
    <span class="icon">🏛</span>
    هنوز مسئولی ثبت نشده است.
    <div style="margin-top:16px;">
        <a href="{{ route('admin.officials.create') }}" class="btn btn-primary">➕ افزودن</a>
    </div>
</div>
@endif

@endsection

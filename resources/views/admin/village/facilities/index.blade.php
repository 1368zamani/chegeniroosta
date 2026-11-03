@extends('admin.village.layout')

@section('title', 'مدیریت امکانات')
@section('content')

<div class="page-title">
    <h2>🏛 امکانات {{ $village->name }}</h2>
    <a href="{{ route('admin.village.facilities.create', $village->id) }}" class="btn btn-primary">➕ امکانات جدید</a>
</div>

@if($facilities->count() > 0)
<table>
    <thead>
        <tr>
            <th>آیکون</th>
            <th>نام</th>
            <th>دسته</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @foreach($facilities as $f)
        <tr>
            <td style="font-size:22px;">{{ $f->icon ?? '🏛' }}</td>
            <td><strong>{{ $f->name }}</strong></td>
            <td>{{ $f->category ?? '—' }}</td>
            <td>
                @if($f->is_available)
                    <span class="badge badge-active">دارد</span>
                @else
                    <span class="badge badge-inactive">ندارد</span>
                @endif
            </td>
            <td>
                <a href="{{ route('admin.village.facilities.edit', [$village->id, $f->id]) }}" class="btn btn-primary btn-sm">ویرایش</a>
                @include('partials.admin.toggle', ['type' => 'facility', 'model' => $f])
                <form method="POST" action="{{ route('admin.village.facilities.destroy', [$village->id, $f->id]) }}" style="display:inline;" onsubmit="return confirm('حذف؟');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div style="margin-top:20px;">{{ $facilities->links() }}</div>
@else
<div class="empty-box">
    <span class="icon">🏛</span>
    هنوز امکاناتی ثبت نشده است.
    <div style="margin-top:16px;">
        <a href="{{ route('admin.village.facilities.create', $village->id) }}" class="btn btn-primary">➕ افزودن</a>
    </div>
</div>
@endif

@endsection

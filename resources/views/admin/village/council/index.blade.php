@extends('admin.village.layout')

@section('title', 'مدیریت شورا')
@section('content')

<div class="page-title">
    <h2>👥 شورای اسلامی {{ $village->name }}</h2>
    <a href="{{ route('admin.village.council.create', $village->id) }}" class="btn btn-primary">➕ عضو جدید</a>
</div>

@if($members->count() > 0)
<table>
    <thead>
        <tr>
            <th>عکس</th>
            <th>نام</th>
            <th>سمت</th>
            <th>تلفن</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @foreach($members as $m)
        <tr>
            <td>
                @if($m->photo)
                    <img src="{{ asset('storage/' . $m->photo) }}" class="thumb-sm">
                @else
                    <div class="thumb-empty">👤</div>
                @endif
            </td>
            <td><strong>{{ $m->name }}</strong></td>
            <td>{{ $m->position }}</td>
            <td style="direction:ltr;">{{ $m->phone ?? '—' }}</td>
            <td>
                @if($m->is_active)
                    <span class="badge badge-active">فعال</span>
                @else
                    <span class="badge badge-inactive">غیرفعال</span>
                @endif
            </td>
            <td>
                <a href="{{ route('admin.village.council.edit', [$village->id, $m->id]) }}" class="btn btn-primary btn-sm">ویرایش</a>
                @include('partials.admin.toggle', ['type' => 'village-council', 'model' => $m])
                <form method="POST" action="{{ route('admin.village.council.destroy', [$village->id, $m->id]) }}" style="display:inline;" onsubmit="return confirm('حذف؟');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="empty-box">
    <span class="icon">👥</span>
    هنوز عضو شورایی ثبت نشده است.
    <div style="margin-top:16px;">
        <a href="{{ route('admin.village.council.create', $village->id) }}" class="btn btn-primary">➕ افزودن</a>
    </div>
</div>
@endif

@endsection

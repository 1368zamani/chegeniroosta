@extends('admin.village.layout')

@section('title', 'مدیریت جاذبه‌ها')
@section('content')

<div class="page-title">
    <h2>🏞 جاذبه‌های {{ $village->name }}</h2>
    <a href="{{ route('admin.village.attractions.create', $village->id) }}" class="btn btn-primary">➕ جاذبه جدید</a>
</div>

@if($attractions->count() > 0)
<table>
    <thead>
        <tr>
            <th>تصویر</th>
            <th>نام</th>
            <th>دسته</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attractions as $a)
        <tr>
            <td>
                @if($a->image)
                    <img src="{{ asset('storage/' . $a->image) }}" class="thumb-sm">
                @else
                    <div class="thumb-empty">🏞</div>
                @endif
            </td>
            <td><strong>{{ $a->name }}</strong></td>
            <td>{{ $a->category ?? '—' }}</td>
            <td>
                <a href="{{ route('admin.village.attractions.edit', [$village->id, $a->id]) }}" class="btn btn-primary btn-sm">ویرایش</a>
                @include('partials.admin.toggle', ['type' => 'attraction', 'model' => $a])
                <form method="POST" action="{{ route('admin.village.attractions.destroy', [$village->id, $a->id]) }}" style="display:inline;" onsubmit="return confirm('حذف؟');">
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
    <span class="icon">🏞</span>
    هنوز جاذبه‌ای ثبت نشده است.
    <div style="margin-top:16px;">
        <a href="{{ route('admin.village.attractions.create', $village->id) }}" class="btn btn-primary">➕ افزودن</a>
    </div>
</div>
@endif

@endsection

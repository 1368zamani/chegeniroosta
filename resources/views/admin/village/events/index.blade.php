@extends('admin.village.layout')

@section('title', 'مدیریت رویدادها')
@section('content')

<div class="page-title">
    <h2>📅 رویدادهای {{ $village->name }}</h2>
    <a href="{{ route('admin.village.events.create', $village->id) }}" class="btn btn-primary">➕ رویداد جدید</a>
</div>

@if($events->count() > 0)
<table>
    <thead>
        <tr>
            <th>عنوان</th>
            <th>تاریخ</th>
            <th>محل</th>
            <th>نوع</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @foreach($events as $event)
        <tr>
            <td>
                <strong>{{ $event->title }}</strong>
                @if($event->is_important)
                    <span class="badge badge-warning">⭐ مهم</span>
                @endif
            </td>
            <td>{{ jalali($event->event_date) }}</td>
            <td>{{ $event->location ?? '—' }}</td>
            <td>{{ $event->event_type ?? '—' }}</td>
            <td>
                <a href="{{ route('admin.village.events.edit', [$village->id, $event->id]) }}" class="btn btn-primary btn-sm">ویرایش</a>
                @include('partials.admin.toggle', ['type' => 'event', 'model' => $event])
                <form method="POST" action="{{ route('admin.village.events.destroy', [$village->id, $event->id]) }}" style="display:inline;" onsubmit="return confirm('حذف؟');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div style="margin-top:20px;">{{ $events->links() }}</div>
@else
<div class="empty-box">
    <span class="icon">📅</span>
    هنوز رویدادی ثبت نشده است.
    <div style="margin-top:16px;">
        <a href="{{ route('admin.village.events.create', $village->id) }}" class="btn btn-primary">➕ افزودن رویداد</a>
    </div>
</div>
@endif

@endsection

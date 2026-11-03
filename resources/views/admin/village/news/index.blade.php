@extends('admin.village.layout')

@section('title', 'مدیریت اخبار')
@section('content')

<div class="page-title">
    <h2>📰 اخبار {{ $village->name }}</h2>
    <a href="{{ route('admin.village.news.create', $village->id) }}" class="btn btn-primary">➕ خبر جدید</a>
</div>

@if($news->count() > 0)
<table>
    <thead>
        <tr>
            <th>تصویر</th>
            <th>عنوان</th>
            <th>وضعیت</th>
            <th>تاریخ</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @foreach($news as $item)
        <tr>
            <td>
                @if($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}" class="thumb-sm">
                @else
                    <div class="thumb-empty">📰</div>
                @endif
            </td>
            <td>
                <strong>{{ $item->title }}</strong>
                @if($item->is_pinned)
                    <span class="badge badge-warning" style="margin-right:6px;">📌 سنجاق</span>
                @endif
            </td>
            <td>
                @if($item->is_published)
                    <span class="badge badge-active">منتشر شده</span>
                @else
                    <span class="badge badge-inactive">پیش‌نویس</span>
                @endif
            </td>
            <td style="font-size:12px; color:#78909c;">{{ jalali($item->created_at) }}</td>
            <td>
                <a href="{{ route('admin.village.news.edit', [$village->id, $item->id]) }}" class="btn btn-primary btn-sm">ویرایش</a>
                @include('partials.admin.toggle', ['type' => 'village-news', 'model' => $item])
                <form method="POST" action="{{ route('admin.village.news.destroy', [$village->id, $item->id]) }}" style="display:inline;" onsubmit="return confirm('حذف این خبر؟');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div style="margin-top:20px;">{{ $news->links() }}</div>
@else
<div class="empty-box">
    <span class="icon">📭</span>
    هنوز خبری ثبت نشده است.
    <div style="margin-top:16px;">
        <a href="{{ route('admin.village.news.create', $village->id) }}" class="btn btn-primary">➕ افزودن اولین خبر</a>
    </div>
</div>
@endif

@endsection

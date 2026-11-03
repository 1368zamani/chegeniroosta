@extends('admin.section-news.layout')

@section('title', 'مدیریت اخبار بخش‌ها')
@section('header', 'مدیریت اخبار بخش‌ها')

@section('content')

@if(auth()->user()->isSuperAdmin() && $sections->count() > 1)
    <div class="card" style="padding:18px 24px;">
        <form method="GET" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
            <label style="font-size:13px; color:#546e7a;">فیلتر بخش:</label>
            <select name="section_id" onchange="this.form.submit()" style="padding:8px 14px; border:1px solid #cfd8dc; border-radius:8px; font-family:inherit;">
                <option value="">همه بخش‌ها</option>
                @foreach($sections as $s)
                    <option value="{{ $s->id }}" {{ $sectionId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </form>
    </div>
@endif

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <p style="color:#546e7a; margin:0;">{{ $news->total() }} خبر</p>
    <a href="{{ route('admin.section-news.create') }}" class="btn btn-primary">➕ خبر جدید</a>
</div>

@if($news->count() > 0)
    <table>
        <thead>
            <tr>
                <th>تصویر</th>
                <th>عنوان</th>
                <th>بخش</th>
                <th>وضعیت</th>
                <th>تاریخ</th>
                <th title="تعداد بازدید">بازدید</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($news as $item)
                <tr>
                    <td>
                        @if($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}" class="thumb">
                        @else
                            <div class="thumb-empty">📰</div>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $item->title }}</strong>
                        @if($item->is_pinned)
                            <span class="badge badge-pinned" style="margin-right:6px;">📌 سنجاق</span>
                        @endif
                        @if($item->excerpt)
                            <div style="color:#90a4ae; font-size:12px; margin-top:4px;">
                                {{ \Illuminate\Support\Str::limit($item->excerpt, 60) }}
                            </div>
                        @endif
                    </td>
                    <td>{{ $item->section->name ?? '—' }}</td>
                    <td>
                        @if($item->is_published)
                            <span class="badge badge-published">منتشر شده</span>
                        @else
                            <span class="badge badge-draft">پیش‌نویس</span>
                        @endif
                    </td>
                    <td style="font-size:12px; color:#78909c;">
                        {{ jalali($item->created_at) }}
                    </td>
                    <td style="font-size:13px; color:#2e7d32; font-weight:600; white-space:nowrap;">
                        👁 {{ \App\Support\Number::fa(number_format($item->stat_views)) }}
                    </td>
                    <td>
                        <a href="{{ route('admin.section-news.edit', $item->id) }}" class="btn btn-primary btn-sm">ویرایش</a>
                        @include('partials.admin.toggle', ['type' => 'section-news', 'model' => $item])
                        <form method="POST" action="{{ route('admin.section-news.destroy', $item->id) }}" style="display:inline;" onsubmit="return confirm('حذف این خبر؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:20px;">
        {{ $news->withQueryString()->links() }}
    </div>
@else
    <div class="card">
        <div class="empty">
            <div class="icon">📭</div>
            <p>هنوز خبری منتشر نشده است.</p>
            <a href="{{ route('admin.section-news.create') }}" class="btn btn-primary" style="margin-top:16px;">➕ انتشار اولین خبر</a>
        </div>
    </div>
@endif

@endsection

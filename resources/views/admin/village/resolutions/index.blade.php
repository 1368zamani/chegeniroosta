@extends('admin.village.layout')

@section('title', 'مصوبات شورا')
@section('content')

<div class="page-title">
    <h2>📋 مصوبات شورای اسلامی {{ $village->name }}</h2>
    <a href="{{ route('admin.village.resolutions.create', $village->id) }}" class="btn btn-primary">➕ مصوبه جدید</a>
</div>

@if(session('success'))
    <div class="success">{{ session('success') }}</div>
@endif

{{-- آمار --}}
<div class="stats-grid">
    <div class="stat-card">
        <span class="icon">📋</span>
        <span class="num">{{ $stats['total'] }}</span>
        <span class="label">کل مصوبات</span>
    </div>
    <div class="stat-card">
        <span class="icon">🟦</span>
        <span class="num">{{ $stats['approved'] }}</span>
        <span class="label">تصویب شده</span>
    </div>
    <div class="stat-card">
        <span class="icon">🟨</span>
        <span class="num">{{ $stats['in_progress'] }}</span>
        <span class="label">در حال اجرا</span>
    </div>
    <div class="stat-card">
        <span class="icon">🟩</span>
        <span class="num">{{ $stats['completed'] }}</span>
        <span class="label">اجرا شده</span>
    </div>
</div>

{{-- فیلتر --}}
<div class="card">
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
        <div style="flex:1;min-width:200px;">
            <label style="font-size:13px;color:#64748b;margin-bottom:6px;display:block;">جستجو</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="عنوان، شماره جلسه..." style="width:100%;padding:10px;border:1px solid #cfd8dc;border-radius:8px;font-family:inherit;">
        </div>
        <div style="min-width:160px;">
            <label style="font-size:13px;color:#64748b;margin-bottom:6px;display:block;">وضعیت</label>
            <select name="status" style="width:100%;padding:10px;border:1px solid #cfd8dc;border-radius:8px;font-family:inherit;">
                <option value="">— همه —</option>
                <option value="approved"    @selected(request('status')=='approved')>تصویب شده</option>
                <option value="in_progress" @selected(request('status')=='in_progress')>در حال اجرا</option>
                <option value="completed"   @selected(request('status')=='completed')>اجرا شده</option>
                <option value="suspended"   @selected(request('status')=='suspended')>متوقف شده</option>
                <option value="rejected"    @selected(request('status')=='rejected')>رد شده</option>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary">فیلتر</button>
        </div>
    </form>
</div>

@if($resolutions->count() > 0)
    <table>
        <thead>
            <tr>
                <th>جلسه</th>
                <th>تاریخ جلسه</th>
                <th>عنوان</th>
                <th>وضعیت</th>
                <th>پیشرفت</th>
                <th>عمومی</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resolutions as $res)
                <tr>
                    <td>{{ $res->session_number ?? '—' }}</td>
                    <td style="font-size:12px;color:#64748b;">{{ $res->session_date ? $res->session_date->format('Y/m/d') : '—' }}</td>
                    <td><strong>{{ $res->title }}</strong></td>
                    <td>
                        <span class="badge" style="background:{{ $res->status_bg }};color:{{ $res->status_color }};">
                            {{ $res->status_label }}
                        </span>
                    </td>
                    <td>
                        <div style="background:#e2e8f0;border-radius:10px;height:8px;width:80px;overflow:hidden;display:inline-block;vertical-align:middle;">
                            <div style="background:{{ $res->status_color }};height:100%;width:{{ $res->progress_percent }}%;"></div>
                        </div>
                        <span style="font-size:11px;color:#64748b;">{{ $res->progress_percent }}%</span>
                    </td>
                    <td>
                        @if($res->is_public)
                            <span class="badge badge-active">نمایش</span>
                        @else
                            <span class="badge badge-inactive">مخفی</span>
                        @endif
                    </td>
                    <td style="white-space:nowrap;">
                        <a href="{{ route('admin.village.resolutions.edit', [$village->id, $res->id]) }}" class="btn btn-primary btn-sm">ویرایش</a>
                        <form method="POST" action="{{ route('admin.village.resolutions.destroy', [$village->id, $res->id]) }}" style="display:inline;" onsubmit="return confirm('حذف این مصوبه؟');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:16px;display:flex;justify-content:center;">
        {{ $resolutions->links() }}
    </div>
@else
    <div class="empty-box">
        <span class="icon">📋</span>
        هنوز مصوبه‌ای ثبت نشده است.
        <div style="margin-top:16px;">
            <a href="{{ route('admin.village.resolutions.create', $village->id) }}" class="btn btn-primary">➕ افزودن مصوبه</a>
        </div>
    </div>
@endif

@endsection

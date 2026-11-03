@extends('admin.village.layout')

@section('title', 'مدیریت پروژه‌ها')
@section('content')

<div class="page-title">
    <h2>🏗 پروژه‌های {{ $village->name }}</h2>
    <a href="{{ route('admin.village.projects.create', $village->id) }}" class="btn btn-primary">➕ پروژه جدید</a>
</div>

@if($projects->count() > 0)
<table>
    <thead>
        <tr>
            <th>عنوان</th>
            <th>وضعیت</th>
            <th>پیشرفت</th>
            <th>پیمانکار</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @foreach($projects as $project)
        <tr>
            <td><strong>{{ $project->title }}</strong></td>
            <td>
                @php
                    $statusMap = [
                        'planned' => ['در برنامه', 'badge-warning'],
                        'in_progress' => ['در حال اجرا', 'badge-active'],
                        'done' => ['انجام شده', 'badge-active'],
                        'paused' => ['متوقف', 'badge-inactive'],
                    ];
                    $st = $statusMap[$project->status] ?? ['-', ''];
                @endphp
                <span class="badge {{ $st[1] }}">{{ $st[0] }}</span>
            </td>
            <td>
                <div style="background:#f0f3f7; border-radius:4px; height:8px; width:80px; display:inline-block; vertical-align:middle;">
                    <div style="background:#2e7d32; width:{{ $project->progress }}%; height:100%; border-radius:4px;"></div>
                </div>
                <span style="font-size:12px; margin-right:6px;">{{ $project->progress }}%</span>
            </td>
            <td style="font-size:12px; color:#78909c;">{{ $project->contractor ?? '—' }}</td>
            <td>
                <a href="{{ route('admin.village.projects.edit', [$village->id, $project->id]) }}" class="btn btn-primary btn-sm">ویرایش</a>
                @include('partials.admin.toggle', ['type' => 'project', 'model' => $project])
                <form method="POST" action="{{ route('admin.village.projects.destroy', [$village->id, $project->id]) }}" style="display:inline;" onsubmit="return confirm('حذف؟');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div style="margin-top:20px;">{{ $projects->links() }}</div>
@else
<div class="empty-box">
    <span class="icon">🏗</span>
    هنوز پروژه‌ای ثبت نشده است.
    <div style="margin-top:16px;">
        <a href="{{ route('admin.village.projects.create', $village->id) }}" class="btn btn-primary">➕ افزودن اولین پروژه</a>
    </div>
</div>
@endif

@endsection

@extends('admin.sections.layout')

@section('title', 'شورای بخش')
@section('content')

<div class="page-title">
    <h2>👥 شورای اسلامی بخش {{ $section->name }}</h2>
    <a href="{{ route('admin.sections.council.create', $section->id) }}" class="btn btn-primary">➕ عضو جدید</a>
</div>

@if(session('success'))
    <div class="success">{{ session('success') }}</div>
@endif

@if($members->count() > 0)
<table>
    <thead>
        <tr>
            <th>عکس</th>
            <th>نام</th>
            <th>سمت</th>
            <th>تلفن</th>
            <th>وضعیت</th>
            <th>ترتیب</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @foreach($members as $m)
        <tr>
            <td>
                @if($m->photo)
                    <img src="{{ asset('storage/' . $m->photo) }}" style="width:50px;height:50px;border-radius:50%;object-fit:cover;">
                @else
                    <div style="width:50px;height:50px;border-radius:50%;background:#f0f3f7;display:inline-flex;align-items:center;justify-content:center;color:#90a4ae;">👤</div>
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
            <td>{{ $m->sort_order }}</td>
            <td style="white-space:nowrap;">
                <a href="{{ route('admin.sections.council.edit', [$section->id, $m->id]) }}" class="btn btn-primary btn-sm">ویرایش</a>
                @include('partials.admin.toggle', ['type' => 'section-council', 'model' => $m])
                <form method="POST" action="{{ route('admin.sections.council.destroy', [$section->id, $m->id]) }}" style="display:inline;" onsubmit="return confirm('حذف این عضو؟');">
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
    <span class="icon">👥</span>
    هنوز عضو شورایی برای این بخش ثبت نشده است.
    <div style="margin-top:16px;">
        <a href="{{ route('admin.sections.council.create', $section->id) }}" class="btn btn-primary">➕ افزودن عضو</a>
    </div>
</div>
@endif

@endsection

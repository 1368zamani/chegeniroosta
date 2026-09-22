@extends('admin.users.layout')

@section('title', 'مدیریت کاربران')
@section('header', 'مدیریت کاربران')

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <p style="color:#546e7a; margin:0;">{{ $users->count() }} کاربر</p>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">➕ افزودن کاربر جدید</a>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>نام</th>
            <th>ایمیل</th>
            <th>نقش</th>
            <th>بخش / روستا</th>
            <th>عملیات</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td style="direction:ltr; text-align:left;">{{ $user->email }}</td>
                <td>
                    @if($user->isSuperAdmin())
                        <span class="badge badge-super">سوپر ادمین</span>
                    @elseif($user->isSectionAdmin())
                        <span class="badge badge-section">ادمین بخش</span>
                    @elseif($user->isVillageAdmin())
                        <span class="badge badge-village">ادمین روستا</span>
                    @else
                        <span class="badge">{{ $user->role }}</span>
                    @endif
                </td>
                <td>
                    @if($user->section)
                        بخش: {{ $user->section->name }}
                    @endif
                    @if($user->village)
                        روستا: {{ $user->village->name }}
                    @endif
                    @if(!$user->section && !$user->village)
                        —
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-sm">ویرایش</a>
                    @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" style="display:inline;" onsubmit="return confirm('حذف این کاربر؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@endsection

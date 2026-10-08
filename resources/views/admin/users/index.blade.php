@extends('admin.users.layout')

@section('content')

<div class="page-title">
    <h2>👥 کاربران سامانه</h2>
</div>


<div style="margin-bottom:20px;">
    <a href="{{ route('admin.users.create') }}"
       class="btn btn-primary">
        ➕ افزودن کاربر
    </a>
</div>


<div class="card">

<table>
<thead>
<tr>
<th>نام</th>
<th>ایمیل</th>
<th>نقش</th>
<th>بخش</th>
<th>روستا</th>
<th>عملیات</th>
</tr>
</thead>

<tbody>

@foreach($users as $user)

<tr>

<td>{{ $user->name }}</td>

<td>{{ $user->email }}</td>

<td>
{{ $user->role_label ?? '-' }}
</td>

<td>
{{ $user->section->name ?? '-' }}
</td>

<td>
{{ $user->village->name ?? '-' }}
</td>


<td>

<a href="{{ route('admin.users.edit',$user->id) }}"
class="btn btn-primary btn-sm">
✏️ ویرایش
</a>


<form method="POST"
action="{{ route('admin.users.destroy',$user->id) }}"
style="display:inline">

@csrf
@method('DELETE')

<button type="submit"
class="btn btn-danger btn-sm">
🗑 حذف
</button>

</form>

</td>


</tr>

@endforeach

</tbody>

</table>

</div>


@endsection

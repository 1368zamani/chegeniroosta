<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>مدیریت رمز کاربران</title>

<style>
body{
    font-family:tahoma,Arial;
    background:#fff;
    color:#263238;
    margin:0;
}

.header{
    background:#1565C0;
    color:white;
    padding:20px;
}

.container{
    padding:30px;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

th{
    background:#1565C0;
    color:white;
    padding:12px;
}

td{
    padding:12px;
    border-bottom:1px solid #ddd;
}

input{
    padding:8px;
    border:1px solid #ddd;
    border-radius:5px;
}

button{
    background:#D32F2F;
    color:white;
    border:0;
    padding:8px 15px;
    border-radius:6px;
}

a{
    color:white;
    text-decoration:none;
}
</style>

</head>

<body>

<div class="header">
<h2>🔑 مدیریت رمز کاربران</h2>
<a href="{{ route('admin.dashboard') }}">بازگشت به داشبورد</a>
</div>


<div class="container">

@if(session('success'))
<div style="background:#e8f5e9;padding:15px">
{{ session('success') }}
</div>
@endif


<table>

<tr>
<th>نام</th>
<th>نام کاربری</th>
<th>نقش</th>
<th>رمز جدید</th>
<th>عملیات</th>
</tr>


@foreach($users as $user)

<tr>

<td>
{{ $user->name }}
</td>

<td>
{{ $user->username }}
</td>

<td>
@if($user->role === 'fee_officer')

    @if($user->officer_type === 'financial')
        👨‍💼 کارشناس مالی

    @elseif($user->officer_type === 'technical')
        🏗 کارشناس فنی و عمرانی

    @else
        کارشناس

    @endif

@elseif($user->role === 'section_admin')

    مدیر بخش

@elseif($user->role === 'village_admin')

    مدیر روستا

@elseif($user->role === 'super_admin')

    سوپر ادمین

@else

    {{ $user->role }}

@endif
</td>


<td>

<form method="POST" action="{{ route('admin.passwords.reset',$user->id) }}">

@csrf

<input type="password" name="password" placeholder="رمز جدید">


</td>

<td>

<button>
تغییر رمز
</button>

</form>

</td>


</tr>

@endforeach


</table>

</div>

</body>
</html>

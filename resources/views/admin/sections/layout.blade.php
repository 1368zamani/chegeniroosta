<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>@yield('title') - پنل بخشداری</title>

<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-light-theme.css') }}?v=2">

<style>

*{
box-sizing:border-box;
}

body{
margin:0;
font-family:Tahoma,Arial,sans-serif;
background:#fff7e9;
color:#2b2119;
}


.admin-wrapper{
display:flex;
min-height:100vh;
}


.sidebar{
width:260px;
background:#ffffff;
border-left:1px solid #e3e8ef;
padding:20px;
}


.sidebar-menu{
list-style:none;
padding:0;
margin:0;
}


.sidebar-menu li{
margin-bottom:8px;
}


.sidebar-menu a{
display:block;
padding:12px 15px;
border-radius:10px;
text-decoration:none;
color:#2b2119;
font-size:14px;
}


.sidebar-menu a:hover,
.sidebar-menu a.active{
background:#fdeee7;
color:#c94a2b;
font-weight:bold;
}


.menu-label{
color:#8a7a6a;
font-size:12px;
margin:20px 10px 10px;
}


.menu-divider{
height:1px;
background:#f5efe4;
margin:15px 0;
}


.content{
flex:1;
padding:30px;
}



.page-title{
background:white;
padding:20px;
border-radius:14px;
margin-bottom:25px;
display:flex;
justify-content:space-between;
align-items:center;
}


.stats-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
gap:18px;
}


.stat-card{
background:white;
padding:22px;
border-radius:14px;
text-decoration:none;
color:#2b2119;
box-shadow:0 5px 15px rgba(0,0,0,.05);
}


.stat-card:hover{
transform:translateY(-3px);
}


.stat-card .num{
display:block;
font-size:32px;
font-weight:bold;
color:#c94a2b;
}


.stat-card .label{
display:block;
margin-top:8px;
color:#8a7a6a;
}


.card{
background:white;
padding:22px;
border-radius:14px;
margin-top:25px;
}


table{
width:100%;
border-collapse:collapse;
background:white;
}


th{
background:#fdeee7;
padding:12px;
}


td{
padding:12px;
border-bottom:1px solid #f5efe4;
}


.btn{
background:#c94a2b;
color:white;
padding:8px 15px;
border-radius:8px;
text-decoration:none;
font-size:13px;
}


@media(max-width:800px){

.admin-wrapper{
display:block;
}

.sidebar{
width:100%;
}

.content{
padding:15px;
}

}

</style>

<link rel="stylesheet" href="{{ asset('css/admin-final-fix.css') }}?v=10">
<link rel="stylesheet" href="/css/admin-header-fix.css?v=1"><link rel="stylesheet" href="{{ asset('css/mobile-admin.css') }}?v=1">
<link rel="stylesheet" href="{{ asset('css/admin-utils.css') }}?v=1">
<link rel="stylesheet" href="{{ asset('css/admin-v3.css') }}?v=1">
</head>

<body>


<div class="admin-wrapper">


<aside class="sidebar">

<ul class="sidebar-menu">

<li class="menu-label">
منوی اصلی
</li>


@if(isset($section))
<li>
<a href="{{ url('/admin/sections/'.$section->id.'/dashboard') }}">
📊 داشبورد بخشداری
</a>
</li>


<li>
<a href="{{ url('/admin/sections/'.$section->id.'/villages') }}">
🏘 روستاهای بخش
</a>

<li>
<a href="{{ url('/admin/sections/'.$section->id.'/council') }}">
👥 شورای بخش
</a>
</li>
</li>


<li>
<a href="{{ url('/admin/sections/'.$section->id.'/requests') }}">
📩 درخواست‌ها
</a>
</li>
@else
<li>
<a href="{{ url('/admin') }}">
🏠 داشبورد سامانه
</a>
</li>
@endif


<li class="menu-divider"></li>


<li class="menu-label">
مدیریت
</li>


<li>
<a href="{{ url('/admin/section-news') }}">
📰 اخبار
</a>
</li>


@if(isset($section))
<li>
<a href="{{ url('/admin/sections/'.$section->id.'/edit') }}">
⚙️ تنظیمات بخش
</a>
</li>
@endif


<li>
<a href="{{ route('profile.password.edit') }}">
🔑 تغییر رمز
</a>
</li>

<li>
<a href="/logout">
🚪 خروج
</a>
</li>


</ul>


</aside>



<main class="content">


@if(session('success'))
<div style="background:#fbdcd0;padding:15px;border-radius:10px;margin-bottom:20px">
{{session('success')}}
</div>
@endif


@yield('content')


</main>


</div>


</body>
</html>

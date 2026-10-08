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
background:#f5f7fa;
color:#263238;
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
color:#37474f;
font-size:14px;
}


.sidebar-menu a:hover,
.sidebar-menu a.active{
background:#e3f2fd;
color:#1565c0;
font-weight:bold;
}


.menu-label{
color:#78909c;
font-size:12px;
margin:20px 10px 10px;
}


.menu-divider{
height:1px;
background:#eceff1;
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
color:#263238;
box-shadow:0 5px 15px rgba(0,0,0,.05);
}


.stat-card:hover{
transform:translateY(-3px);
}


.stat-card .num{
display:block;
font-size:32px;
font-weight:bold;
color:#1565c0;
}


.stat-card .label{
display:block;
margin-top:8px;
color:#607d8b;
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
background:#e3f2fd;
padding:12px;
}


td{
padding:12px;
border-bottom:1px solid #eee;
}


.btn{
background:#1565c0;
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
<link rel="stylesheet" href="/css/admin-header-fix.css?v=1"></head>

<body>


<div class="admin-wrapper">


<aside class="sidebar">

<ul class="sidebar-menu">

<li class="menu-label">
منوی اصلی
</li>


<li>
<a href="{{ url('/admin/sections/'.$section->id.'/dashboard') }}">
📊 داشبورد بخشداری
</a>
</li>


<li>
<a href="{{ url('/admin/sections/'.$section->id.'/villages') }}">
🏘 روستاهای بخش
</a>
</li>


<li>
<a href="{{ url('/admin/sections/'.$section->id.'/requests') }}">
📩 درخواست‌ها
</a>
</li>


<li class="menu-divider"></li>


<li class="menu-label">
مدیریت
</li>


<li>
<a href="{{ url('/admin/section-news') }}">
📰 اخبار
</a>
</li>


<li>
<a href="{{ url('/admin/sections/'.$section->id.'/edit') }}">
⚙️ تنظیمات بخش
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
<div style="background:#c8e6c9;padding:15px;border-radius:10px;margin-bottom:20px">
{{session('success')}}
</div>
@endif


@yield('content')


</main>


</div>


</body>
</html>

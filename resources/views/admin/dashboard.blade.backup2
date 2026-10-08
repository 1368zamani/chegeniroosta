<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>پنل مدیریت سامانه بخشداری</title>


<style>

*{
    box-sizing:border-box;
}

body{
    margin:0;
    font-family:tahoma,Arial;
    background:#fff;
    color:#263238;
}


/* هدر */
.header{
    background:#1565C0;
    color:white;
    padding:20px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:15px;
}

.header h1{
    margin:0;
    color:white;
    font-size:22px;
}

.user-info{
    color:white;
    margin-top:8px;
}


/* منوی بالا */
.nav{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.nav a{
    background:#1976D2;
    color:white !important;
    text-decoration:none;
    padding:10px 18px;
    border-radius:8px;
    font-size:14px;
    border:1px solid rgba(255,255,255,.3);
}

.nav a:hover{
    background:#0D47A1;
}


/* محتوا */

.container{
    max-width:1200px;
    margin:auto;
    padding:30px 20px;
}


/* آمار */

.stats-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(150px,1fr));
    gap:15px;
}


.stat-card{
    background:white;
    border:1px solid #E3F2FD;
    border-radius:14px;
    padding:25px;
    text-align:center;
    box-shadow:0 5px 15px rgba(0,0,0,.05);
}


.stat-card .num{
    display:block;
    font-size:32px;
    font-weight:bold;
    color:#1565C0;
}


.stat-card .label{
    margin-top:10px;
    color:#1565C0;
}



/* عنوان */

.section-title{
    margin:35px 0 20px;
    color:#1565C0;
    font-size:20px;
    border-bottom:2px solid #E3F2FD;
    padding-bottom:10px;
}


/* کارت بخش ها */

.sections-grid{

    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
    gap:20px;

}


.section-card{

    background:#fff;
    border-radius:15px;
    border-top:5px solid #1565C0;
    padding:25px;
    text-decoration:none;
    box-shadow:0 5px 20px rgba(0,0,0,.08);

}


.section-card h3{

    color:#1565C0;
    font-size:20px;

}


.section-card .count{

    color:#546E7A;

}


.section-card .enter{

    display:inline-block;
    margin-top:15px;
    background:#1565C0;
    color:white;
    padding:8px 18px;
    border-radius:8px;

}


.section-card:hover{

    transform:translateY(-3px);

}


</style>

<link rel="stylesheet" href="/css/admin-master.css?v=2">
<link rel="stylesheet" href="{{ asset('css/admin-final-fix.css') }}?v=10">
<link rel="stylesheet" href="/css/admin-header-fix.css?v=1"></head>


<body>


<header class="header">

<div>

<h1>
پنل مدیریت سامانه بخشداری
</h1>


<div class="user-info">

خوش آمدی، {{ auth()->user()->name }}

@if(auth()->user()->isSuperAdmin())
(سوپر ادمین)
@endif

</div>

</div>



<div class="nav">


<a href="{{ route('admin.settings.index') }}">
⚙️ تنظیمات
</a>


<a href="{{ route('admin.users.index') }}">
👥 کاربران
</a>


<a href="{{ route('admin.officials.index') }}">
🏛 مسئولین شهرستان
</a>


<a href="{{ route('admin.sections.index') }}">
📁 بخش‌ها
</a>


<a href="{{ url('/') }}">
🌐 سایت
</a>


<a href="{{ route('admin.passwords.index') }}" class="btn btn-primary" style="margin-left:10px;">🔑 مدیریت رمزها</a>

<form method="POST" action="{{ url('/logout') }}">
@csrf
<button style="background:#D32F2F;color:white;border:0;padding:10px 18px;border-radius:8px">
خروج
</button>
</form>


</div>


</header>



<div class="container">


<div class="stats-grid">


<div class="stat-card">
<span class="num">{{ $stats['villages'] ?? 0 }}</span>
<span class="label">🏠 روستا</span>
</div>


<div class="stat-card">
<span class="num">{{ $stats['users'] ?? 0 }}</span>
<span class="label">👥 کاربران</span>
</div>


<div class="stat-card">
<span class="num">{{ $stats['news'] ?? 0 }}</span>
<span class="label">📰 اخبار</span>
</div>


<div class="stat-card">
<span class="num">{{ $stats['sections'] ?? 0 }}</span>
<span class="label">📁 بخش‌ها</span>
</div>


</div>



<h2 class="section-title">
📁 بخش‌ها
</h2>



<div class="sections-grid">


@foreach($sections as $section)

<a class="section-card"
href="{{ route('admin.sections.dashboard',$section->id) }}">


<h3>
{{ $section->name }}
</h3>


<div class="count">
{{ $section->villages_count ?? $section->villages()->count() }} روستا
</div>


<span class="enter">
ورود به بخش
</span>


</a>


@endforeach


</div>


</div>


</body>
</html>

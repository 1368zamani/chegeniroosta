<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>پنل مدیریت {{ $section->name }}</title>

<link rel="stylesheet" href="{{ asset('css/persian-fonts.css') }}">

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


.header{
    background:#2e7d32;
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
    font-size:22px;
}


.user{
    margin-top:8px;
    font-size:14px;
}


.nav{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}


.nav a,
.nav button{

    background:rgba(255,255,255,.15);
    color:white;
    border:none;
    text-decoration:none;

    padding:9px 15px;
    border-radius:8px;

    font-family:inherit;
    cursor:pointer;

}



.container{

    max-width:1200px;
    margin:auto;
    padding:30px 20px;

}



.stats{

display:grid;
grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
gap:18px;

}



.stat{

background:white;
padding:25px;
border-radius:14px;

box-shadow:0 5px 20px rgba(0,0,0,.08);

text-align:center;

}



.stat .num{

display:block;
font-size:32px;
font-weight:bold;
color:#2e7d32;

}



.stat .label{

display:block;
margin-top:10px;
color:#607d8b;

}




.title{

margin:35px 0 20px;

color:#2e7d32;

font-size:20px;

border-bottom:2px solid #e8f5e9;

padding-bottom:10px;

}



.cards{

display:grid;

grid-template-columns:repeat(auto-fit,minmax(240px,1fr));

gap:20px;

}



.card{

background:white;

padding:25px;

border-radius:14px;

box-shadow:0 5px 20px rgba(0,0,0,.08);

text-decoration:none;

color:#263238;

border-top:4px solid #2e7d32;

}



.card:hover{

transform:translateY(-3px);

}



.card h3{

color:#2e7d32;

margin-top:0;

}



.card p{

color:#607d8b;

}




.logout{

background:#e53935!important;

}



@media(max-width:700px){

.header{

padding:15px;

}


.nav{

width:100%;

}


.nav a{

flex:1;

text-align:center;

}


}


</style>

<link rel="stylesheet" href="{{ asset('css/admin-final-fix.css') }}?v=10">
<link rel="stylesheet" href="/css/admin-header-fix.css?v=1"></head>


<body>


<div class="header">

<div>

<h1>
پنل مدیریت {{ $section->name }}
</h1>


<div class="user">

خوش آمدی،
{{ auth()->user()->name }}

</div>


</div>



<div class="nav">


<a href="{{ route('admin.sections.villages',$section->id) }}">
🏘 روستاها
</a>


<a href="{{ route('admin.section-news.index') }}">
📰 اخبار
</a>


<a href="{{ route('admin.sections.edit',$section->id) }}">
⚙️ تنظیمات
</a>


<a href="/">
🌐 سایت
</a>



<form method="POST" action="{{ route('logout') }}">
@csrf

<button class="logout">
خروج
</button>

</form>



</div>

</div>




<div class="container">


<div class="stats">


<div class="stat">

<span class="num">
{{ $section->villages()->count() }}
</span>

<span class="label">
🏘 تعداد روستاها
</span>

</div>



<div class="stat">

<span class="num">

{{ number_format($section->villages()->sum('population')) }}

</span>

<span class="label">
👥 جمعیت
</span>

</div>



<div class="stat">

<span class="num">

{{ number_format($section->villages()->sum('households')) }}

</span>

<span class="label">
🏠 خانوار
</span>

</div>


</div>




<h2 class="title">
مدیریت بخشداری
</h2>



<div class="cards">



<a class="card"
href="{{ route('admin.sections.villages',$section->id) }}">

<h3>
🏘 روستاهای بخش
</h3>

<p>
مشاهده و مدیریت اطلاعات روستاها
</p>

</a>



<a class="card"
href="{{ route('admin.section-news.index') }}">

<h3>
📰 اخبار بخشداری
</h3>

<p>
مدیریت اخبار و اطلاعیه‌ها
</p>

</a>



<a class="card"
href="{{ route('admin.sections.edit',$section->id) }}">

<h3>
⚙️ تنظیمات بخش
</h3>

<p>
ویرایش اطلاعات بخشداری
</p>

</a>



</div>


</div>



</body>

</html>

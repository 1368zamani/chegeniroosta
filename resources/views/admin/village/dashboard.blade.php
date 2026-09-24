@extends('admin.village.layout')

@section('title', 'داشبورد')

@section('content')

<div class="page-title">
    <h2>📊 داشبورد {{ $village->name }}</h2>
</div>

<div class="stats-grid">
    <a href="{{ route('admin.village.news.index', $village->id) }}" class="stat-card">
        <span class="icon">📰</span>
        <span class="num">{{ $stats['news'] }}</span>
        <span class="label">خبر</span>
    </a>
    <a href="{{ route('admin.village.projects.index', $village->id) }}" class="stat-card">
        <span class="icon">🏗</span>
        <span class="num">{{ $stats['projects'] }}</span>
        <span class="label">پروژه</span>
    </a>
    <a href="{{ route('admin.village.events.index', $village->id) }}" class="stat-card">
        <span class="icon">📅</span>
        <span class="num">{{ $stats['events'] }}</span>
        <span class="label">رویداد</span>
    </a>
    <a href="{{ route('admin.village.facilities.index', $village->id) }}" class="stat-card">
        <span class="icon">🏛</span>
        <span class="num">{{ $stats['facilities'] }}</span>
        <span class="label">امکانات</span>
    </a>
    <a href="{{ route('admin.village.council.index', $village->id) }}" class="stat-card">
        <span class="icon">👥</span>
        <span class="num">{{ $stats['council'] }}</span>
        <span class="label">عضو شورا</span>
    </a>
    <a href="{{ route('admin.village.attractions.index', $village->id) }}" class="stat-card">
        <span class="icon">🏞</span>
        <span class="num">{{ $stats['attractions'] }}</span>
        <span class="label">جاذبه</span>
    </a>
    <a href="{{ route('admin.village.images.index', $village->id) }}" class="stat-card">
        <span class="icon">🖼</span>
        <span class="num">{{ $stats['gallery'] }}</span>
        <span class="label">تصویر</span>
    </a>
    <a href="{{ route('admin.village.requests.index', $village->id) }}" class="stat-card">
        <span class="icon">📩</span>
        <span class="num">{{ $stats['requests'] }}</span>
        <span class="label">درخواست</span>
    </a>
</div>

<div class="card">
    <h3>⚙️ تنظیمات سریع</h3>
    <p style="color:#546e7a; font-size:13.5px; line-height:2;">
        از منوی سمت راست می‌توانید هر بخش از سایت روستا را مدیریت کنید. برای پنهان یا نمایش هر بخش، به «تنظیمات نمایش» بروید.
    </p>
    <a href="{{ route('admin.village.settings.index', $village->id) }}" class="btn btn-primary" style="margin-top:10px;">⚙️ برو به تنظیمات نمایش</a>
</div>

@endsection

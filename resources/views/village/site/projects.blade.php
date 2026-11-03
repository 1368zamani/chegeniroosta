@extends('village.site._layout')
@section('title', 'پروژه‌های ' . $village->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">پروژه‌های عمرانی روستای {{ $village->name }}</h1>

    @forelse($projects as $project)
    <div class="bg-white rounded-lg shadow p-6 mb-4">
        <div class="flex justify-between items-start mb-3">
            <h3 class="font-bold text-lg">{{ $project->title }}</h3>
            <span class="px-3 py-1 rounded-full text-xs
                @if($project->status === 'completed') bg-green-100 text-green-800
                @elseif($project->status === 'in_progress') bg-yellow-100 text-yellow-800
                @elseif($project->status === 'planned') bg-green-100 text-green-800
                @else bg-gray-100 text-gray-800 @endif">
                @switch($project->status)
                    @case('completed') تکمیل‌شده @break
                    @case('in_progress') در حال اجرا @break
                    @case('planned') برنامه‌ریزی‌شده @break
                    @default {{ $project->status ?? 'نامشخص' }}
                @endswitch
            </span>
        </div>
        @if($project->description)
        <p class="text-gray-700 leading-relaxed mb-3">{{ $project->description }}</p>
        @endif
        <div class="flex gap-4 text-sm text-gray-600">
            @if($project->budget ?? false)
            <span>💰 بودجه: {{ number_format($project->budget) }} ریال</span>
            @endif
            @if($project->progress ?? false)
            <span>📊 پیشرفت: {{ $project->progress }}%</span>
            @endif
            @if($project->contractor ?? false)
            <span>🏗 پیمانکار: {{ $project->contractor }}</span>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-green-50 border border-green-200 rounded p-4 text-green-800">
        پروژه‌ای برای این روستا ثبت نشده است.
    </div>
    @endforelse
</div>
@endsection

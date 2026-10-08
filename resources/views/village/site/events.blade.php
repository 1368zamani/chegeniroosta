@extends('village.site._layout')
@section('title', 'رویدادهای ' . $village->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">رویدادهای روستای {{ $village->name }}</h1>

    <h2 class="text-2xl font-semibold mb-4 text-green-700">رویدادهای پیش‌رو</h2>
    @forelse($upcoming as $event)
    <div class="bg-white rounded-lg shadow p-6 mb-4 border-r-4 border-green-500">
        <div class="flex justify-between items-start mb-2">
            <h3 class="font-bold text-lg">{{ $event->title }}</h3>
            <span class="text-sm text-gray-500" dir="ltr">
                {{ $event->event_date }}
            </span>
        </div>
        @if($event->description)
        <p class="text-gray-700 leading-relaxed">{{ $event->description }}</p>
        @endif
        @if($event->location ?? false)
        <p class="text-sm text-gray-500 mt-2">📍 {{ $event->location }}</p>
        @endif
    </div>
    @empty
    <div class="bg-blue-50 border border-blue-200 rounded p-4 text-blue-800 mb-8">
        رویداد پیش‌رویی ثبت نشده است.
    </div>
    @endforelse

    <h2 class="text-2xl font-semibold mb-4 mt-8 text-gray-600">رویدادهای گذشته</h2>
    @forelse($past as $event)
    <div class="bg-gray-50 rounded-lg shadow p-6 mb-4 border-r-4 border-gray-300">
        <div class="flex justify-between items-start mb-2">
            <h3 class="font-bold">{{ $event->title }}</h3>
            <span class="text-sm text-gray-500" dir="ltr">{{ $event->event_date }}</span>
        </div>
        @if($event->description)
        <p class="text-gray-700 text-sm leading-relaxed">{{ Str::limit($event->description, 200) }}</p>
        @endif
    </div>
    @empty
    <div class="bg-gray-50 border border-gray-200 rounded p-4 text-gray-600">
        رویداد گذشته‌ای ثبت نشده است.
    </div>
    @endforelse
</div>
@endsection

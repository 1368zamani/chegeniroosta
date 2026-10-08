@extends('village.site._layout')
@section('title', 'جاذبه‌های ' . $village->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">جاذبه‌های گردشگری {{ $village->name }}</h1>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($attractions as $attr)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if(!empty($attr->image))
            <img src="{{ asset('storage/' . $attr->image) }}"
                 alt="{{ $attr->title ?? '' }}"
                 class="w-full h-48 object-cover">
            @endif
            <div class="p-4">
                <h3 class="font-bold mb-2">{{ $attr->title ?? '-' }}</h3>
                @if(!empty($attr->description))
                <p class="text-sm text-gray-700 leading-relaxed">
                    {{ \Illuminate\Support\Str::limit($attr->description, 150) }}
                </p>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-3 bg-blue-50 border border-blue-200 rounded p-4 text-blue-800">
            جاذبه‌ای برای این روستا ثبت نشده است.
        </div>
        @endforelse
    </div>
</div>
@endsection

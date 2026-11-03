@extends('village.site._layout')
@section('title', 'امکانات ' . $village->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">امکانات و خدمات روستای {{ $village->name }}</h1>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($facilities as $facility)
        <div class="bg-white rounded-lg shadow p-5 border-r-4 border-green-500">
            <h3 class="font-bold mb-2">{{ $facility->name }}</h3>
            @if($facility->type)
            <p class="text-sm text-gray-600 mb-1">نوع: {{ $facility->type }}</p>
            @endif
            @if($facility->description)
            <p class="text-sm text-gray-700 leading-relaxed">{{ $facility->description }}</p>
            @endif
        </div>
        @empty
        <div class="col-span-3 bg-green-50 border border-green-200 rounded p-4 text-green-800">
            امکاناتی برای این روستا ثبت نشده است.
        </div>
        @endforelse
    </div>
</div>
@endsection

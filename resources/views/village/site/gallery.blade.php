@extends('village.site._layout')
@section('title', 'گالری تصاویر ' . $village->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">گالری تصاویر {{ $village->name }}</h1>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($images as $image)
        @php $path = $image->path ?? $image->image ?? ''; @endphp
        @if($path)
        <a href="{{ asset('storage/' . $path) }}"
           data-lightbox="village-gallery"
           class="block rounded-lg overflow-hidden shadow hover:shadow-lg transition">
            <img src="{{ asset('storage/' . $path) }}"
                 alt="{{ $image->caption ?? $village->name }}"
                 class="w-full h-40 object-cover hover:scale-105 transition duration-300"
                 loading="lazy">
        </a>
        @endif
        @empty
        <div class="col-span-4 bg-green-50 border border-green-200 rounded p-4 text-green-800">
            تصویری برای این روستا ثبت نشده است.
        </div>
        @endforelse
    </div>
</div>
@endsection

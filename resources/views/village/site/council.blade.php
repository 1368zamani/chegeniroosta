@extends('village.site._layout')
@section('title', 'شورای اسلامی ' . $village->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">اعضای شورای اسلامی روستای {{ $village->name }}</h1>

    @forelse($members as $member)
    <div class="bg-white rounded-lg shadow p-6 mb-4 flex items-center gap-4">
        @if($member->photo ?? false)
        <img src="{{ asset('storage/' . $member->photo) }}"
             alt="{{ $member->full_name ?? $member->name ?? 'عضو' }}"
             class="w-20 h-20 rounded-full object-cover">
        @else
        <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center">
            <svg class="w-10 h-10 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
            </svg>
        </div>
        @endif
        <div class="flex-1">
            <h3 class="font-bold text-lg">{{ $member->full_name ?? $member->name ?? '-' }}</h3>
            <p class="text-sm text-gray-600">{{ $member->position ?? $member->role ?? 'عضو شورا' }}</p>
            @if($member->phone ?? false)
            <p class="text-sm text-gray-500" dir="ltr">{{ $member->phone }}</p>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-yellow-50 border border-yellow-200 rounded p-4 text-yellow-800">
        اطلاعات شورای این روستا هنوز ثبت نشده است.
    </div>
    @endforelse
</div>
@endsection

@extends('village.site._layout')

@section('title', 'درباره ' . $village->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">درباره روستای {{ $village->name }}</h1>

    @if(!empty($village->description))
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <p class="leading-relaxed text-gray-700">{{ $village->description }}</p>
    </div>
    @endif

    <div class="grid md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">اطلاعات کلی</h2>
            <dl class="space-y-3">
                <div class="flex justify-between border-b pb-2">
                    <dt class="text-gray-600">بخش</dt>
                    <dd class="font-semibold">{{ optional($village->section)->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <dt class="text-gray-600">دهستان</dt>
                    <dd class="font-semibold">{{ $village->district ?? '-' }}</dd>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <dt class="text-gray-600">جمعیت</dt>
                    <dd class="font-semibold">{{ number_format($village->population ?? 0) }} نفر</dd>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <dt class="text-gray-600">تعداد خانوار</dt>
                    <dd class="font-semibold">{{ number_format($village->households ?? 0) }}</dd>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <dt class="text-gray-600">کد آماری</dt>
                    <dd class="font-semibold">{{ $village->code ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        @if(!empty($village->contact))
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">اطلاعات تماس</h2>
            <dl class="space-y-3">
                @if(!empty($village->contact->phone))
                <div class="flex justify-between border-b pb-2">
                    <dt class="text-gray-600">تلفن دهیاری</dt>
                    <dd class="font-semibold" dir="ltr">{{ $village->contact->phone }}</dd>
                </div>
                @endif
                @if(!empty($village->contact->address))
                <div class="border-b pb-2">
                    <dt class="text-gray-600 mb-1">آدرس</dt>
                    <dd class="text-sm">{{ $village->contact->address }}</dd>
                </div>
                @endif
            </dl>
        </div>
        @endif
    </div>

    
    @if(!empty($village->bank_account) || !empty($village->bank_iban))
    <div class="bg-white rounded-lg shadow p-6 mt-6 border-r-4 border-green-500">
        <h2 class="text-xl font-bold mb-4">💳 اطلاعات بانکی دهیاری</h2>
        <dl class="space-y-3">
            @if(!empty($village->bank_name))
            <div class="flex justify-between border-b pb-2">
                <dt class="text-gray-600">بانک</dt>
                <dd class="font-semibold">{{ $village->bank_name }}</dd>
            </div>
            @endif
            @if(!empty($village->bank_account))
            <div class="flex justify-between border-b pb-2">
                <dt class="text-gray-600">شماره حساب</dt>
                <dd class="font-semibold font-mono" dir="ltr">{{ $village->bank_account }}</dd>
            </div>
            @endif
            @if(!empty($village->bank_iban))
            <div class="flex justify-between border-b pb-2">
                <dt class="text-gray-600">شماره شبا</dt>
                <dd class="font-semibold font-mono text-xs" dir="ltr">{{ $village->bank_iban }}</dd>
            </div>
            @endif
        </dl>
        <p class="text-xs text-gray-500 mt-3">
            جهت واریز کمک‌های مردمی و مشارکت‌های عمومی
        </p>
    </div>
    @endif

    @if(isset($attractions) && $attractions->count() > 0)
    <section class="mb-8">
        <h2 class="text-2xl font-bold mb-4">جاذبه‌های گردشگری</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($attractions as $attr)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                @if(!empty($attr->image))
                <img src="{{ asset('storage/' . $attr->image) }}"
                     alt="{{ $attr->title ?? '' }}"
                     class="w-full h-40 object-cover">
                @endif
                <div class="p-4">
                    <h3 class="font-bold mb-2">{{ $attr->title ?? '-' }}</h3>
                    @if(!empty($attr->description))
                    <p class="text-sm text-gray-700 leading-relaxed">{{ \Illuminate\Support\Str::limit($attr->description, 120) }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    @if(isset($gallery) && $gallery->count() > 0)
    <section>
        <h2 class="text-2xl font-bold mb-4">گالری تصاویر</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach($gallery as $img)
            @php $path = $img->path ?? $img->image ?? ''; @endphp
            @if($path)
            <a href="{{ asset('storage/' . $path) }}"
               data-lightbox="village-gallery"
               class="block rounded-lg overflow-hidden shadow hover:shadow-lg transition">
                <img src="{{ asset('storage/' . $path) }}"
                     alt="{{ $img->caption ?? $village->name }}"
                     class="w-full h-32 object-cover hover:scale-105 transition duration-300"
                     loading="lazy">
            </a>
            @endif
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection

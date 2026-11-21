@extends(View::exists('layouts.admin') ? 'layouts.admin' : 'layouts.tariff-fallback')
@section('title', 'گزارش اطلاعات ناقص')
@section('content')
<div class="p-4 md:p-6" dir="rtl">
    @include('admin.levy._nav')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-5 no-print">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">پروندههای ناقص و تکمیلنشده</h1>
            <p class="text-sm text-slate-500 mt-1">فیلدهای ناقص در هر پرونده مشخص شدهاند تا کارشناس بتواند آنها را تکمیل کند.</p>
        </div>
        <button onclick="window.print()" class="px-4 py-2 rounded bg-slate-800 text-white text-sm hover:bg-slate-900">چاپ</button>
    </div>

    @if($files->isEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-8 text-center text-slate-500">همه پروندهها تکمیل شدهاند.</div>
    @else
        <div class="space-y-4">
            @foreach($files as $f)
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                        <div class="font-bold text-slate-800">
                            {{ $f->village?->name }}
                            <span class="text-xs font-normal text-slate-500 mr-2">سال {{ \App\Support\Number::fa($f->jalali_year) }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-slate-500">تکمیل: {{ \App\Support\Number::money($f->completion_percent) }}٪</span>
                            <a href="{{ route('admin.levy.files.show', $f) }}" class="text-xs text-blue-700 hover:underline no-print">تکمیل اطلاعات ←</a>
                        </div>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 mb-3">
                        <div class="h-2 bg-amber-500 rounded-full" style="width: {{ $f->completion_percent }}%"></div>
                    </div>
                    <div class="text-xs text-slate-600">
                        <span class="font-semibold">فیلدهای ناقص:</span>
                        @foreach($f->missingFields() as $m)
                            <span class="inline-block bg-amber-50 text-amber-800 border border-amber-200 rounded px-2 py-0.5 ml-1 mb-1">{{ $m }}</span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

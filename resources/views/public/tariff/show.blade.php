@extends(View::exists('layouts.public') ? 'layouts.public' : 'layouts.tariff-fallback')
@section('title', "تعرفه {$year} - {$village->name}")
@section('content')
<div class="p-4 md:p-6" dir="rtl">
    <header class="mb-6">
        <h1 class="text-2xl font-bold">تعرفه عوارض و بهای خدمات دهیاری روستای {{ $village->name }}</h1>
        <p class="text-gray-600 mt-1">سال {{ \App\Support\Number::fa($year) }}
            @if($set->announced_at)— اعلام‌شده در {{ \Morilog\Jalali\Jalalian::fromCarbon($set->announced_at)->format('Y/m/d') }}@endif
        </p>
    </header>
    <div class="bg-amber-50 border-r-4 border-amber-500 p-3 rounded mb-6 text-amber-900 text-sm">
        ⚠️ دریافت وجه نقد توسط دهیار یا کارکنان <strong>ممنوع</strong> است. وجوه باید به حساب بانکی دهیاری واریز شود.
    </div>
    @if($years->count() > 1)
    <div class="mb-6 flex gap-2 flex-wrap no-print">
        @foreach($years as $y)
            <a href="{{ route('tariff.public', ['slug' => $village->slug, 'year' => $y]) }}" class="px-3 py-1 rounded border {{ $y == $year ? 'bg-emerald-600 text-white' : 'bg-white text-gray-700' }}">{{ \App\Support\Number::fa($y) }}</a>
        @endforeach
    </div>
    @endif
    <input type="text" id="searchBox" placeholder="جست‌وجو..." class="w-full mb-4 p-2 border rounded no-print">
    @include('public.tariff._section', ['title' => 'عوارض محلی', 'items' => $grouped->get('levy', collect()), 'type' => 'levy'])
    @include('public.tariff._section', ['title' => 'بهای خدمات', 'items' => $grouped->get('service_fee', collect()), 'type' => 'service_fee'])
    <div class="mt-8 no-print"><button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded">نسخه‌ی چاپی</button></div>
</div>
<script>
document.getElementById('searchBox')?.addEventListener('input', e => {
    const q = e.target.value.trim();
    document.querySelectorAll('[data-tariff-row]').forEach(el => {
        el.style.display = !q || el.textContent.includes(q) ? '' : 'none';
    });
});
</script>
@endsection

@extends('village.site._layout')
@section('title', "تعرفه {$year} - {$village->name}")

@section('content')
<div x-data="tariffPage()" x-init="init()" dir="rtl" class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-6">

        {{-- ═══ هدر ═══ --}}
        <header class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-emerald-800 mb-2">
                        📋 تعرفه عوارض و بهای خدمات
                    </h1>
                    <p class="text-gray-600">
                        روستای <strong>{{ $village->name }}</strong> — سال
                        <strong>{{ \App\Support\Number::fa($year) }}</strong>
                    </p>
                    @if($set->announced_at)
                    <p class="text-sm text-gray-500 mt-1">
                        اعلام‌شده در {{ \Morilog\Jalali\Jalalian::fromCarbon($set->announced_at)->format('Y/m/d') }}
                    </p>
                    @endif
                </div>

                {{-- انتخاب سال --}}
                @if($years->count() > 1)
                <div class="flex gap-2 flex-wrap">
                    @foreach($years as $y)
                    <a href="{{ route('village.tariff.index', ['slug' => $village->slug, 'year' => $y]) }}"
                       class="px-3 py-1 rounded-lg border text-sm
                              {{ $y == $year ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-700 border-gray-300 hover:border-emerald-500' }}">
                        {{ \App\Support\Number::fa($y) }}
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
        </header>

        {{-- ═══ هشدار ═══ --}}
        <div class="bg-amber-50 border-r-4 border-amber-500 p-4 rounded-lg mb-6 text-amber-900 text-sm flex items-start gap-3">
            <span class="text-2xl">⚠️</span>
            <div>
                <strong>توجه:</strong>
                دریافت وجه نقد توسط دهیار یا کارکنان <strong>ممنوع</strong> است.
                وجوه باید به حساب بانکی دهیاری واریز شود.
                @if($village->bank_iban)
                <div class="mt-2 text-xs font-mono bg-amber-100 p-2 rounded" dir="ltr">
                    شبا: {{ $village->bank_iban }}
                </div>
                @endif
            </div>
        </div>

        {{-- ═══ تب‌ها ═══ --}}
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="flex border-b overflow-x-auto">
                <button @click="activeTab = 'levy'"
                        :class="activeTab === 'levy' ? 'border-b-2 border-emerald-600 text-emerald-700 font-bold' : 'text-gray-600 hover:text-emerald-600'"
                        class="px-6 py-3 whitespace-nowrap transition">
                    🏗️ عوارض عمرانی
                    <span class="text-xs bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full mr-2">
                        {{ $grouped->get('levy', collect())->count() }}
                    </span>
                </button>
                <button @click="activeTab = 'service_fee'"
                        :class="activeTab === 'service_fee' ? 'border-b-2 border-emerald-600 text-emerald-700 font-bold' : 'text-gray-600 hover:text-emerald-600'"
                        class="px-6 py-3 whitespace-nowrap transition">
                    💰 بهای خدمات
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full mr-2">
                        {{ $grouped->get('service_fee', collect())->count() }}
                    </span>
                </button>
                @auth
                <button @click="activeTab = 'edit'"
                        :class="activeTab === 'edit' ? 'border-b-2 border-orange-600 text-orange-700 font-bold' : 'text-gray-600 hover:text-orange-600'"
                        class="px-6 py-3 whitespace-nowrap transition">
                    ✏️ ویرایش (کارشناسان)
                </button>
                @endauth
            </div>
        </div>

        {{-- ═══ تب levy ═══ --}}
        <div x-show="activeTab === 'levy'" class="bg-white rounded-lg shadow-md p-6">
            @include('public.tariff._table', ['items' => $grouped->get('levy', collect()), 'type' => 'levy'])
        </div>

        {{-- ═══ تب service_fee ═══ --}}
        <div x-show="activeTab === 'service_fee'" class="bg-white rounded-lg shadow-md p-6">
            @include('public.tariff._table', ['items' => $grouped->get('service_fee', collect()), 'type' => 'service_fee'])
        </div>

        {{-- ═══ تب ویرایش ═══ --}}
        @auth
        <div x-show="activeTab === 'edit'" x-cloak class="bg-white rounded-lg shadow-md p-6">
            
        </div>
        @endauth

        {{-- ═══ دکمه‌های ابزار ═══ --}}
        <div class="mt-6 flex gap-2 flex-wrap">
            <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900">
                🖨️ چاپ
            </button>
        </div>
    </div>

    <style>[x-cloak] { display: none !important; }</style>
</div>

<script>
function tariffPage() {
    return {
        activeTab: 'levy',
        init() {
            // اگر کارشناس فنی/مالی هستیم، به تب ویرایش برو
            @auth
            const role = @js(auth()->user()->role);
            const officerType = @js(auth()->user()->officer_type ?? null);
            if (role === 'fee_officer' || role === 'super_admin') {
                this.activeTab = 'edit';
            }
            @endauth
        }
    }
}
</script>
@endsection

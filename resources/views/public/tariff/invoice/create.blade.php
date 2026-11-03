@extends('village.site._layout')
@section('title', 'صدور فیش عوارض — ' . $village->name)

@section('content')
@php
    $allItemsArray = $items->flatten()->map(function ($i) {
        return [
            'id'     => (int) $i->id,
            'title'  => (string) ($i->title->name_fa ?? ''),
            'amount' => (int) $i->final_amount,
        ];
    })->values()->toArray();
@endphp
<div class="container mx-auto px-4 py-8" dir="rtl" x-data="invoiceCreate()">

    <div class="mb-6">
        <a href="{{ route('village.tariff.index', $village->slug) }}" class="text-emerald-700 text-sm">← بازگشت به تعرفه‌ها</a>
        <h1 class="text-2xl font-bold text-emerald-800 mt-2">📄 صدور فیش عوارض</h1>
        <p class="text-gray-600 text-sm mt-1">روستای {{ $village->name }} — سال {{ \App\Support\Number::fa($set->jalali_year ?? '') }}</p>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border-r-4 border-red-500 p-3 rounded mb-4 text-red-800 text-sm">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    {{-- ═══════════════════ اطلاعات حساب بانکی جهت پرداخت ═══════════════════ --}}
    @if($village->bank_account || $village->bank_iban)
    <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-5 mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-sm">
        <div>
            <h2 class="font-bold text-blue-900 text-lg flex items-center gap-2">
                🏦 اطلاعات حساب جهت واریز عوارض
            </h2>
            <p class="text-sm text-blue-700 mt-1 mb-0">
                لطفاً مبالغ را به حساب زیر واریز نموده و فیش را در سیستم بارگذاری کنید.
            </p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-blue-100 min-w-[280px]">
            @if($village->bank_name)
                <div class="text-sm text-gray-700 mb-2"><strong>بانک:</strong> {{ $village->bank_name }}</div>
            @endif
            @if($village->bank_account)
                <div class="text-sm text-gray-700 mb-2 flex items-center justify-between gap-4">
                    <strong>شماره حساب:</strong>
                    <span class="font-mono text-base font-bold text-blue-900 select-all" dir="ltr">{{ $village->bank_account }}</span>
                </div>
            @endif
            @if($village->bank_iban)
                <div class="text-sm text-gray-700 flex items-center justify-between gap-4">
                    <strong>شماره شبا:</strong>
                    <span class="font-mono text-sm text-blue-900 select-all" dir="ltr">{{ $village->bank_iban }}</span>
                </div>
            @endif
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('village.tariff.invoice.issue', $village->slug) }}" class="space-y-4">
        @csrf

        <div class="bg-white rounded-lg shadow p-5">
            <h2 class="font-bold mb-4">👤 اطلاعات پرداخت‌کننده</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <label class="block">
                    <span class="text-sm font-medium">نام و نام خانوادگی *</span>
                    <input name="payer_name" value="{{ old('payer_name') }}" required class="w-full border rounded p-2 mt-1">
                </label>
                <label class="block">
                    <span class="text-sm font-medium">کد ملی (اختیاری)</span>
                    <input name="payer_national_id" value="{{ old('payer_national_id') }}" inputmode="numeric" maxlength="10" dir="ltr" class="w-full border rounded p-2 mt-1 font-mono" placeholder="۱۰ رقم">
                </label>
                <label class="block">
                    <span class="text-sm font-medium">شماره موبایل *</span>
                    <input name="payer_mobile" value="{{ old('payer_mobile') }}" required inputmode="numeric" maxlength="11" dir="ltr" class="w-full border rounded p-2 mt-1 font-mono" placeholder="09xxxxxxxxx">
                </label>
                <label class="block">
                    <span class="text-sm font-medium">موضوع (اختیاری)</span>
                    <input name="subject" value="{{ old('subject') }}" maxlength="500" class="w-full border rounded p-2 mt-1" placeholder="مثلاً: عوارض سال ۱۴۰۵">
                </label>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5">
            <h2 class="font-bold mb-4">📋 انتخاب عوارض / خدمات</h2>

            @forelse($items as $type => $typeItems)
            <div class="mb-4">
                <h3 class="text-sm font-bold text-emerald-700 mb-2 pb-2 border-b">
                    {{ $type === 'levy' ? '🏗️ عوارض عمرانی' : '💰 بهای خدمات' }}
                </h3>
                <div class="space-y-2">
                    @foreach($typeItems as $item)
                    <label class="flex items-start gap-3 p-3 border rounded cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" class="mt-1" @change="toggleItem({{ $item->id }})" :checked="selectedItems.includes({{ $item->id }})">
                        <div class="flex-1">
                            <div class="font-medium">{{ $item->title->name_fa ?? '-' }}</div>
                            @if($item->notes)
                            <div class="text-xs text-gray-500 mt-1">{{ $item->notes }}</div>
                            @endif
                            <div class="text-sm mt-2">
                                مبلغ پایه: <span class="font-mono font-bold">{{ \App\Support\Number::money($item->final_amount) }}</span> ریال
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">هیچ آیتم فعالی برای این تعرفه وجود ندارد.</div>
            @endforelse

            <template x-for="(item, index) in selectedItemsData" :key="item.id">
                <div class="border rounded p-4 mt-4 bg-emerald-50">
                    <h4 class="font-bold mb-3" x-text="item.title"></h4>
                    <div class="grid md:grid-cols-3 gap-3">
                        <label class="block">
                            <span class="text-sm">مبلغ (ریال)</span>
                            <input type="number" min="0" required :name="`items[${index}][amount]`" x-model="item.amount" class="w-full border rounded p-2 mt-1 font-mono">
                        </label>
                        <label class="block">
                            <span class="text-sm">تخفیف (%)</span>
                            <input type="number" min="0" max="100" step="0.01" :name="`items[${index}][discount]`" x-model="item.discount" class="w-full border rounded p-2 mt-1 font-mono">
                        </label>
                        <label class="block">
                            <span class="text-sm">توضیحات</span>
                            <input type="text" :name="`items[${index}][notes]`" x-model="item.notes" class="w-full border rounded p-2 mt-1">
                        </label>
                    </div>
                    <input type="hidden" :name="`items[${index}][id]`" :value="item.id">
                </div>
            </template>
        </div>

        <div class="flex gap-3 justify-end">
            <a href="{{ route('village.tariff.index', $village->slug) }}" class="px-6 py-3 border rounded-lg">انصراف</a>
            <button type="submit" :disabled="selectedItems.length === 0" class="bg-emerald-700 text-white px-6 py-3 rounded-lg disabled:opacity-50">
                📄 صدور فیش
            </button>
        </div>
    </form>
</div>

<script>
function invoiceCreate() {
    return {
        selectedItems: [],
        allItems: @json($allItemsArray),

        get selectedItemsData() {
            return this.selectedItems.map(id => {
                const base = this.allItems.find(i => i.id === id) || { id, title: '', amount: 0 };
                return {
                    id: base.id,
                    title: base.title,
                    amount: base.amount,
                    discount: 0,
                    notes: '',
                };
            });
        },

        toggleItem(id) {
            const idx = this.selectedItems.indexOf(id);
            if (idx >= 0) {
                this.selectedItems.splice(idx, 1);
            } else {
                this.selectedItems.push(id);
            }
        }
    }
}
</script>
@endsection

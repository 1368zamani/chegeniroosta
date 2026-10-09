@extends('village.site._layout')
@section('title', 'تعرفه عوارض — ' . $village->name)

@php use App\Support\Number; @endphp

@section('content')
<div class="container mx-auto px-4 py-6" dir="rtl">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-emerald-800">📋 تعرفه عوارض و بهای خدمات</h1>
        <p class="text-gray-600 mt-1">
            روستای <strong>{{ $village->name }}</strong> · سال {{ Number::fa($set->jalali_year) }}
        </p>
    </div>

    <div class="bg-amber-50 border-r-4 border-amber-500 p-3 rounded mb-6 text-sm text-amber-900">
        ⚠️ روی عنوان هر دسته کلیک کنید تا آیتم‌های آن نمایش داده شود، سپس انتخاب کنید.
    </div>

    @if($errors->any())
    <div class="bg-red-50 border-r-4 border-red-500 p-3 rounded mb-4 text-red-800 text-sm">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('village.tariff.invoice.issue', $village->slug) }}" id="invoiceForm">
        @csrf

        {{-- اطلاعات پرداخت‌کننده --}}
        <div class="bg-white rounded-lg shadow p-5 mb-6">
            <h2 class="font-bold mb-4 text-lg">👤 اطلاعات شما</h2>
            <div class="grid md:grid-cols-3 gap-4">
                <label class="block">
                    <span class="text-sm font-medium">نام و نام خانوادگی *</span>
                    <input name="payer_name" value="{{ old('payer_name') }}" required class="w-full border rounded p-2 mt-1">
                </label>
                <label class="block">
                    <span class="text-sm font-medium">کد ملی</span>
                    <input name="payer_national_id" value="{{ old('payer_national_id') }}" inputmode="numeric" maxlength="10" dir="ltr" class="w-full border rounded p-2 mt-1 font-mono">
                </label>
                <label class="block">
                    <span class="text-sm font-medium">شماره موبایل *</span>
                    <input name="payer_mobile" value="{{ old('payer_mobile') }}" required inputmode="numeric" maxlength="11" dir="ltr" class="w-full border rounded p-2 mt-1 font-mono" placeholder="09xxxxxxxxx">
                </label>
            </div>
        </div>

        {{-- ═══ آیتم‌ها — همه باز ═══ --}}
        @foreach($items as $type => $typeItems)
        <div class="bg-white rounded-lg shadow mb-4 overflow-hidden">
            <div class="bg-emerald-50 p-4 border-b-2 border-emerald-200">
                <div class="font-bold text-lg text-emerald-800">
                    {{ $type === 'levy' ? '🏗️ عوارض عمرانی' : '💰 بهای خدمات' }}
                    <span class="text-sm text-gray-500 font-normal mr-2">({{ Number::fa($typeItems->count()) }} مورد)</span>
                </div>
            </div>

            <div class="p-4 space-y-2">
                @foreach($typeItems as $item)
                <label class="flex items-start gap-3 p-3 border-2 border-gray-200 rounded cursor-pointer hover:bg-emerald-50 transition"
                       id="label-{{ $item->id }}">
                    <input type="checkbox"
                           class="mt-1 w-5 h-5"
                           data-id="{{ $item->id }}"
                           data-amount="{{ $item->final_amount }}"
                           data-title="{{ str_replace('"', '&quot;', optional($item->title)->name_fa) }}"
                           onchange="toggleItem(this)">
                    <div class="flex-1">
                        <div class="font-medium text-gray-800">{{ optional($item->title)->name_fa }}</div>
                        <div class="text-xs text-gray-500 font-mono mt-1">{{ optional($item->title)->code }}</div>
                        @if($item->notes)
                        <div class="text-xs text-gray-600 mt-1">{{ $item->notes }}</div>
                        @endif
                        <div class="text-sm mt-2 text-emerald-700">
                            مبلغ: <span class="font-mono font-bold">{{ Number::fa(number_format($item->final_amount)) }}</span> ریال
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
        </div>
        @endforeach

        {{-- خلاصه --}}
        <div id="summaryBox" class="hidden fixed bottom-0 left-0 right-0 bg-white shadow-2xl border-t-4 border-emerald-600 p-4 z-50">
            <div class="container mx-auto">
                <div class="flex flex-wrap justify-between items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="text-xs text-gray-500 mb-1">آیتم‌های انتخاب‌شده:</div>
                        <div id="summaryItems" class="text-sm space-y-1 max-h-24 overflow-y-auto"></div>
                    </div>
                    <div class="text-left">
                        <div class="text-xs text-gray-500">جمع کل (ریال):</div>
                        <div id="summaryTotal" class="text-2xl font-bold font-mono text-emerald-700">۰</div>
                        <div id="summaryToman" class="text-xs text-gray-500">معادل ۰ تومان</div>
                    </div>
                    <button type="submit" class="bg-emerald-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-emerald-700">
                        📄 صدور فیش
                    </button>
                </div>
            </div>
        </div>

        <div style="height:200px;"></div>
    </form>

    <div class="mt-4 text-center text-sm">
        <a href="{{ route('village.tariff.index', $village->slug) }}#track" class="text-emerald-700 underline">
            🔍 پیگیری فیش صادرشده
        </a>
    </div>
</div>

<script>
let selectedItems = [];

function toggleItem(checkbox) {
    const id = parseInt(checkbox.dataset.id);
    const amount = parseInt(checkbox.dataset.amount);
    const title = checkbox.dataset.title;
    const label = document.getElementById('label-' + id);

    if (checkbox.checked) {
        selectedItems.push({ id, amount, title });
        label.classList.add('border-emerald-500', 'bg-emerald-50');
        label.classList.remove('border-gray-200');
    } else {
        selectedItems = selectedItems.filter(i => i.id !== id);
        label.classList.remove('border-emerald-500', 'bg-emerald-50');
        label.classList.add('border-gray-200');
    }
    updateSummary();
}

function updateSummary() {
    const box = document.getElementById('summaryBox');
    const itemsDiv = document.getElementById('summaryItems');
    const totalDiv = document.getElementById('summaryTotal');
    const tomanDiv = document.getElementById('summaryToman');

    if (selectedItems.length === 0) {
        box.classList.add('hidden');
        return;
    }

    box.classList.remove('hidden');

    let html = '';
    let total = 0;

    selectedItems.forEach((item, idx) => {
        total += item.amount;
        html += '<div class="flex justify-between text-sm border-b pb-1">' +
                '<span>• ' + item.title + '</span>' +
                '<span class="font-mono">' + formatNumber(item.amount) + '</span>' +
                '</div>' +
                '<input type="hidden" name="items[' + idx + '][id]" value="' + item.id + '">' +
                '<input type="hidden" name="items[' + idx + '][amount]" value="' + item.amount + '">';
    });

    itemsDiv.innerHTML = html;
    totalDiv.textContent = formatNumber(total);
    tomanDiv.textContent = 'معادل ' + formatNumber(Math.round(total / 10)) + ' تومان';
}

function formatNumber(v) {
    const n = Number(v) || 0;
    return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
        .replace(/\d/g, d => '۰۱۲۳۴۵۶۷۸۹'[d]);
}

document.getElementById('invoiceForm').addEventListener('submit', function(e) {
    if (selectedItems.length === 0) {
        e.preventDefault();
        alert('⚠️ لطفاً حداقل یک آیتم تعرفه را انتخاب کنید.');
        return false;
    }

    const name = document.querySelector('input[name="payer_name"]').value.trim();
    const mobile = document.querySelector('input[name="payer_mobile"]').value.trim();

    if (!name || name.length < 3) {
        e.preventDefault();
        alert('⚠️ لطفاً نام و نام خانوادگی خود را کامل وارد کنید.');
        return false;
    }

    if (!mobile || mobile.length < 10) {
        e.preventDefault();
        alert('⚠️ لطفاً شماره موبایل معتبر وارد کنید.');
        return false;
    }

    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = '⏳ در حال صدور...';
    return true;
});
</script>
@endsection

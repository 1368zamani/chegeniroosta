@extends('village.site._layout')
@section('title', 'عوارض و بهای خدمات — ' . $village->name)

@php
    use App\Support\Number;

    $groupMeta = [
        'waste' => [
            'title' => 'عوارض پسماند',
            'icon'  => '🗑',
            'desc'  => 'جمع‌آوری و دفع زباله‌ی خانگی، تجاری و صنعتی',
            'color' => '#2e7d32',
            'soft'  => '#e8f5e9',
            'border'=> '#a5d6a7',
        ],
        'new_build' => [
            'title' => 'ساخت‌وساز جدید',
            'icon'  => '🏗',
            'desc'  => 'پروانه‌ی ساخت، زیربنا، تفکیک و الحاق',
            'color' => '#1565c0',
            'soft'  => '#e3f2fd',
            'border'=> '#90caf9',
        ],
        'renovation' => [
            'title' => 'ساخت‌وساز تعمیری',
            'icon'  => '🔨',
            'desc'  => 'بازسازی، تمدید پروانه و تغییر کاربری',
            'color' => '#e65100',
            'soft'  => '#fff3e0',
            'border'=> '#ffcc80',
        ],
    ];

    $wasteCount = $groups['waste']->count();
    $constrCount = $groups['new_build']->count() + $groups['renovation']->count();
@endphp

@section('content')
<div class="container mx-auto px-4 py-6" dir="rtl" id="tariffApp">

    {{-- ═══════════════════ سرصفحه ═══════════════════ --}}
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-emerald-800 m-0">💰 عوارض و بهای خدمات</h1>
        <p class="text-gray-600 mt-2 mb-0 text-sm md:text-base">
            روستای <strong class="text-emerald-700">{{ $village->name }}</strong>
            · سال <strong>{{ Number::fa($set->jalali_year) }}</strong>
            · تعرفه‌ی مصوب شورای اسلامی روستا
        </p>

        {{-- دانلود PDF رسمی --}}
        <div class="mt-3 flex flex-wrap items-center gap-2">
            <a href="{{ route('download.tariff-pdf', [$set->id, 'form2']) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-700 text-white rounded-lg text-sm hover:bg-emerald-800">
                ⬇️ فرم ۲ (عوارض محلی)
            </a>
            <a href="{{ route('download.tariff-pdf', [$set->id, 'form3']) }}"
               class="inline-flex items-center gap-2 px-4 py-2 border border-emerald-700 text-emerald-800 rounded-lg text-sm hover:bg-emerald-50">
                ⬇️ فرم ۳ (بهای خدمات)
            </a>
            @if(($set->stat_downloads ?? 0) > 0)
                <span class="text-xs text-gray-500">
                    👁 {{ Number::fa(number_format($set->stat_downloads)) }} بار دانلود شده
                </span>
            @endif
        </div>
    </div>

    @if($errors->any())
        <div class="mb-5 p-4 rounded-xl text-sm" style="background:#ffebee;color:#b71c1c;border-right:4px solid #c62828;">
            @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
        </div>
    @endif

    {{-- ═══════════════════ کارت‌های دسته ═══════════════════ --}}
    <div id="catCards" class="grid md:grid-cols-2 gap-4 mb-6">

        <button type="button" class="cat-card text-right rounded-2xl p-6 transition-all"
                data-cat="waste"
                style="background:{{ $groupMeta['waste']['soft'] }};border:2px solid {{ $groupMeta['waste']['border'] }};">
            <div class="flex items-start justify-between">
                <div class="text-4xl">{{ $groupMeta['waste']['icon'] }}</div>
                <span class="text-xs px-2.5 py-1 rounded-full bg-white/70"
                      style="color:{{ $groupMeta['waste']['color'] }};">
                    {{ Number::fa($wasteCount) }} قلم
                </span>
            </div>
            <div class="mt-3 text-lg font-bold" style="color:{{ $groupMeta['waste']['color'] }};">
                {{ $groupMeta['waste']['title'] }}
            </div>
            <div class="text-xs text-gray-600 mt-1">{{ $groupMeta['waste']['desc'] }}</div>
            <div class="mt-3 text-xs font-bold" style="color:{{ $groupMeta['waste']['color'] }};">
                مشاهده و انتخاب ←
            </div>
        </button>

        <button type="button" class="cat-card text-right rounded-2xl p-6 transition-all"
                data-cat="construction"
                style="background:{{ $groupMeta['new_build']['soft'] }};border:2px solid {{ $groupMeta['new_build']['border'] }};">
            <div class="flex items-start justify-between">
                <div class="text-4xl">🏗</div>
                <span class="text-xs px-2.5 py-1 rounded-full bg-white/70"
                      style="color:{{ $groupMeta['new_build']['color'] }};">
                    {{ Number::fa($constrCount) }} قلم
                </span>
            </div>
            <div class="mt-3 text-lg font-bold" style="color:{{ $groupMeta['new_build']['color'] }};">
                عوارض ساخت‌وساز
            </div>
            <div class="text-xs text-gray-600 mt-1">
                جدید و تعمیری — زیربنا، پروانه، تفکیک و بازسازی
            </div>
            <div class="mt-3 text-xs font-bold" style="color:{{ $groupMeta['new_build']['color'] }};">
                مشاهده و انتخاب ←
            </div>
        </button>
    </div>

    {{-- ═══════════════════ فرم صدور فیش ═══════════════════ --}}
    <form method="POST" action="{{ route('village.tariff.invoice.issue', $village->slug) }}" id="invoiceForm">
        @csrf
        <div id="hiddenItems"></div>

        {{-- ─── پنل پسماند ─── --}}
        <div class="cat-panel" data-panel="waste" style="display:none;">
            <div class="flex items-center justify-between flex-wrap gap-2 mb-4">
                <h2 class="text-lg font-bold m-0" style="color:{{ $groupMeta['waste']['color'] }};">
                    🗑 عوارض پسماند
                </h2>
                <button type="button" class="back-to-cats text-xs px-3 py-1.5 rounded-lg bg-gray-100 text-gray-600">
                    ← بازگشت به دسته‌ها
                </button>
            </div>

            @if($groups['waste']->isEmpty())
                <div class="bg-white rounded-xl p-10 text-center text-gray-400 text-sm shadow-sm">
                    برای این دسته قلمی تعریف نشده است.
                </div>
            @else
                <div class="space-y-3">
                    @foreach($groups['waste'] as $card)
                        @include('village.tariff._item_card', ['card' => $card, 'meta' => $groupMeta['waste']])
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ─── پنل ساخت‌وساز ─── --}}
        <div class="cat-panel" data-panel="construction" style="display:none;">
            <div class="flex items-center justify-between flex-wrap gap-2 mb-4">
                <h2 class="text-lg font-bold m-0" style="color:{{ $groupMeta['new_build']['color'] }};">
                    🏗 عوارض ساخت‌وساز
                </h2>
                <button type="button" class="back-to-cats text-xs px-3 py-1.5 rounded-lg bg-gray-100 text-gray-600">
                    ← بازگشت به دسته‌ها
                </button>
            </div>

            {{-- زیرتب‌ها --}}
            <div class="flex flex-wrap gap-2 mb-4">
                <button type="button" class="sub-tab px-4 py-2 rounded-lg text-sm font-bold transition-all"
                        data-sub="new_build">
                    🏗 ساخت‌وساز جدید
                    <span class="text-xs opacity-70">({{ Number::fa($groups['new_build']->count()) }})</span>
                </button>
                <button type="button" class="sub-tab px-4 py-2 rounded-lg text-sm font-bold transition-all"
                        data-sub="renovation">
                    🔨 ساخت‌وساز تعمیری
                    <span class="text-xs opacity-70">({{ Number::fa($groups['renovation']->count()) }})</span>
                </button>
            </div>

            {{-- زیرپنل جدید --}}
            <div class="sub-panel" data-subpanel="new_build" style="display:none;">
                @if($groups['new_build']->isEmpty())
                    <div class="bg-white rounded-xl p-10 text-center text-gray-400 text-sm shadow-sm">
                        برای این دسته قلمی تعریف نشده است.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($groups['new_build'] as $card)
                            @include('village.tariff._item_card', ['card' => $card, 'meta' => $groupMeta['new_build']])
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- زیرپنل تعمیری --}}
            <div class="sub-panel" data-subpanel="renovation" style="display:none;">
                @if($groups['renovation']->isEmpty())
                    <div class="bg-white rounded-xl p-10 text-center text-gray-400 text-sm shadow-sm">
                        برای این دسته قلمی تعریف نشده است.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($groups['renovation'] as $card)
                            @include('village.tariff._item_card', ['card' => $card, 'meta' => $groupMeta['renovation']])
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- ─── مودال اطلاعات پرداخت‌کننده ─── --}}
        <div id="payerModal" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(15,23,42,.55);align-items:center;justify-content:center;padding:16px;">
            <div style="background:#fff;border-radius:16px;width:100%;max-width:520px;max-height:92vh;overflow-y:auto;box-shadow:0 24px 60px rgba(0,0,0,.28);">
                <div class="p-5 border-b border-gray-100" style="background:#e8f5e9;">
                    <div class="font-bold text-base" style="color:#1b5e20;">📝 اطلاعات شما برای صدور فیش</div>
                    <div class="text-xs text-gray-600 mt-1">
                        جمع مبلغ انتخابی: <strong id="modalTotal" class="font-mono">—</strong>
                    </div>
                </div>

                <div class="p-5">
                    <div class="mb-4">
                        <label class="block mb-1 text-sm font-bold text-gray-700">نام و نام خانوادگی *</label>
                        <input name="payer_name" required minlength="3" maxlength="200"
                               value="{{ old('payer_name') }}"
                               class="w-full border-2 border-gray-300 rounded-lg p-2.5 focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 text-sm font-bold text-gray-700">کد ملی</label>
                        <input name="payer_national_id" inputmode="numeric" maxlength="10" dir="ltr"
                               value="{{ old('payer_national_id') }}"
                               class="w-full border-2 border-gray-300 rounded-lg p-2.5 font-mono text-left focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 text-sm font-bold text-gray-700">شماره موبایل *</label>
                        <input name="payer_mobile" required inputmode="numeric" maxlength="11" dir="ltr"
                               value="{{ old('payer_mobile') }}" placeholder="09xxxxxxxxx"
                               class="w-full border-2 border-gray-300 rounded-lg p-2.5 font-mono text-left focus:border-emerald-500 focus:outline-none">
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 text-sm font-bold text-gray-700">موضوع (اختیاری)</label>
                        <input name="subject" maxlength="500" value="{{ old('subject') }}"
                               placeholder="مثلاً: ساخت‌وساز مسکونی"
                               class="w-full border-2 border-gray-300 rounded-lg p-2.5 focus:border-emerald-500 focus:outline-none">
                    </div>

                    <div class="rounded-lg p-3 mb-4 text-xs leading-relaxed" style="background:#f1f8e9;color:#33691e;">
                        با تأیید، فیش برای شما صادر می‌شود و شماره پیگیری دریافت می‌کنید.
                        پرداخت پس از تأیید کارشناس انجام می‌شود.
                    </div>

                    <div class="flex gap-2 justify-end">
                        <button type="button" id="payerCancel"
                                class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-bold">انصراف</button>
                        <button type="submit" id="submitBtn"
                                class="px-5 py-2 rounded-lg text-white text-sm font-bold" style="background:#2e7d32;">
                            📄 صدور فیش
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- ═══════════════════ نوار جمع کل ═══════════════════ --}}
    <div id="summaryBox" style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:8000;background:#fff;border-top:4px solid #2e7d32;box-shadow:0 -6px 24px rgba(0,0,0,.12);padding:14px 16px;">
        <div class="container mx-auto flex flex-wrap items-center justify-between gap-3">
            <div class="flex-1 min-w-0">
                <div class="text-xs text-gray-500 mb-1">آیتم‌های انتخاب‌شده:</div>
                <div id="summaryItems" class="text-xs space-y-0.5 max-h-20 overflow-y-auto text-gray-700"></div>
            </div>
            <div class="text-center px-3">
                <div class="text-xs text-gray-500">جمع کل:</div>
                <div id="summaryTotal" class="text-xl font-extrabold font-mono" style="color:#2e7d32;">۰</div>
                <div id="summaryToman" class="text-[11px] text-gray-500">ریال</div>
            </div>
            <button type="button" id="continueBtn"
                    class="px-6 py-3 rounded-xl text-white font-bold text-sm" style="background:#2e7d32;">
                ادامه →
            </button>
        </div>
    </div>

    {{-- لینک پیگیری --}}
    <div class="mt-6 mb-24 text-center text-sm">
        <a href="{{ route('tariff.invoice.track.form', $village->slug) }}"
           class="text-emerald-700 underline">🔍 پیگیری فیش صادرشده</a>
    </div>

    <div class="text-[11px] text-gray-400 leading-relaxed mb-4">
        ℹ️ سایر عوارض (مشاغل، آرامستان، پارکینگ و…) در این صفحه نمایش داده نمی‌شوند.
        برای پرداخت آن‌ها با دهیاری تماس بگیرید.
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    'use strict';

    const FA = '۰۱۲۳۴۵۶۷۸۹';
    const AR = '٠١٢٣٤٥٦٧٨٩';

    function faNum(n) {
        const v = Number(n) || 0;
        return v.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '٬').replace(/\d/g, d => FA[d]);
    }

    // ═══════════ انتخاب دسته ═══════════
    const catCards  = document.querySelectorAll('.cat-card');
    const catPanels = document.querySelectorAll('.cat-panel');

    function showCategory(cat) {
        document.getElementById('catCards').style.display = 'none';

        catPanels.forEach(p => {
            p.style.display = p.dataset.panel === cat ? '' : 'none';
        });

        if (cat === 'construction') {
            showSubTab('new_build');
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function showCats() {
        document.getElementById('catCards').style.display = '';
        catPanels.forEach(p => { p.style.display = 'none'; });
    }

    catCards.forEach(c => c.addEventListener('click', () => showCategory(c.dataset.cat)));
    document.querySelectorAll('.back-to-cats').forEach(b => b.addEventListener('click', showCats));

    // ═══════════ زیرتب‌ها ═══════════
    const subTabs   = document.querySelectorAll('.sub-tab');
    const subPanels = document.querySelectorAll('.sub-panel');

    function showSubTab(key) {
        subTabs.forEach(t => {
            const on = t.dataset.sub === key;
            t.style.background = on ? '#1565c0' : '#eceff1';
            t.style.color = on ? '#fff' : '#546e7a';
        });
        subPanels.forEach(p => {
            p.style.display = p.dataset.subpanel === key ? '' : 'none';
        });
    }

    subTabs.forEach(t => t.addEventListener('click', () => showSubTab(t.dataset.sub)));

    // ═══════════ انتخاب آیتم‌ها ═══════════
    let selected = [];

    document.querySelectorAll('.item-check').forEach(cb => {
        cb.addEventListener('change', () => {
            const id     = parseInt(cb.dataset.id, 10);
            const amount = parseInt(cb.dataset.amount, 10) || 0;
            const title  = cb.dataset.title || '';

            if (cb.checked) {
                selected.push({ id, amount, title });
            } else {
                selected = selected.filter(x => x.id !== id);
            }

            const card = document.getElementById('card-' + id);
            if (card) {
                card.style.borderColor = cb.checked ? '#2e7d32' : '#e0e0e0';
                card.style.background  = cb.checked ? '#f1f8e9' : '#fff';
            }

            updateSummary();
        });
    });

    function updateSummary() {
        const box     = document.getElementById('summaryBox');
        const itemsEl = document.getElementById('summaryItems');
        const totalEl = document.getElementById('summaryTotal');
        const tomanEl = document.getElementById('summaryToman');
        const hidden  = document.getElementById('hiddenItems');

        if (!selected.length) {
            box.style.display = 'none';
            hidden.innerHTML = '';
            return;
        }

        box.style.display = '';
        let total = 0;
        let html = '';
        let inputs = '';

        selected.forEach((item, idx) => {
            total += item.amount;
            html += '<div class="flex justify-between gap-2">'
                 +  '<span class="truncate">• ' + item.title + '</span>'
                 +  '<span class="font-mono shrink-0">' + faNum(item.amount) + '</span>'
                 +  '</div>';
            inputs += '<input type="hidden" name="items[' + idx + '][id]" value="' + item.id + '">'
                   +  '<input type="hidden" name="items[' + idx + '][amount]" value="' + item.amount + '">';
        });

        itemsEl.innerHTML = html;
        hidden.innerHTML  = inputs;
        totalEl.textContent = faNum(total);
        tomanEl.textContent = 'ریال — معادل ' + faNum(Math.round(total / 10)) + ' تومان';

        const modalTotal = document.getElementById('modalTotal');
        if (modalTotal) modalTotal.textContent = faNum(total) + ' ریال';
    }

    // ═══════════ مودال پرداخت‌کننده ═══════════
    const modal = document.getElementById('payerModal');

    document.getElementById('continueBtn').addEventListener('click', () => {
        if (!selected.length) {
            alert('⚠️ لطفاً حداقل یک آیتم را انتخاب کنید.');
            return;
        }
        modal.style.display = 'flex';
    });

    document.getElementById('payerCancel').addEventListener('click', () => {
        modal.style.display = 'none';
    });

    modal.addEventListener('click', e => {
        if (e.target === modal) modal.style.display = 'none';
    });

    // ═══════════ اعتبارسنجی نهایی ═══════════
    document.getElementById('invoiceForm').addEventListener('submit', function (e) {
        if (!selected.length) {
            e.preventDefault();
            alert('⚠️ لطفاً حداقل یک آیتم را انتخاب کنید.');
            return;
        }

        const name   = (this.querySelector('input[name="payer_name"]').value || '').trim();
        const mobile = (this.querySelector('input[name="payer_mobile"]').value || '').trim();

        if (name.length < 3) {
            e.preventDefault();
            alert('⚠️ لطفاً نام و نام خانوادگی را کامل وارد کنید.');
            return;
        }
        if (mobile.replace(/\D/g, '').length < 10) {
            e.preventDefault();
            alert('⚠️ لطفاً شماره موبایل معتبر وارد کنید.');
            return;
        }

        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.textContent = '⏳ در حال صدور...';
    });
})();
</script>
@endsection

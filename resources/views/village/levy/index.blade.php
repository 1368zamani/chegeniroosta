@extends('village.site._layout')
@section('title', 'عوارض روستا — ' . $village->name)

@php
    use App\Support\Number;
@endphp

@section('content')
<div class="container mx-auto px-4 py-6" dir="rtl" id="levyApp">
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-emerald-800 m-0">💰 عوارض روستا</h1>
        <p class="text-gray-600 mt-2 mb-0 text-sm md:text-base">
            روستای <strong class="text-emerald-700">{{ $village->name }}</strong>
            · محاسبه بر اساس تعرفهٔ مصوب و ضرایب پروندهٔ اکسل عوارض
        </p>
    </div>

    @if(session('success'))
        <div class="mb-5 p-4 rounded-xl text-sm" style="background:#e8f5e9;color:#1b5e20;border-right:4px solid #2e7d32;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 p-4 rounded-xl text-sm" style="background:#fdeceb;color:#8e2f18;border-right:4px solid #b3401f;">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-5 p-4 rounded-xl text-sm" style="background:#fdeceb;color:#8e2f18;border-right:4px solid #b3401f;">
            @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
        </div>
    @endif

    {{-- تبها --}}
    <div class="flex gap-2 mb-5 border-b border-gray-200">
        <button type="button" class="levy-tab px-5 py-3 font-bold text-sm rounded-t-lg" data-tab="construction"
                style="border-bottom:3px solid #27689f;color:#27689f;">🏗 عوارض احداث بنا</button>
        <button type="button" class="levy-tab px-5 py-3 font-bold text-sm rounded-t-lg" data-tab="waste"
                style="border-bottom:3px solid transparent;color:#6b7280;">🗑 عوارض پسماند</button>
        <a href="{{ route('tariff.invoice.track.form', $village->slug) }}"
           class="mr-auto px-4 py-3 text-sm text-emerald-700 hover:underline">پیگیری درخواست →</a>
    </div>

    <form method="POST" action="{{ route('village.tariff.invoice.issue', $village->slug) }}" id="levyForm">
        @csrf
        <input type="hidden" name="type" id="levyType" value="construction">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- ═══ ستون اصلی: ورودیها ═══ --}}
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-200 p-5 md:p-6">

                {{-- پنل احداث بنا --}}
                <div class="levy-panel" data-panel="construction">
                    <h2 class="text-lg font-bold text-blue-800 mb-1">محاسبهٔ عوارض احداث بنا</h2>
                    <p class="text-xs text-gray-500 mb-5">
                        مبلغ بر اساس ارزش معاملاتی روستا و ضرایب پروندهٔ اکسل محاسبه میشود.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">
                                مساحت ساخت (متر مربع) <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" inputmode="decimal" dir="ltr" name="construction_area" id="constructionArea"
                                   value="{{ old('construction_area') }}"
                                   placeholder="مثال: 5460"
                                   class="w-full border-2 border-gray-300 rounded-lg p-2.5 text-left focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">
                                مساحت ملک (متر مربع)
                            </label>
                            <input type="text" inputmode="decimal" dir="ltr" name="property_area" id="propertyArea"
                                   value="{{ old('property_area') }}"
                                   placeholder="اختیاری"
                                   class="w-full border-2 border-gray-300 rounded-lg p-2.5 text-left focus:border-blue-500 focus:outline-none">
                            @if($params && $params->density_area_source === 'R')
                                <p class="text-xs text-amber-700 mt-1">در این روستا مساحت ملک در محاسبهٔ تراکم استفاده میشود.</p>
                            @endif
                        </div>
                    </div>

                    <button type="button" id="calcConstruction"
                            class="mt-4 px-5 py-2.5 rounded-lg bg-blue-700 text-white text-sm font-bold hover:bg-blue-800">
                        محاسبه مبلغ
                    </button>
                </div>

                {{-- پنل پsmاند --}}
                <div class="levy-panel" data-panel="waste" style="display:none;">
                    <h2 class="text-lg font-bold mb-1" style="color:#2e7d32;">انتخاب اقلام عوارض پسماند</h2>
                    <p class="text-xs text-gray-500 mb-4">
                        مبالغ توسط کارشناس عمرانی تعیین و بهصورت سراسری اعمال شدهاند.
                    </p>

                    @if($wasteTitles->isEmpty())
                        <div class="p-4 rounded-lg bg-amber-50 text-amber-800 text-sm">
                            فعلاً قلم فعالی برای عوارض پسماند ثبت نشده است.
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach($wasteTitles as $title)
                                <div class="waste-row flex flex-wrap items-center gap-3 p-3 rounded-lg border border-gray-200"
                                     data-price="{{ (int) $title->base_amount }}"
                                     data-id="{{ $title->id }}">
                                    <label class="flex items-center gap-2 flex-1 min-w-[200px] cursor-pointer">
                                        <input type="checkbox" class="waste-check" name="items[{{ $loop->index }}][enabled]" value="1">
                                        <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $title->id }}">
                                        <span class="text-sm text-gray-800">{{ $title->name_fa }}</span>
                                    </label>
                                    <span class="text-xs text-gray-500">{{ $title->unit }}</span>
                                    <span class="text-sm font-bold text-gray-700" dir="ltr">{{ Number::money($title->base_amount) }} ریال</span>
                                    <input type="text" inputmode="numeric" dir="ltr"
                                           class="waste-qty w-20 border border-gray-300 rounded p-1.5 text-left text-sm"
                                           name="items[{{ $loop->index }}][qty]" value="1">
                                </div>
                            @endforeach
                        </div>
                        <button type="button" id="calcWaste"
                                class="mt-4 px-5 py-2.5 rounded-lg text-white text-sm font-bold hover:opacity-90"
                                style="background:#2e7d32;">محاسبه مبلغ</button>
                    @endif
                </div>

                {{-- مشخصات متقاضی --}}
                <div class="mt-7 pt-5 border-t border-gray-200">
                    <h3 class="text-sm font-bold text-gray-700 mb-4">مشخصات متقاضی</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">نام و نام خانوادگی <span class="text-rose-600">*</span></label>
                            <input type="text" name="payer_name" required maxlength="200"
                                   value="{{ old('payer_name') }}"
                                   class="w-full border-2 border-gray-300 rounded-lg p-2.5 focus:border-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">شماره تلفن <span class="text-rose-600">*</span></label>
                            <input type="text" name="payer_mobile" required maxlength="20" dir="ltr" inputmode="tel"
                                   value="{{ old('payer_mobile') }}"
                                   placeholder="09xxxxxxxxx"
                                   class="w-full border-2 border-gray-300 rounded-lg p-2.5 text-left focus:border-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">کد ملی (اختیاری)</label>
                            <input type="text" name="national_id" maxlength="20" dir="ltr"
                                   value="{{ old('national_id') }}"
                                   class="w-full border border-gray-300 rounded-lg p-2.5 text-left focus:border-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">توضیحات (اختیاری)</label>
                            <input type="text" name="notes" maxlength="1000" value="{{ old('notes') }}"
                                   class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-emerald-500 focus:outline-none">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ ستون کناری: نتیجه و حساب ═══ --}}
            <div class="space-y-4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
                    <h3 class="text-sm font-bold text-gray-700 mb-3">نتیجهٔ محاسبه</h3>
                    <div id="calcResult" class="text-sm text-gray-500">
                        متراژ را وارد و سپس «محاسبه مبلغ» را بزنید.
                    </div>
                    <div id="calcDetails" class="mt-3 text-xs text-gray-600 space-y-1" style="display:none;"></div>
                </div>

                <div class="rounded-2xl p-5 border" style="background:#eef6fb;border-color:#b9d9ec;">
                    <h3 class="text-sm font-bold mb-3" style="color:#1565c0;">💳 حساب واریز عوارض</h3>
                    <table class="w-full text-xs" style="color:#0d47a1;">
                        <tr><td class="py-1 text-gray-600">نام دارنده:</td><td class="py-1 font-bold">{{ $account['holder'] }}</td></tr>
                        <tr><td class="py-1 text-gray-600">بانک:</td><td class="py-1 font-bold">{{ $account['bank'] }}</td></tr>
                        @if($account['number'])
                        <tr><td class="py-1 text-gray-600">شماره حساب:</td><td class="py-1 font-bold" dir="ltr">{{ $account['number'] }}</td></tr>
                        @endif
                        @if($account['iban'])
                        <tr><td class="py-1 text-gray-600">شبا:</td><td class="py-1 font-bold" dir="ltr">{{ $account['iban'] }}</td></tr>
                        @endif
                    </table>
                    <p class="text-[11px] text-gray-600 mt-3 leading-relaxed">
                        پس از ثبت درخواست، مبلغ را به حساب فوق واریز نموده و تصویر رسید را در صفحهٔ بعدی آپلود کنید.
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-5 flex flex-wrap items-center gap-3">
            <button type="submit" class="px-6 py-3 rounded-lg bg-emerald-700 text-white font-bold text-sm hover:bg-emerald-800">
                ثبت درخواست و ادامه به پرداخت
            </button>
            <span class="text-xs text-gray-500">پس از ثبت، کد پیگیری برای شما نمایش داده میشود.</span>
        </div>
    </form>
</div>

<script>
(function () {
    var tabs = document.querySelectorAll('.levy-tab');
    var panels = document.querySelectorAll('.levy-panel');
    var typeInput = document.getElementById('levyType');

    function activate(tab) {
        tabs.forEach(function (t) {
            var on = t === tab;
            t.style.borderBottom = on ? '3px solid ' + (on.dataset.tab === 'construction' ? '#27689f' : '#2e7d32') : '3px solid transparent';
            t.style.color = on ? (on.dataset.tab === 'construction' ? '#27689f' : '#2e7d32') : '#6b7280';
        });
        panels.forEach(function (p) { p.style.display = p.dataset.panel === tab.dataset.tab ? '' : 'none'; });
        typeInput.value = tab.dataset.tab;
        document.getElementById('calcResult').textContent = 'متراژ / اقلام را مشخص و سپس «محاسبه مبلغ» را بزنید.';
        document.getElementById('calcDetails').style.display = 'none';
    }
    tabs.forEach(function (t) { t.addEventListener('click', function () { activate(t); }); });

    var calculateUrl = @json(route('tariff.public.calculate', $village->slug));
    var resultBox = document.getElementById('calcResult');
    var detailsBox = document.getElementById('calcDetails');

    function toFa(n) {
        return String(n).replace(/\d/g, function (d) { return '۰۱۲۳۴۵۶۷۸۹'[parseInt(d, 10)]; });
    }
    function fmt(n) { return toFa(Number(n).toLocaleString('en-US')); }

    function render(data) {
        if (!data.ok) { resultBox.textContent = 'خطا در محاسبه.'; return; }
        resultBox.innerHTML = '<span class="text-gray-600 text-xs">مبلغ قابل پرداخت:</span> '
            + '<strong class="text-xl text-emerald-700">' + data.money + ' ریال</strong>';
        var d = '';
        if (data.type === 'construction') {
            var c = data.calc;
            d += '<div>ارزش معاملاتی (M): ' + fmt(c.transaction_value) + '</div>';
            d += '<div>ضریب نهایی (N = M × 0.14): ' + fmt(c.final_coefficient) + '</div>';
            d += '<div>عوارض مسکونی (S = Q × N × O): ' + fmt(c.residential_levy) + '</div>';
            d += '<div>عوارض تراکم (T = N × مساحت × P): ' + fmt(c.density_levy) + '</div>';
            d += '<div>مبلغ نهایی = MAX(S, T)</div>';
        } else {
            (data.lines || []).forEach(function (l) {
                d += '<div>' + l.title + ' — ' + fmt(l.qty) + ' × ' + fmt(l.price) + ' = ' + fmt(l.amount) + '</div>';
            });
        }
        detailsBox.innerHTML = d;
        detailsBox.style.display = 'block';
    }

    function calculate(payload) {
        var fd = new FormData();
        fd.append('_token', document.querySelector('input[name="_token"]').value);
        Object.keys(payload).forEach(function (k) { fd.append(k, payload[k]); });
        fetch(calculateUrl, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(render)
            .catch(function () { resultBox.textContent = 'خطا در ارتباط با سرور.'; });
    }

    document.getElementById('calcConstruction').addEventListener('click', function () {
        calculate({
            type: 'construction',
            construction_area: document.getElementById('constructionArea').value,
            property_area: document.getElementById('propertyArea').value
        });
    });

    var calcWaste = document.getElementById('calcWaste');
    if (calcWaste) {
        calcWaste.addEventListener('click', function () {
            var payload = { type: 'waste' };
            var i = 0;
            document.querySelectorAll('.waste-row').forEach(function (row) {
                if (!row.querySelector('.waste-check').checked) { return; }
                payload['items[' + i + '][id]'] = row.dataset.id;
                payload['items[' + i + '][qty]'] = row.querySelector('.waste-qty').value || '1';
                i++;
            });
            if (i === 0) { resultBox.textContent = 'حداقل یک قلم پسماند را انتخاب کنید.'; return; }
            calculate(payload);
        });
    }

    // مقداردهی اولیهٔ تب بر اساس مقدار قدیمی فرم
    var oldType = @json(old('type', 'construction'));
    var initial = Array.prototype.find.call(tabs, function (t) { return t.dataset.tab === oldType; }) || tabs[0];
    activate(initial);
})();
</script>
@endsection

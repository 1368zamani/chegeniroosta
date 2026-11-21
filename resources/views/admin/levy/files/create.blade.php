@extends(View::exists('layouts.admin') ? 'layouts.admin' : 'layouts.tariff-fallback')
@section('title', 'ایجاد پرونده عوارض')
@section('content')
<div class="p-4 md:p-6" dir="rtl">
    @include('admin.levy._nav')

    <div class="mb-5">
        <h1 class="text-2xl font-bold text-slate-800">ایجاد پرونده مستقل عوارض</h1>
        <p class="text-sm text-slate-500 mt-1">پس از انتخاب روستا، ارقام واردشده توسط کارشناس بهصورت خودکار طبق فرمول اکسل محاسبه میشوند.</p>
    </div>

    <form method="POST" action="{{ route('admin.levy.files.store') }}" id="createForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- ستون اصلی --}}
            <div class="lg:col-span-2 space-y-5">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
                    <h2 class="font-bold text-slate-700 mb-4 border-r-4 border-slate-700 pr-3">اطلاعات متقاضی و دهیاری</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">روستا <span class="text-rose-600">*</span></label>
                            <select name="village_id" id="village_id" required class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                                <option value="">— انتخاب روستا —</option>
                                @foreach(['الف','ب','ج'] as $g)
                                    <optgroup label="گروه {{ $g }}">
                                        @foreach($villages as $v)
                                            @php $p = $params[$v->id] ?? null; @endphp
                                            @if(($p->group ?? 'الف') === $g)
                                                <option value="{{ $v->id }}"
                                                        data-tv="{{ $p->transaction_value ?? 39000 }}"
                                                        data-ccoef="{{ $p->construction_coefficient ?? 30 }}"
                                                        data-dcoef="{{ $p->density_coefficient ?? 30 }}"
                                                        data-source="{{ $p->density_area_source ?? 'Q' }}">{{ $v->name }}</option>
                                            @endif
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">سال جاری (شمسی) <span class="text-rose-600">*</span></label>
                            <select name="jalali_year" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                                @foreach($years as $y)<option value="{{ $y }}" {{ (string) $y === (string) now()->year ? 'selected' : '' }}>{{ \App\Support\Number::fa($y) }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">نام و نام خانوادگی متقاضی</label>
                            <input type="text" name="applicant_name" value="{{ old('applicant_name') }}" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">نام دهیاری</label>
                            <input type="text" name="dehyari_name" value="{{ old('dehyari_name') }}" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">شماره حساب دهیاری</label>
                            <select name="dehyari_account_no" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                                <option value="">— انتخاب حساب —</option>
                                @foreach($accounts as $a)
                                    <option value="{{ $a->account_no }}" {{ old('dehyari_account_no') === $a->account_no ? 'selected' : '' }} dir="ltr">{{ $a->account_no }} — {{ $a->label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">شماره شبا</label>
                            <input type="text" name="iban" value="{{ old('iban') }}" class="w-full border border-slate-300 rounded px-3 py-2 text-sm" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">شماره شناسه</label>
                            <input type="text" name="id_number" value="{{ old('id_number') }}" class="w-full border border-slate-300 rounded px-3 py-2 text-sm" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">گروه روستا</label>
                            <select name="village_group" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                                <option value="">— انتخاب —</option>
                                @foreach(['الف','ب','ج'] as $g)<option value="{{ $g }}" {{ old('village_group') === $g ? 'selected' : '' }}>{{ $g }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">وضعیت طبقه</label>
                            <input type="text" name="class_status" value="{{ old('class_status') }}" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
                    <h2 class="font-bold text-slate-700 mb-4 border-r-4 border-slate-700 pr-3">عوارض (جدول سمت چپ اکسل)</h2>
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600 text-xs"><tr><th class="p-2 text-right">عنوان عوارض</th><th class="p-2 text-right">مبلغ (ریال)</th></tr></thead>
                        <tbody>
                            @foreach(['عوارض حصار کشی','عوارض احداث غیرمسکونی','عوارض تراکم مجاز'] as $i => $title)
                                <tr class="border-b border-slate-100">
                                    <td class="p-2">{{ $title }}</td>
                                    <td class="p-2"><input type="text" name="lines[{{ $i }}]" value="{{ old('lines.'.$i, 0) }}" class="line-amount w-full border border-slate-300 rounded px-3 py-2 text-sm" dir="ltr" inputmode="numeric"></td>
                                </tr>
                            @endforeach
                            <tr class="bg-slate-50 font-bold">
                                <td class="p-2">جمع کل (H = SUM(G4:G6))</td>
                                <td class="p-2" dir="ltr" id="linesTotal">۰</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
                    <h2 class="font-bold text-slate-700 mb-4 border-r-4 border-slate-700 pr-3">محاسبات خودکار (جدول سمت راست اکسل)</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">مساحت ساخت (Q)</label>
                            <input type="text" name="construction_area" id="qArea" value="{{ old('construction_area') }}" class="w-full border border-slate-300 rounded px-3 py-2 text-sm calc-input" dir="ltr" inputmode="decimal" placeholder="۰">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">مساحت ملک (R)</label>
                            <input type="text" name="property_area" id="rArea" value="{{ old('property_area') }}" class="w-full border border-slate-300 rounded px-3 py-2 text-sm calc-input" dir="ltr" inputmode="decimal" placeholder="۰">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-600 mb-1">یادداشت</label>
                        <textarea name="notes" rows="2" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- پانل محاسبه زنده --}}
            <div>
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5 lg:sticky lg:top-4" id="calcPanel">
                    <h2 class="font-bold text-slate-700 mb-3 border-r-4 border-blue-700 pr-3">پیشنمایش محاسبه</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-slate-500">M — ارزش معاملاتی</span><b dir="ltr" id="cM">۰</b></div>
                        <div class="flex justify-between"><span class="text-slate-500">N = M × ۰.۱۴</span><b dir="ltr" id="cN">۰</b></div>
                        <div class="flex justify-between"><span class="text-slate-500">O — ضریب ساخت</span><b dir="ltr" id="cO">۰</b></div>
                        <div class="flex justify-between"><span class="text-slate-500">P — ضریب تراکم</span><b dir="ltr" id="cP">۰</b></div>
                        <hr class="border-slate-200">
                        <div class="flex justify-between"><span class="text-slate-500">S = Q × N × O</span><b dir="ltr" id="cS">۰</b></div>
                        <div class="flex justify-between"><span class="text-slate-500">T = N × مساحت × P</span><b dir="ltr" id="cT">۰</b></div>
                        <div class="text-xs text-slate-500" id="sourceNote">منبع مساحت تراکم: مساحت ساخت (Q)</div>
                        <div class="flex justify-between bg-slate-50 p-3 rounded mt-2"><span class="font-bold">U = MAX(S,T)</span><b class="text-blue-800 text-lg" dir="ltr" id="cU">۰</b></div>
                    </div>
                </div>
                <button type="submit" class="mt-4 w-full px-4 py-3 rounded bg-slate-800 text-white font-bold hover:bg-slate-900">ایجاد پرونده</button>
            </div>
        </div>
    </form>

    <script>
        (function () {
            const fmt = new Intl.NumberFormat('fa-IR');
            const village = document.getElementById('village_id');
            const q = document.getElementById('qArea');
            const r = document.getElementById('rArea');
            const $ = (id) => document.getElementById(id);
            const faToEn = (s) => String(s || '')
                .replace(/[۰-۹]/g, d => '0123456789'['۰۹۱۲۳۴۵۶۷۸۹'.indexOf(d)])
                .replace(/[٠-٩]/g, d => '0123456789'['٠١٢٣٤٥٦٧٨٩'.indexOf(d)]);
            const num = (v) => parseFloat(faToEn(v).replace(/[^0-9.\-]/g, '')) || 0;

            function recalc() {
                const opt = village.options[village.selectedIndex];
                const M = opt && opt.dataset.tv ? parseFloat(opt.dataset.tv) : 0;
                const O = opt && opt.dataset.ccoef ? parseFloat(opt.dataset.ccoef) : 0;
                const P = opt && opt.dataset.dcoef ? parseFloat(opt.dataset.dcoef) : 0;
                const source = opt && opt.dataset.source ? opt.dataset.source : 'Q';
                const N = Math.round(M * 0.14);
                const Q = num(q.value), R = num(r.value);
                const area = source === 'R' ? R : Q;
                const S = Math.round(Q * N * O);
                const T = Math.round(N * area * P);
                const U = Math.max(S, T);
                $('cM').textContent = fmt.format(M);
                $('cN').textContent = fmt.format(N);
                $('cO').textContent = fmt.format(O);
                $('cP').textContent = fmt.format(P);
                $('cS').textContent = fmt.format(S);
                $('cT').textContent = fmt.format(T);
                $('cU').textContent = fmt.format(U);
                $('sourceNote').textContent = 'منبع مساحت تراکم: ' + (source === 'R' ? 'مساحت ملک (R) — استثناء اکسل' : 'مساحت ساخت (Q)');
                let total = 0;
                document.querySelectorAll('.line-amount').forEach(function (el) { total += num(el.value); });
                $('linesTotal').textContent = fmt.format(total);
            }

            [village, q, r].forEach(function (el) { el.addEventListener('input', recalc); el.addEventListener('change', recalc); });
            document.querySelectorAll('.line-amount').forEach(function (el) { el.addEventListener('input', recalc); });
            recalc();
        })();
    </script>
</div>
@endsection

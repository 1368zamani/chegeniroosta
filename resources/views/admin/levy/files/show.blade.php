@extends(View::exists('layouts.admin') ? 'layouts.admin' : 'layouts.tariff-fallback')
@section('title', 'پرونده عوارض — ' . ($file->village?->name ?? ''))
@section('content')
<div class="p-4 md:p-6" dir="rtl" id="calcMeta"
     data-m="{{ $file->params?->transaction_value ?? 0 }}"
     data-o="{{ $file->params?->construction_coefficient ?? 0 }}"
     data-p="{{ $file->params?->density_coefficient ?? 0 }}"
     data-source="{{ $file->params?->density_area_source ?? 'Q' }}">
    @include('admin.levy._nav')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ $file->village?->name }} — عوارض احداث بنا</h1>
            <p class="text-sm text-slate-500 mt-1">
                سال {{ \App\Support\Number::fa($file->jalali_year) }} ·
                وضعیت:
                <span class="px-2 py-0.5 rounded text-xs {{ $file->status === 'complete' ? 'bg-emerald-100 text-emerald-800' : ($file->status === 'incomplete' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600') }}">
                    {{ $file->status === 'complete' ? 'تکمیلشده' : ($file->status === 'incomplete' ? 'ناقص' : 'پیشنویس') }}
                </span>
                · تکمیل: <span id="completionText">{{ \App\Support\Number::money($file->completion_percent) }}٪</span>
            </p>
        </div>
        <div class="flex flex-wrap gap-2 text-xs no-print">
            <a href="{{ route('admin.levy.reports.village', $file) }}" class="px-3 py-2 rounded bg-slate-100 text-slate-700 hover:bg-slate-200">گزارش چاپی</a>
            <a href="{{ route('admin.levy.export.file', $file) }}" class="px-3 py-2 rounded bg-blue-700 text-white hover:bg-blue-800">خروجی اکسل</a>
            <a href="{{ route('admin.levy.files.audit', $file) }}" class="px-3 py-2 rounded bg-slate-100 text-slate-700 hover:bg-slate-200">سوابق تغییرات</a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.levy.files.update', $file) }}" id="editForm">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="lg:col-span-2 space-y-5">

                {{-- اطلاعات متقاضی --}}
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
                    <h2 class="font-bold text-slate-700 mb-4 border-r-4 border-slate-700 pr-3">اطلاعات متقاضی و دهیاری</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">روستا</label>
                            <input type="text" value="{{ $file->village?->name }}" disabled class="w-full border border-slate-200 bg-slate-50 rounded px-3 py-2 text-sm">
                            <input type="hidden" name="village_id" value="{{ $file->village_id }}">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">سال جاری (شمسی)</label>
                            <input type="text" name="jalali_year" value="{{ old('jalali_year', $file->jalali_year) }}" class="w-full border border-slate-300 rounded px-3 py-2 text-sm" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">نام و نام خانوادگی متقاضی</label>
                            <input type="text" name="applicant_name" value="{{ old('applicant_name', $file->applicant_name) }}" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">نام دهیاری</label>
                            <input type="text" name="dehyari_name" value="{{ old('dehyari_name', $file->dehyari_name) }}" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">شماره حساب دهیاری</label>
                            <select name="dehyari_account_no" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                                <option value="">— انتخاب حساب —</option>
                                @foreach($accounts as $a)
                                    <option value="{{ $a->account_no }}" {{ old('dehyari_account_no', $file->dehyari_account_no) === $a->account_no ? 'selected' : '' }} dir="ltr">{{ $a->account_no }} — {{ $a->label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">شماره شبا</label>
                            <input type="text" name="iban" value="{{ old('iban', $file->iban) }}" class="w-full border border-slate-300 rounded px-3 py-2 text-sm" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">شماره شناسه</label>
                            <input type="text" name="id_number" value="{{ old('id_number', $file->id_number) }}" class="w-full border border-slate-300 rounded px-3 py-2 text-sm" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">گروه روستا</label>
                            <select name="village_group" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                                <option value="">— انتخاب —</option>
                                @foreach(['الف','ب','ج'] as $g)
                                    <option value="{{ $g }}" {{ old('village_group', $file->village_group) === $g ? 'selected' : '' }}>{{ $g }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">وضعیت طبقه</label>
                            <input type="text" name="class_status" value="{{ old('class_status', $file->class_status) }}" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
                        </div>
                    </div>
                </div>

                {{-- جدول چپ اکسل --}}
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
                    <h2 class="font-bold text-slate-700 mb-4 border-r-4 border-slate-700 pr-3">عوارض (جدول سمت چپ اکسل)</h2>
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600 text-xs"><tr><th class="p-2 text-right">عنوان عوارض</th><th class="p-2 text-right">مبلغ عوارض (ریال)</th></tr></thead>
                        <tbody>
                            @foreach($file->lines as $i => $line)
                                <tr class="border-b border-slate-100">
                                    <td class="p-2">{{ $line->title }}</td>
                                    <td class="p-2"><input type="text" name="lines[{{ $i }}]" value="{{ old('lines.'.$i, $line->amount) }}" class="line-amount w-full border border-slate-300 rounded px-3 py-2 text-sm" dir="ltr" inputmode="numeric"></td>
                                </tr>
                            @endforeach
                            <tr class="bg-slate-50 font-bold">
                                <td class="p-2">جمع نهایی (H = SUM(G4:G6))</td>
                                <td class="p-2 text-blue-800" dir="ltr" id="linesTotal">{{ \App\Support\Number::money($file->lines_total) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- جدول راست اکسل --}}
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
                    <h2 class="font-bold text-slate-700 mb-4 border-r-4 border-slate-700 pr-3">محاسبات خودکار (جدول سمت راست اکسل)</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">مساحت ساخت (Q) — ورودی کارشناس</label>
                            <input type="text" name="construction_area" id="qArea" value="{{ old('construction_area', $file->construction_area) }}" class="w-full border border-slate-300 rounded px-3 py-2 text-sm calc-input" dir="ltr" inputmode="decimal">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-600 mb-1">مساحت ملک (R) — ورودی کارشناس</label>
                            <input type="text" name="property_area" id="rArea" value="{{ old('property_area', $file->property_area) }}" class="w-full border border-slate-300 rounded px-3 py-2 text-sm calc-input" dir="ltr" inputmode="decimal">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-600 mb-1">یادداشت</label>
                        <textarea name="notes" rows="2" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">{{ old('notes', $file->notes) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- پانل محاسبه زنده --}}
            <div>
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5 lg:sticky lg:top-4">
                    <h2 class="font-bold text-slate-700 mb-3 border-r-4 border-blue-700 pr-3">نتایج محاسبه</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-slate-500">M — ارزش معاملاتی</span><b dir="ltr" id="cM">—</b></div>
                        <div class="flex justify-between"><span class="text-slate-500">N = M × ۰.۱۴</span><b dir="ltr" id="cN">—</b></div>
                        <div class="flex justify-between"><span class="text-slate-500">O — ضریب ساخت</span><b dir="ltr" id="cO">—</b></div>
                        <div class="flex justify-between"><span class="text-slate-500">P — ضریب تراکم</span><b dir="ltr" id="cP">—</b></div>
                        <hr class="border-slate-200">
                        <div class="flex justify-between"><span class="text-slate-500">S = Q × N × O</span><b dir="ltr" id="cS">—</b></div>
                        <div class="flex justify-between"><span class="text-slate-500">T = N × مساحت × P</span><b dir="ltr" id="cT">—</b></div>
                        <div class="text-xs text-slate-500" id="sourceNote"></div>
                        <div class="flex justify-between bg-slate-50 p-3 rounded mt-2"><span class="font-bold">U = MAX(S,T)</span><b class="text-blue-800 text-lg" dir="ltr" id="cU">—</b></div>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-3">هر عددی که وارد کنید، خروجیهای وابسته بلافاصله بازمحاسبه میشوند.</p>
                </div>
            </div>
        </div>

        {{-- نوار ذخیره --}}
        <div class="sticky bottom-0 no-print bg-white border border-slate-200 rounded-lg shadow-lg px-4 py-3 mt-6 flex flex-wrap items-center gap-3 z-10">
            <button type="submit" class="px-5 py-2 rounded bg-slate-800 text-white text-sm font-bold hover:bg-slate-900">ذخیره</button>
            <span id="saveStatus" class="text-xs text-slate-500">تغییری ثبت نشده است.</span>
            <span class="text-xs text-slate-400 mr-auto">ذخیره خودکار پس از ۱.۲ ثانیه توقف تایپ فعال میشود.</span>
            <a href="{{ route('admin.levy.files.index') }}" class="text-xs text-slate-600 hover:underline">بازگشت</a>
        </div>
    </form>

    <script>
        (function () {
            const fmt = new Intl.NumberFormat('fa-IR');
            const meta = document.getElementById('calcMeta');
            const form = document.getElementById('editForm');
            const $ = (id) => document.getElementById(id);
            const faToEn = (s) => String(s || '')
                .replace(/[۰-۹]/g, d => '0123456789'['۰۹۱۲۳۴۵۶۷۸۹'.indexOf(d)])
                .replace(/[٠-٩]/g, d => '0123456789'['٠١٢٣٤٥٦٧٨٩'.indexOf(d)]);
            const num = (v) => parseFloat(faToEn(v).replace(/[^0-9.\-]/g, '')) || 0;

            function recalc() {
                const M = num(meta.dataset.m), O = num(meta.dataset.o), P = num(meta.dataset.p);
                const source = meta.dataset.source || 'Q';
                const N = Math.round(M * 0.14);
                const Q = num($('qArea').value), R = num($('rArea').value);
                const area = source === 'R' ? R : Q;
                const S = Math.round(Q * N * O);
                const T = Math.round(N * area * P);
                $('cM').textContent = fmt.format(M);
                $('cN').textContent = fmt.format(N);
                $('cO').textContent = fmt.format(O);
                $('cP').textContent = fmt.format(P);
                $('cS').textContent = fmt.format(S);
                $('cT').textContent = fmt.format(T);
                $('cU').textContent = fmt.format(Math.max(S, T));
                $('sourceNote').textContent = 'منبع مساحت تراکم: ' + (source === 'R' ? 'مساحت ملک (R) — استثناء اکسل' : 'مساحت ساخت (Q)');
                let total = 0;
                form.querySelectorAll('.line-amount').forEach(function (el) { total += num(el.value); });
                $('linesTotal').textContent = fmt.format(total);
            }

            let timer = null;
            function scheduleAutosave() {
                const st = $('saveStatus');
                st.textContent = 'در حال تایپ…';
                clearTimeout(timer);
                timer = setTimeout(function () {
                    st.textContent = 'در حال ذخیره…';
                    const fd = new FormData(form);
                    fd.delete('_method');
                    fetch('{{ route('admin.levy.files.autosave', $file) }}', {
                        method: 'POST',
                        body: fd,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        credentials: 'same-origin'
                    }).then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
                      .then(function (res) {
                          if (res.ok && res.j.ok) {
                              st.textContent = 'ذخیره شد — ساعت ' + res.j.saved_at;
                              $('completionText').textContent = fmt.format(res.j.completion) + '٪';
                          } else {
                              st.textContent = 'خطا در ذخیره خودکار';
                          }
                      }).catch(function () { st.textContent = 'خطا در ارتباط با سرور'; });
                }, 1200);
            }

            form.querySelectorAll('input, select, textarea').forEach(function (el) {
                el.addEventListener('input', function () { recalc(); scheduleAutosave(); });
                el.addEventListener('change', function () { recalc(); scheduleAutosave(); });
            });
            recalc();
        })();
    </script>
</div>
@endsection

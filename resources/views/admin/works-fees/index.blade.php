@extends('layouts.tariff-fallback')

@section('title', 'مدیریت عوارض پسماند و ساخت‌وساز')

@php use App\Support\Number; @endphp

@section('content')
<div class="p-1 md:p-4" dir="rtl" id="wfApp">

{{-- ═══════════════ نوار بالا ═══════════════ --}}
<div class="flex items-start justify-between flex-wrap gap-3 mb-5">
    <div>
        <h1 class="text-2xl font-bold text-emerald-800 m-0">💰 مدیریت عوارض</h1>
        <p class="text-sm text-gray-500 mt-1 mb-0">
            عوارض <strong>پسماند</strong> و <strong>ساخت‌وساز</strong>
            — ویرایش مبلغ توسط کارشناس عمران
        </p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.tariffs.index') }}"
           class="px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">📄 تعرفه‌ها</a>
        <a href="{{ route('admin.tariff-invoices.index') }}"
           class="px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">🧾 فیش‌ها</a>
        <a href="{{ route('admin.dashboard') }}"
           class="px-3 py-2 text-sm rounded-lg bg-emerald-700 text-white hover:bg-emerald-800">← داشبورد</a>
    </div>
</div>

{{-- ═══════════════ پیام‌ها ═══════════════ --}}
@foreach(['success' => 'emerald', 'info' => 'blue', 'error' => 'red'] as $k => $c)
    @if(session($k))
        <div class="mb-4 p-3 rounded-lg text-sm" style="background:{{ $c==='emerald'?'#e8f5e9':($c==='blue'?'#e3f2fd':'#ffebee') }};color:{{ $c==='emerald'?'#1b5e20':($c==='blue'?'#0d47a1':'#b71c1c') }};border-right:4px solid {{ $c==='emerald'?'#2e7d32':($c==='blue'?'#1565c0':'#c62828') }};">
            {{ session($k) }}
        </div>
    @endif
@endforeach

@if(isset($errors) && $errors->any())
    <div class="mb-4 p-3 rounded-lg text-sm" style="background:#ffebee;color:#b71c1c;border-right:4px solid #c62828;">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
@endif

{{-- ═══════════════ کارت‌های آمار ═══════════════ --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
    @php
        $statCards = [
            ['کل اقلام', $stats['total'], '📋', '#2e7d32'],
            ['فعال', $stats['active'], '✅', '#2e7d32'],
            ['غیرفعال', $stats['inactive'], '⛔', '#c62828'],
            ['دارای مبلغ در تعرفه', $stats['with_items'], '💰', '#1565c0'],
        ];
    @endphp
    @foreach($statCards as [$label, $value, $icon, $color])
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
            <div class="text-xl mb-1">{{ $icon }}</div>
            <div class="text-2xl font-extrabold" style="color:{{ $color }};">{{ Number::fa(number_format($value)) }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ $label }}</div>
        </div>
    @endforeach
</div>

{{-- ═══════════════ تب‌ها ═══════════════ --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

    <div class="flex flex-wrap border-b-2 border-gray-100 bg-gray-50" id="wfTabs">
        @foreach($groups as $key => $g)
            <button type="button"
                    class="wf-tab px-5 py-3.5 text-sm font-bold border-b-[3px] transition-all"
                    data-tab="{{ $key }}"
                    style="border-color:transparent;color:#607d8b;">
                <span class="text-base">{{ $g['meta']['icon'] }}</span>
                {{ $g['meta']['title'] }}
                <span class="inline-block text-xs px-2 py-0.5 rounded-full mr-1"
                      style="background:{{ $g['meta']['soft'] }};color:{{ $g['meta']['color'] }};">
                    {{ Number::fa($g['count']) }}
                </span>
            </button>
        @endforeach
    </div>

    @foreach($groups as $key => $g)
        <div class="wf-panel" data-panel="{{ $key }}" style="display:none;">

            {{-- هدر پنل --}}
            <div class="flex items-center justify-between flex-wrap gap-2 p-4 border-b border-gray-100"
                 style="background:{{ $g['meta']['soft'] }};">
                <div>
                    <div class="font-bold" style="color:{{ $g['meta']['color'] }};">
                        {{ $g['meta']['icon'] }} {{ $g['meta']['title'] }}
                    </div>
                    <div class="text-xs text-gray-600 mt-0.5">{{ $g['meta']['desc'] }}</div>
                </div>
                @if($g['can_manage'])
                    <button type="button" class="wf-add-btn px-4 py-2 rounded-lg text-sm font-bold text-white"
                            style="background:{{ $g['meta']['color'] }};"
                            data-category="{{ $g['meta']['category'] }}"
                            data-subcategory="{{ $g['meta']['subcategory'] ?? '' }}"
                            data-group="{{ $key }}">
                        + افزودن قلم جدید
                    </button>
                @else
                    <span class="text-xs px-3 py-1.5 rounded-lg bg-white/70 text-gray-600">
                        👁 فقط مشاهده
                    </span>
                @endif
            </div>

            {{-- جدول --}}
            @if($g['rows']->isEmpty())
                <div class="p-10 text-center text-gray-400 text-sm">
                    هنوز قلمی در این دسته تعریف نشده است.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500">
                            <tr>
                                <th class="p-3 font-bold">#</th>
                                <th class="p-3 font-bold">نام عوارض</th>
                                <th class="p-3 font-bold whitespace-nowrap">واحد</th>
                                <th class="p-3 font-bold whitespace-nowrap">مبلغ پایه (ریال)</th>
                                <th class="p-3 font-bold whitespace-nowrap">تاریخ اعمال</th>
                                <th class="p-3 font-bold whitespace-nowrap">وضعیت</th>
                                <th class="p-3 font-bold whitespace-nowrap">آخرین ویرایش</th>
                                <th class="p-3 font-bold whitespace-nowrap">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($g['rows'] as $i => $row)
                            <tr class="border-t border-gray-100 hover:bg-gray-50/70 {{ $row->is_active ? '' : 'opacity-60' }}">
                                <td class="p-3 text-gray-400 font-mono">{{ Number::fa($i + 1) }}</td>

                                <td class="p-3">
                                    <div class="font-bold text-gray-800">{{ $row->name_fa }}</div>
                                    <div class="text-[11px] text-gray-400 font-mono mt-0.5">{{ $row->code }}</div>
                                    @if($row->description)
                                        <div class="text-[11.5px] text-gray-500 mt-1">{{ $row->description }}</div>
                                    @endif
                                </td>

                                <td class="p-3 text-gray-600 whitespace-nowrap text-xs">{{ $row->unitLabel() }}</td>

                                <td class="p-3 whitespace-nowrap">
                                    <span class="font-extrabold text-base" style="color:{{ $g['meta']['color'] }};">
                                        {{ Number::fa(number_format((int) $row->base_amount)) }}
                                    </span>
                                    @if($row->base_amount)
                                        <div class="text-[10.5px] text-gray-400">
                                            {{ Number::fa(number_format(intdiv((int) $row->base_amount, 10))) }} تومان
                                        </div>
                                    @else
                                        <div class="text-[10.5px] text-amber-600">تعیین نشده</div>
                                    @endif
                                </td>

                                <td class="p-3 whitespace-nowrap text-gray-600 text-xs">
                                    @if($row->effective_from)
                                        {{ Number::fa(\Morilog\Jalali\Jalalian::fromCarbon($row->effective_from)->format('Y/m/d')) }}
                                    @else
                                        —
                                    @endif
                                </td>

                                <td class="p-3 whitespace-nowrap">
                                    @if($row->is_active)
                                        <span class="text-[11px] px-2.5 py-1 rounded-full" style="background:#e8f5e9;color:#1b5e20;">✅ فعال</span>
                                    @else
                                        <span class="text-[11px] px-2.5 py-1 rounded-full" style="background:#ffebee;color:#b71c1c;">⛔ غیرفعال</span>
                                    @endif
                                </td>

                                <td class="p-3 whitespace-nowrap text-[11.5px] text-gray-500">
                                    @if($row->lastEditor)
                                        {{ $row->lastEditor->name }}
                                    @endif
                                    @if($row->updated_at)
                                        <div class="text-[10.5px] text-gray-400">
                                            {{ Number::fa(\Morilog\Jalali\Jalalian::fromCarbon($row->updated_at)->format('Y/m/d H:i')) }}
                                        </div>
                                    @endif
                                </td>

                                <td class="p-3 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        @if($g['can_manage'])
                                            <button type="button"
                                                    class="wf-amount-btn text-[12px] px-2.5 py-1.5 rounded-lg font-bold"
                                                    style="background:#fff3e0;color:#e65100;"
                                                    title="تغییر مبلغ"
                                                    data-id="{{ $row->id }}"
                                                    data-name="{{ $row->name_fa }}"
                                                    data-unit="{{ $row->unitLabel() }}"
                                                    data-amount="{{ (int) $row->base_amount }}">💰 مبلغ</button>

                                            <button type="button"
                                                    class="wf-edit-btn text-[12px] px-2.5 py-1.5 rounded-lg font-bold"
                                                    style="background:#e3f2fd;color:#1565c0;"
                                                    title="ویرایش کامل"
                                                    data-id="{{ $row->id }}"
                                                    data-name="{{ $row->name_fa }}"
                                                    data-category="{{ $row->category }}"
                                                    data-subcategory="{{ $row->subcategory ?? '' }}"
                                                    data-unit="{{ $row->unit }}"
                                                    data-amount="{{ (int) $row->base_amount }}"
                                                    data-description="{{ $row->description }}"
                                                    data-effective="{{ $row->effective_from ? \Morilog\Jalali\Jalalian::fromCarbon($row->effective_from)->format('Y/m/d') : '' }}"
                                                    data-active="{{ $row->is_active ? 1 : 0 }}">✏️ ویرایش</button>

                                            <form method="POST" action="{{ route('admin.works-fees.toggle', $row) }}" class="inline"
                                                  onsubmit="return confirm('وضعیت این قلم تغییر کند؟');">
                                                @csrf
                                                <button type="submit" class="text-[12px] px-2.5 py-1.5 rounded-lg font-bold"
                                                        style="background:{{ $row->is_active ? '#ffebee' : '#e8f5e9' }};color:{{ $row->is_active ? '#b71c1c' : '#1b5e20' }};"
                                                        title="{{ $row->is_active ? 'غیرفعال کردن' : 'فعال کردن' }}">
                                                    {{ $row->is_active ? '⛔' : '✅' }}
                                                </button>
                                            </form>
                                        @endif

                                        <button type="button"
                                                class="wf-logs-btn text-[12px] px-2.5 py-1.5 rounded-lg font-bold bg-gray-100 text-gray-600"
                                                title="تاریخچه‌ی تغییرات"
                                                data-id="{{ $row->id }}"
                                                data-name="{{ $row->name_fa }}">📜</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endforeach
</div>

<div class="mt-4 text-[11.5px] text-gray-400 leading-relaxed">
    ⚠️ این اقلام <strong>کاتالوگ پیشنهادی</strong> هستند. مبلغ واقعی هر روستا بر اساس مصوبه‌ی شورای اسلامی
    در بخش «تعرفه‌ها» تعیین می‌شود. تغییرات مبلغ اینجا در تاریخچه ثبت می‌شود و قابل پیگیری است.
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- مودال تغییر مبلغ                                      --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div id="amountModal" class="wf-modal" style="display:none;">
    <div class="wf-modal-box" style="max-width:480px;">
        <div class="p-5 border-b border-gray-100" style="background:#fff3e0;">
            <div class="font-bold text-base" style="color:#e65100;">💰 تغییر مبلغ عوارض</div>
            <div class="text-xs text-gray-600 mt-1" id="amItemName">—</div>
        </div>

        <form method="POST" id="amountForm" class="p-5">
            @csrf
            @method('PATCH')

            <div class="mb-4 p-3 rounded-lg" style="background:#f5f7fa;">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">مبلغ فعلی:</span>
                    <strong class="font-mono text-gray-800" id="amCurrent">—</strong>
                </div>
            </div>

            <label class="block mb-1 text-sm font-bold text-gray-700">مبلغ جدید (ریال) *</label>
            <input type="text" name="base_amount" id="amInput" required inputmode="numeric" dir="ltr"
                   class="w-full border-2 border-gray-300 rounded-lg p-2.5 mb-1 font-mono text-left focus:border-emerald-500 focus:outline-none"
                   placeholder="950000" autocomplete="off">
            <div class="text-xs mb-4 min-h-[18px]" id="amDiff"></div>

            <label class="block mb-1 text-sm font-bold text-gray-700">دلیل تغییر *</label>
            <input type="text" name="reason" required maxlength="500"
                   class="w-full border-2 border-gray-300 rounded-lg p-2.5 mb-4 focus:border-emerald-500 focus:outline-none"
                   placeholder="مثلاً: مصوبه‌ی جدید شورای اسلامی — جلسه ۱۲">

            <div class="flex gap-2 justify-end">
                <button type="button" class="wf-close px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-bold">انصراف</button>
                <button type="submit" class="px-5 py-2 rounded-lg text-white text-sm font-bold" style="background:#e65100;">
                    ✅ تأیید و ذخیره
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- مودال ویرایش / افزودن                                --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div id="editModal" class="wf-modal" style="display:none;">
    <div class="wf-modal-box" style="max-width:620px;">
        <div class="p-5 border-b border-gray-100" style="background:#e3f2fd;">
            <div class="font-bold text-base" style="color:#0d47a1;" id="emTitle">✏️ ویرایش عوارض</div>
            <div class="text-xs text-gray-600 mt-1">تغییرات در تاریخچه ثبت می‌شود</div>
        </div>

        <form method="POST" id="editForm" class="p-5">
            @csrf
            <input type="hidden" name="_method" id="emMethod" value="PUT">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block mb-1 text-sm font-bold text-gray-700">نام عوارض *</label>
                    <input type="text" name="name_fa" id="emName" required maxlength="255"
                           class="w-full border-2 border-gray-300 rounded-lg p-2.5 focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block mb-1 text-sm font-bold text-gray-700">دسته *</label>
                    <select name="category" id="emCategory" required
                            class="w-full border-2 border-gray-300 rounded-lg p-2.5 focus:border-emerald-500 focus:outline-none">
                        <option value="waste">🗑 عوارض پسماند</option>
                        <option value="construction">🏗 عوارض ساخت‌وساز</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-bold text-gray-700">زیرگروه</label>
                    <select name="subcategory" id="emSubcategory"
                            class="w-full border-2 border-gray-300 rounded-lg p-2.5 focus:border-emerald-500 focus:outline-none">
                        <option value="">—</option>
                        <option value="new_build">ساخت‌وساز جدید</option>
                        <option value="renovation">ساخت‌وساز تعمیری</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-bold text-gray-700">واحد محاسبه *</label>
                    <select name="unit" id="emUnit" required
                            class="w-full border-2 border-gray-300 rounded-lg p-2.5 focus:border-emerald-500 focus:outline-none">
                        @foreach($units as $u)
                            <option value="{{ $u }}">{{ $u }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-bold text-gray-700">مبلغ پایه (ریال) *</label>
                    <input type="text" name="base_amount" id="emAmount" required inputmode="numeric" dir="ltr"
                           class="w-full border-2 border-gray-300 rounded-lg p-2.5 font-mono text-left focus:border-emerald-500 focus:outline-none">
                </div>

                <div class="md:col-span-2">
                    <label class="block mb-1 text-sm font-bold text-gray-700">توضیح کوتاه (برای شهروند)</label>
                    <input type="text" name="description" id="emDescription" maxlength="500"
                           class="w-full border-2 border-gray-300 rounded-lg p-2.5 focus:border-emerald-500 focus:outline-none"
                           placeholder="مثلاً: درصد بنای احداثی، طبق پروانه‌ی ساختمانی">
                </div>

                <div>
                    <label class="block mb-1 text-sm font-bold text-gray-700">تاریخ اعمال (شمسی) *</label>
                    <input type="text" name="effective_from" id="emEffective" required dir="ltr" inputmode="numeric"
                           maxlength="10" placeholder="1405/01/01"
                           class="w-full border-2 border-gray-300 rounded-lg p-2.5 font-mono text-left focus:border-emerald-500 focus:outline-none">
                </div>

                <div class="flex items-end pb-1">
                    <label class="flex items-center gap-2 cursor-pointer text-sm">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" id="emActive" class="w-5 h-5">
                        <span class="font-bold text-gray-700">فعال (نمایش به شهروند)</span>
                    </label>
                </div>

                <div class="md:col-span-2">
                    <label class="block mb-1 text-sm font-bold text-gray-700">دلیل تغییر (اختیاری)</label>
                    <input type="text" name="reason" maxlength="500"
                           class="w-full border-2 border-gray-300 rounded-lg p-2.5 focus:border-emerald-500 focus:outline-none"
                           placeholder="برای ثبت در تاریخچه">
                </div>
            </div>

            <div class="flex gap-2 justify-end mt-5">
                <button type="button" class="wf-close px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-bold">انصراف</button>
                <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-700 text-white text-sm font-bold hover:bg-emerald-800">
                    💾 ذخیره
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- مودال تاریخچه                                        --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div id="logsModal" class="wf-modal" style="display:none;">
    <div class="wf-modal-box" style="max-width:620px;">
        <div class="p-5 border-b border-gray-100" style="background:#eceff1;">
            <div class="font-bold text-base text-gray-700">📜 تاریخچه‌ی تغییرات</div>
            <div class="text-xs text-gray-600 mt-1" id="lmTitle">—</div>
        </div>
        <div class="p-4 max-h-[60vh] overflow-y-auto" id="lmBody">
            <div class="text-center text-gray-400 py-6 text-sm">در حال بارگذاری…</div>
        </div>
        <div class="p-4 border-t border-gray-100 flex justify-end">
            <button type="button" class="wf-close px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-bold">بستن</button>
        </div>
    </div>
</div>

<style>
    .wf-modal {
        position: fixed; inset: 0; z-index: 9000;
        background: rgba(15, 23, 42, .55);
        display: flex; align-items: center; justify-content: center;
        padding: 16px;
        backdrop-filter: blur(2px);
    }
    .wf-modal-box {
        background: #fff; border-radius: 16px; width: 100%;
        max-height: 92vh; overflow-y: auto;
        box-shadow: 0 24px 60px rgba(0,0,0,.28);
        animation: wfPop .18s ease-out;
    }
    @keyframes wfPop {
        from { transform: scale(.96); opacity: 0; }
        to   { transform: scale(1); opacity: 1; }
    }
    .wf-tab:hover { background: #f5f7fa; }
    .wf-tab.wf-active { background: #fff; }
    .wf-log-row {
        display: flex; gap: 10px; padding: 10px 0;
        border-bottom: 1px dashed #e6eaec; font-size: 12.5px;
    }
    .wf-log-row:last-child { border-bottom: none; }
</style>

<script>
(function () {
    'use strict';

    // ═══════════ ابزار اعداد فارسی ═══════════
    const FA = '۰۱۲۳۴۵۶۷۸۹';
    const AR = '٠١٢٣٤٥٦٧٨٩';

    function faNum(n) {
        const v = Number(n) || 0;
        return v.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '٬').replace(/\d/g, d => FA[d]);
    }

    function parseNum(v) {
        return Number(
            String(v == null ? '' : v)
                .replace(/[۰-۹]/g, d => FA.indexOf(d))
                .replace(/[٠-٩]/g, d => AR.indexOf(d))
                .replace(/[^\d]/g, '')
        ) || 0;
    }

    function normalizeDate(raw) {
        const digits = String(raw || '')
            .replace(/[۰-۹]/g, d => FA.indexOf(d))
            .replace(/[٠-٩]/g, d => AR.indexOf(d))
            .replace(/[^\d]/g, '').slice(0, 8);
        if (digits.length <= 4) return digits;
        if (digits.length <= 6) return digits.slice(0, 4) + '/' + digits.slice(4);
        return digits.slice(0, 4) + '/' + digits.slice(4, 6) + '/' + digits.slice(6, 8);
    }

    // ═══════════ تب‌ها ═══════════
    const tabs = document.querySelectorAll('.wf-tab');
    const panels = document.querySelectorAll('.wf-panel');

    function activateTab(key) {
        tabs.forEach(t => {
            const on = t.dataset.tab === key;
            t.classList.toggle('wf-active', on);
            t.style.borderColor = on ? '#2e7d32' : 'transparent';
            t.style.color = on ? '#1b5e20' : '#607d8b';
        });
        panels.forEach(p => {
            p.style.display = p.dataset.panel === key ? '' : 'none';
        });
    }

    tabs.forEach(t => t.addEventListener('click', () => activateTab(t.dataset.tab)));

    // تب از URL
    const urlTab = new URLSearchParams(location.search).get('tab');
    const validTab = ['waste', 'new_build', 'renovation'].includes(urlTab) ? urlTab : null;
    activateTab(validTab || (tabs[0] ? tabs[0].dataset.tab : 'waste'));

    // ═══════════ مودال‌ها ═══════════
    function openModal(id) {
        const m = document.getElementById(id);
        if (m) m.style.display = 'flex';
    }
    function closeModals() {
        document.querySelectorAll('.wf-modal').forEach(m => m.style.display = 'none');
    }

    document.querySelectorAll('.wf-close').forEach(b => b.addEventListener('click', closeModals));
    document.querySelectorAll('.wf-modal').forEach(m => {
        m.addEventListener('click', e => { if (e.target === m) closeModals(); });
    });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModals(); });

    // ═══════════ مودال تغییر مبلغ ═══════════
    const amountForm = document.getElementById('amountForm');
    const amInput = document.getElementById('amInput');
    let amCurrentValue = 0;

    document.querySelectorAll('.wf-amount-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            amCurrentValue = parseInt(btn.dataset.amount || '0', 10) || 0;

            document.getElementById('amItemName').textContent =
                btn.dataset.name + '  ·  ' + btn.dataset.unit;
            document.getElementById('amCurrent').textContent = faNum(amCurrentValue) + ' ریال';

            amountForm.action = '/admin/works-fees/items/' + id + '/amount';
            amInput.value = amCurrentValue || '';
            updateDiff();
            openModal('amountModal');
            setTimeout(() => { amInput.focus(); amInput.select(); }, 100);
        });
    });

    function updateDiff() {
        const diffEl = document.getElementById('amDiff');
        const val = parseNum(amInput.value);

        if (!val) {
            diffEl.innerHTML = '<span style="color:#c62828;">مبلغ نمی‌تواند خالی یا صفر باشد.</span>';
            return;
        }
        if (!amCurrentValue) {
            diffEl.innerHTML = '<span style="color:#607d8b;">مبلغ فعلی تعیین نشده بود.</span>';
            return;
        }
        if (val === amCurrentValue) {
            diffEl.innerHTML = '<span style="color:#607d8b;">مبلغ تغییری نکرده است.</span>';
            return;
        }

        const d = val - amCurrentValue;
        const pct = (d / amCurrentValue) * 100;
        const sign = d > 0 ? '+' : '−';
        const color = d > 0 ? '#e65100' : '#2e7d32';

        diffEl.innerHTML = '<span style="color:' + color + ';font-weight:bold;">'
            + sign + faNum(Math.abs(d)) + ' ریال  (' + sign
            + faNum(Math.abs(pct).toFixed(1)) + '٪)</span>';
    }

    amInput.addEventListener('input', () => {
        const digits = amInput.value.replace(/[^\d۰-۹٠-٩]/g, '');
        amInput.value = parseNum(digits) || '';
        updateDiff();
    });

    // فرم مبلغ: تأیید نهایی
    amountForm.addEventListener('submit', e => {
        const val = parseNum(amInput.value);
        if (val < 1) {
            e.preventDefault();
            alert('⚠️ مبلغ باید بزرگ‌تر از صفر باشد.');
            return;
        }
        const ok = confirm(
            '⚠️ تأیید تغییر مبلغ\n\n' +
            'مبلغ فعلی: ' + faNum(amCurrentValue) + ' ریال\n' +
            'مبلغ جدید: ' + faNum(val) + ' ریال\n\n' +
            'این تغییر در تاریخچه ثبت می‌شود. ادامه می‌دهید؟'
        );
        if (!ok) e.preventDefault();
    });

    // ═══════════ مودال ویرایش ═══════════
    const editForm = document.getElementById('editForm');

    document.querySelectorAll('.wf-edit-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;

            document.getElementById('emTitle').textContent = '✏️ ویرایش: ' + btn.dataset.name;
            document.getElementById('emMethod').value = 'PUT';
            editForm.action = '/admin/works-fees/items/' + id;

            document.getElementById('emName').value = btn.dataset.name || '';
            document.getElementById('emCategory').value = btn.dataset.category || 'waste';
            document.getElementById('emSubcategory').value = btn.dataset.subcategory || '';
            document.getElementById('emAmount').value = btn.dataset.amount || '';
            document.getElementById('emDescription').value = btn.dataset.description || '';
            document.getElementById('emEffective').value = btn.dataset.effective || '';
            document.getElementById('emActive').checked = btn.dataset.active === '1';

            const unitSel = document.getElementById('emUnit');
            const unit = btn.dataset.unit || '';
            if (unit && !Array.from(unitSel.options).some(o => o.value === unit)) {
                const opt = document.createElement('option');
                opt.value = unit; opt.textContent = unit;
                unitSel.appendChild(opt);
            }
            unitSel.value = unit || unitSel.options[0].value;

            syncSubcategory();
            openModal('editModal');
        });
    });

    // ═══════════ افزودن قلم ═══════════
    document.querySelectorAll('.wf-add-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('emTitle').textContent = '➕ افزودن قلم جدید';
            document.getElementById('emMethod').value = 'POST';
            editForm.action = '/admin/works-fees';

            editForm.reset();
            document.getElementById('emCategory').value = btn.dataset.category || 'waste';
            document.getElementById('emSubcategory').value = btn.dataset.subcategory || '';
            document.getElementById('emActive').checked = true;

            const today = btn.dataset.today || '';
            document.getElementById('emEffective').value = today;

            syncSubcategory();
            openModal('editModal');
        });
    });

    // زیرگروه فقط برای ساخت‌وساز
    function syncSubcategory() {
        const cat = document.getElementById('emCategory').value;
        const sub = document.getElementById('emSubcategory');
        sub.disabled = cat === 'waste';
        if (cat === 'waste') sub.value = '';
    }
    document.getElementById('emCategory').addEventListener('change', syncSubcategory);

    // تاریخ اعمال — تبدیل خودکار
    const emEff = document.getElementById('emEffective');
    emEff.addEventListener('input', () => { emEff.value = normalizeDate(emEff.value); });
    emEff.addEventListener('blur', () => { emEff.value = normalizeDate(emEff.value); });

    // مبلغ — فقط عدد
    const emAmount = document.getElementById('emAmount');
    emAmount.addEventListener('input', () => {
        emAmount.value = emAmount.value.replace(/[^\d۰-۹٠-٩]/g, '');
        const n = parseNum(emAmount.value);
        emAmount.value = n || '';
    });

    // فرم ویرایش: اعتبارسنجی سمت کلاینت
    editForm.addEventListener('submit', e => {
        const amount = parseNum(emAmount.value);
        if (amount < 1) {
            e.preventDefault();
            alert('⚠️ مبلغ پایه نمی‌تواند خالی، صفر یا منفی باشد.');
            emAmount.focus();
            return;
        }
        const cat = document.getElementById('emCategory').value;
        const sub = document.getElementById('emSubcategory').value;
        if (cat === 'construction' && !sub) {
            e.preventDefault();
            alert('⚠️ برای عوارض ساخت‌وساز، زیرگروه (جدید یا تعمیری) را انتخاب کنید.');
            return;
        }
    });

    // ═══════════ مودال تاریخچه ═══════════
    document.querySelectorAll('.wf-logs-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            document.getElementById('lmTitle').textContent = btn.dataset.name;
            document.getElementById('lmBody').innerHTML =
                '<div class="text-center text-gray-400 py-6 text-sm">در حال بارگذاری…</div>';
            openModal('logsModal');

            try {
                const res = await fetch('/admin/works-fees/items/' + id + '/logs', {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();

                if (!data.logs || !data.logs.length) {
                    document.getElementById('lmBody').innerHTML =
                        '<div class="text-center text-gray-400 py-6 text-sm">هنوز تغییری ثبت نشده است.</div>';
                    return;
                }

                document.getElementById('lmBody').innerHTML = data.logs.map(l =>
                    '<div class="wf-log-row">' +
                        '<div style="flex:0 0 auto;width:8px;border-radius:4px;background:' + l.color + ';"></div>' +
                        '<div style="flex:1;">' +
                            '<div class="flex justify-between flex-wrap gap-1">' +
                                '<strong style="color:' + l.color + ';">' + l.action + '</strong>' +
                                '<span class="text-gray-400" style="font-size:11px;">' + l.date_jalali + '</span>' +
                            '</div>' +
                            (l.amount ? '<div class="font-mono mt-0.5" style="font-size:12px;">' + l.amount + '</div>' : '') +
                            (l.reason ? '<div class="text-gray-500 mt-0.5" style="font-size:11.5px;">📝 ' + l.reason + '</div>' : '') +
                            '<div class="text-gray-400 mt-0.5" style="font-size:11px;">👤 ' + l.user + '</div>' +
                        '</div>' +
                    '</div>'
                ).join('');
            } catch (err) {
                document.getElementById('lmBody').innerHTML =
                    '<div class="text-center py-6 text-sm" style="color:#c62828;">خطا در دریافت تاریخچه.</div>';
            }
        });
    });

    // داده‌ی تاریخ امروز برای فرم افزودن
    document.querySelectorAll('.wf-add-btn').forEach(b => {
        @php
            $todayJalali = \Morilog\Jalali\Jalalian::now()->format('Y/m/d');
        @endphp
        b.dataset.today = '{{ $todayJalali }}';
    });
})();
</script>
</div>
@endsection

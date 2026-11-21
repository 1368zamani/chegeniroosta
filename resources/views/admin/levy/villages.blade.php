@extends(View::exists('layouts.admin') ? 'layouts.admin' : 'layouts.tariff-fallback')
@section('title', 'ضرایب مرجع روستاها')
@section('content')
<div class="p-4 md:p-6" dir="rtl">
    @include('admin.levy._nav')

    <div class="mb-5">
        <h1 class="text-2xl font-bold text-slate-800">ضرایب مرجع روستاها</h1>
        <p class="text-sm text-slate-500 mt-1">
            فرمول اکسل برای <b>تمام روستاهای موجود در سامانه</b> اعمال میشود. ضرایب زیر مقدار اولیهاند و کارشناس عمرانی میتواند آنها را ویرایش کند؛
            با هر ویرایش، محاسبات تمام پروندههای همان روستا بازمحاسبه میشوند.
        </p>
        <div class="text-xs text-slate-500 mt-2 bg-slate-50 border border-slate-200 rounded p-3">
            تنظیمات گروه: الف = (ضریب ساخت ۳۰، ضریب تراکم ۳۰) · ب = (۴۰، ۳۵) · ج = (۵۰، ۴۰) · ضریب نهایی N = ارزش معاملاتی × ۰.۱۴
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 mb-4 p-4 no-print">
        <label class="block text-xs text-slate-600 mb-1" for="villageSearch">جستجو بر اساس نام روستا</label>
        <input id="villageSearch" type="search" placeholder="نام روستا را وارد کنید…"
               class="w-full md:w-96 border border-slate-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-400">
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm text-right min-w-[1000px]">
            <thead class="bg-slate-50 text-slate-600 text-xs">
                <tr>
                    <th class="p-3">ردیف</th>
                    <th class="p-3">نام روستا</th>
                    <th class="p-3">گروه</th>
                    <th class="p-3">ارزش معاملاتی (M)</th>
                    <th class="p-3">ضریب نهایی (N)</th>
                    <th class="p-3">ضریب ساخت (O)</th>
                    <th class="p-3">ضریب تراکم (P)</th>
                    <th class="p-3">منبع مساحت تراکم</th>
                    <th class="p-3">منبع عدد</th>
                    <th class="p-3 no-print">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100" id="paramsRows">
                @foreach($params as $i => $p)
                    @php $formId = 'param-form-' . $p->id; @endphp
                    <tr data-name="{{ $p->village?->name ?? '' }}">
                        {{-- فرم داخل سلول قرار میگیرد تا توسط تجزیهکنندهٔ HTML حذف نشود؛
                             سایر فیلدها و دکمه با ویژگی form به آن متصل میشوند. --}}
                        <td class="p-3 text-slate-500" dir="ltr">
                            <form id="{{ $formId }}" method="POST" action="{{ route('admin.levy.villages.update', $p) }}" class="hidden">
                                @csrf @method('PUT')
                            </form>
                            {{ \App\Support\Number::money($i + 1) }}
                        </td>
                        <td class="p-3 font-semibold">{{ $p->village?->name }}</td>
                        <td class="p-3">
                            <select name="group" form="{{ $formId }}" class="border border-slate-300 rounded px-2 py-1 text-xs">
                                @foreach(['الف','ب','ج'] as $g)
                                    <option value="{{ $g }}" {{ $p->group === $g ? 'selected' : '' }}>{{ $g }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-3"><input type="text" name="transaction_value" form="{{ $formId }}" value="{{ $p->transaction_value }}" class="w-24 border border-slate-300 rounded px-2 py-1 text-xs" dir="ltr"></td>
                        <td class="p-3 font-bold text-blue-800" dir="ltr">{{ \App\Support\Number::money($p->final_coefficient) }}</td>
                        <td class="p-3"><input type="text" name="construction_coefficient" form="{{ $formId }}" value="{{ $p->construction_coefficient }}" class="w-20 border border-slate-300 rounded px-2 py-1 text-xs" dir="ltr"></td>
                        <td class="p-3"><input type="text" name="density_coefficient" form="{{ $formId }}" value="{{ $p->density_coefficient }}" class="w-20 border border-slate-300 rounded px-2 py-1 text-xs" dir="ltr"></td>
                        <td class="p-3">
                            <select name="density_area_source" form="{{ $formId }}" class="border border-slate-300 rounded px-2 py-1 text-xs">
                                <option value="Q" {{ $p->density_area_source === 'Q' ? 'selected' : '' }}>مساحت ساخت (Q)</option>
                                <option value="R" {{ $p->density_area_source === 'R' ? 'selected' : '' }}>مساحت ملک (R)</option>
                            </select>
                            @if($p->density_area_source === 'R')
                                <div class="text-[10px] text-amber-700 mt-1" title="در اکسل اصلی، سلول T64 (امیرآباد) از مساحت ملک R بجای مساحت ساخت Q استفاده میکند.">استثناء اکسل: مساحت ملک</div>
                            @endif
                        </td>
                        <td class="p-3 text-[11px] text-slate-500">{{ $p->source_note }}</td>
                        <td class="p-3 no-print"><button type="submit" form="{{ $formId }}" class="px-3 py-1 rounded bg-slate-800 text-white text-xs hover:bg-slate-900">ذخیره</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($params->isEmpty())
            <div class="p-6 text-center text-sm text-slate-500">هیچ روستایی در سامانه ثبت نشده است.</div>
        @endif
    </div>

    <script>
        (function () {
            const input = document.getElementById('villageSearch');
            if (!input) return;
            input.addEventListener('input', function () {
                const q = this.value.trim();
                document.querySelectorAll('#paramsRows tr').forEach(function (tr) {
                    const name = tr.getAttribute('data-name') || '';
                    tr.style.display = name.indexOf(q) !== -1 ? '' : 'none';
                });
            });
        })();
    </script>
</div>
@endsection

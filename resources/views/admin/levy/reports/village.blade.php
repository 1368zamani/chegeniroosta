@extends(View::exists('layouts.admin') ? 'layouts.admin' : 'layouts.tariff-fallback')
@section('title', 'گزارش عوارض — ' . ($file->village?->name ?? ''))
@section('content')
<div class="p-4 md:p-6" dir="rtl">
    <div class="no-print mb-4 flex flex-wrap gap-2 items-center">
        <a href="{{ route('admin.levy.files.show', $file) }}" class="px-3 py-2 rounded bg-slate-100 text-slate-700 text-sm hover:bg-slate-200">← بازگشت به پرونده</a>
        <button onclick="window.print()" class="px-4 py-2 rounded bg-slate-800 text-white text-sm hover:bg-slate-900 no-print">چاپ گزارش</button>
        <a href="{{ route('admin.levy.export.file', $file) }}" class="px-4 py-2 rounded bg-blue-700 text-white text-sm hover:bg-blue-800 no-print">خروجی اکسل</a>
    </div>

    <div class="bg-white border border-slate-300 rounded-lg p-6">
        <h1 class="text-xl font-bold text-center border-b-2 border-slate-800 pb-3 mb-5">عوارض احداث بنا در محدوده قانونی روستاها</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-6">
            <div><span class="text-slate-500">روستا:</span> <b>{{ $file->village?->name }}</b></div>
            <div><span class="text-slate-500">سال:</span> <b dir="ltr">{{ \App\Support\Number::fa($file->jalali_year) }}</b></div>
            <div><span class="text-slate-500">نام و نام خانوادگی متقاضی:</span> <b>{{ $file->applicant_name ?: '—' }}</b></div>
            <div><span class="text-slate-500">نام دهیاری و شماره حساب:</span> <b>{{ $file->dehyari_name }} {{ $file->dehyari_account_no }}</b></div>
            <div><span class="text-slate-500">گروه روستا:</span> <b>{{ $file->village_group ?: ($file->params?->group ?? '—') }}</b></div>
            <div><span class="text-slate-500">وضعیت طبقه:</span> <b>{{ $file->class_status ?: '—' }}</b></div>
            <div><span class="text-slate-500">شماره شبا:</span> <b dir="ltr">{{ $file->iban ?: '—' }}</b></div>
            <div><span class="text-slate-500">شماره شناسه:</span> <b dir="ltr">{{ $file->id_number ?: '—' }}</b></div>
        </div>

        {{-- جدول چپ اکسل --}}
        <h2 class="font-bold text-slate-700 mb-2">عوارض (جدول سمت چپ)</h2>
        <table class="w-full text-sm border border-slate-300 mb-6">
            <thead class="bg-slate-100"><tr><th class="p-2 border border-slate-300 text-right">ردیف</th><th class="p-2 border border-slate-300 text-right">عنوان عوارض</th><th class="p-2 border border-slate-300 text-right">مبلغ عوارض (ریال)</th></tr></thead>
            <tbody>
                @foreach($file->lines as $i => $line)
                    <tr><td class="p-2 border border-slate-300" dir="ltr">{{ \App\Support\Number::money($i + 1) }}</td><td class="p-2 border border-slate-300">{{ $line->title }}</td><td class="p-2 border border-slate-300" dir="ltr">{{ \App\Support\Number::money($line->amount) }}</td></tr>
                @endforeach
                <tr class="bg-slate-50 font-bold"><td class="p-2 border border-slate-300" colspan="2">مبلغ نهایی عوارض احداث بنا (ریال)</td><td class="p-2 border border-slate-300" dir="ltr">{{ \App\Support\Number::money($file->lines_total) }}</td></tr>
            </tbody>
        </table>

        {{-- جدول راست اکسل --}}
        <h2 class="font-bold text-slate-700 mb-2">محاسبات خودکار (جدول سمت راست)</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm border border-slate-300 min-w-[700px]">
                <thead class="bg-slate-100 text-xs">
                    <tr>
                        @foreach(['ردیف','نام روستا','گروه','ارزش معاملاتی (M)','ضریب نهایی (N)','ضریب ساخت (O)','ضریب تراکم (P)','مساحت ساخت (Q)','مساحت ملک (R)','عوارض ساخت (S)','عوارض تراکم (T)','عوارض نهایی (U)'] as $h)
                            <th class="p-2 border border-slate-300 text-right">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="p-2 border border-slate-300" dir="ltr">۱</td>
                        <td class="p-2 border border-slate-300">{{ $file->village?->name }}</td>
                        <td class="p-2 border border-slate-300">{{ $file->params?->group }}</td>
                        <td class="p-2 border border-slate-300" dir="ltr">{{ \App\Support\Number::money($file->params?->transaction_value) }}</td>
                        <td class="p-2 border border-slate-300" dir="ltr">{{ \App\Support\Number::money($file->final_coefficient) }}</td>
                        <td class="p-2 border border-slate-300" dir="ltr">{{ \App\Support\Number::money($file->params?->construction_coefficient) }}</td>
                        <td class="p-2 border border-slate-300" dir="ltr">{{ \App\Support\Number::money($file->params?->density_coefficient) }}</td>
                        <td class="p-2 border border-slate-300" dir="ltr">{{ \App\Support\Number::money($file->construction_area) }}</td>
                        <td class="p-2 border border-slate-300" dir="ltr">{{ \App\Support\Number::money($file->property_area) }}</td>
                        <td class="p-2 border border-slate-300" dir="ltr">{{ \App\Support\Number::money($file->residential_levy) }}</td>
                        <td class="p-2 border border-slate-300" dir="ltr">{{ \App\Support\Number::money($file->density_levy) }}</td>
                        <td class="p-2 border border-slate-300 font-bold text-blue-800" dir="ltr">{{ \App\Support\Number::money($file->final_levy) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="text-xs text-slate-500 mt-2">
            فرمولها: N = M × ۰.۱۴ · S = Q × N × O · T = N × {{ $file->params?->density_area_source === 'R' ? 'R (مساحت ملک — استثناء اکسل)' : 'Q (مساحت ساخت)' }} × P · U = MAX(S,T)
        </p>

        @if($file->notes)
            <div class="mt-5 border-t border-slate-200 pt-3 text-sm"><b>یادداشت:</b> {{ $file->notes }}</div>
        @endif
    </div>
</div>
@endsection

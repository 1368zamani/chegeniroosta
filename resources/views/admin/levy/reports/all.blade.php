@extends(View::exists('layouts.admin') ? 'layouts.admin' : 'layouts.tariff-fallback')
@section('title', 'گزارش تجمیعی عوارض')
@section('content')
<div class="p-4 md:p-6" dir="rtl">
    @include('admin.levy._nav')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-5 no-print">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">گزارش تجمیعی تمام روستاها</h1>
            <p class="text-sm text-slate-500 mt-1">جمعها مستقیماً از فرمولهای اکسل محاسبه شدهاند.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="window.print()" class="px-4 py-2 rounded bg-slate-800 text-white text-sm hover:bg-slate-900">چاپ</button>
            <a href="{{ route('admin.levy.export.excel') }}" class="px-4 py-2 rounded bg-blue-700 text-white text-sm hover:bg-blue-800">خروجی اکسل</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm text-right min-w-[1000px]">
            <thead class="bg-slate-50 text-slate-600 text-xs">
                <tr>
                    @foreach(['ردیف','روستا','متقاضی','جمع عوارض (H)','عوارض ساخت (S)','عوارض تراکم (T)','عوارض نهایی (U)','وضعیت'] as $h)
                        <th class="p-3">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($files as $i => $f)
                    <tr>
                        <td class="p-3 text-slate-500" dir="ltr">{{ \App\Support\Number::money($i + 1) }}</td>
                        <td class="p-3 font-semibold">{{ $f->village?->name }}</td>
                        <td class="p-3">{{ $f->applicant_name ?: '—' }}</td>
                        <td class="p-3" dir="ltr">{{ \App\Support\Number::money($f->lines_total) }}</td>
                        <td class="p-3" dir="ltr">{{ \App\Support\Number::money($f->residential_levy) }}</td>
                        <td class="p-3" dir="ltr">{{ \App\Support\Number::money($f->density_levy) }}</td>
                        <td class="p-3 font-bold text-blue-800" dir="ltr">{{ \App\Support\Number::money($f->final_levy) }}</td>
                        <td class="p-3 text-xs">{{ $f->status === 'complete' ? 'تکمیلشده' : ($f->status === 'incomplete' ? 'ناقص' : 'پیشنویس') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="p-6 text-center text-slate-500">پروندهای ثبت نشده است.</td></tr>
                @endforelse
            </tbody>
            <tfoot class="bg-slate-100 font-bold text-slate-800">
                <tr>
                    <td class="p-3" colspan="3">جمع کل بخش</td>
                    <td class="p-3" dir="ltr">{{ \App\Support\Number::money($totals['lines_total']) }}</td>
                    <td class="p-3" dir="ltr">{{ \App\Support\Number::money($totals['residential_levy']) }}</td>
                    <td class="p-3" dir="ltr">{{ \App\Support\Number::money($totals['density_levy']) }}</td>
                    <td class="p-3 text-blue-800" dir="ltr">{{ \App\Support\Number::money($totals['final_levy']) }}</td>
                    <td class="p-3"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-5">
        <div class="bg-white rounded-lg border border-slate-200 p-4"><div class="text-xs text-slate-500">تعداد پرونده</div><div class="text-xl font-bold" dir="ltr">{{ \App\Support\Number::money($files->count()) }}</div></div>
        <div class="bg-white rounded-lg border border-slate-200 p-4"><div class="text-xs text-slate-500">جمع عوارض</div><div class="text-xl font-bold" dir="ltr">{{ \App\Support\Number::money($totals['lines_total']) }}</div></div>
        <div class="bg-white rounded-lg border border-slate-200 p-4"><div class="text-xs text-slate-500">جمع ارزش معاملاتی</div><div class="text-xl font-bold" dir="ltr">{{ \App\Support\Number::money($totals['transaction_value_sum']) }}</div></div>
        <div class="bg-white rounded-lg border border-slate-200 p-4 bg-slate-50"><div class="text-xs text-slate-600">جمع نهایی عوارض</div><div class="text-xl font-bold text-blue-800" dir="ltr">{{ \App\Support\Number::money($totals['final_levy']) }}</div></div>
    </div>
</div>
@endsection

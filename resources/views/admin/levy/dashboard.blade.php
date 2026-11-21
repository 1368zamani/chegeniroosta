@extends(View::exists('layouts.admin') ? 'layouts.admin' : 'layouts.tariff-fallback')
@section('title', 'داشبورد عوارض احداث بنا')
@section('content')
<div class="p-4 md:p-6" dir="rtl">
    @include('admin.levy._nav')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">داشبورد مدیریتی عوارض</h1>
            <p class="text-sm text-slate-500 mt-1">تمام اعداد این صفحه مستقیماً از اطلاعات ثبتشده و فرمولهای اکسل استخراج میشوند.</p>
        </div>
        <a href="{{ route('admin.levy.files.create') }}" class="px-4 py-2 rounded bg-slate-800 text-white text-sm hover:bg-slate-900">+ پرونده جدید</a>
    </div>

    {{-- کارتهای وضعیت --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
            <div class="text-xs text-slate-500">تعداد کل روستاهای سامانه</div>
            <div class="text-2xl font-bold text-slate-800 mt-1">{{ \App\Support\Number::money($totalVillages) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
            <div class="text-xs text-slate-500">روستاهای دارای پرونده</div>
            <div class="text-2xl font-bold text-blue-700 mt-1">{{ \App\Support\Number::money($filesCount) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
            <div class="text-xs text-slate-500">پروندههای تکمیلشده</div>
            <div class="text-2xl font-bold text-emerald-700 mt-1">{{ \App\Support\Number::money($completeCount) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
            <div class="text-xs text-slate-500">پروندههای ناقص / پیشنویس</div>
            <div class="text-2xl font-bold text-amber-700 mt-1">{{ \App\Support\Number::money($incompleteCount) }}</div>
        </div>
    </div>

    {{-- کارتهای جمعبندی محاسبات --}}
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 mb-6">
        <div class="px-4 py-3 border-b border-slate-200 font-bold text-slate-700">جمعبندی محاسباتی کل بخش (ریال)</div>
        <div class="grid grid-cols-2 md:grid-cols-5 divide-x divide-x-reverse divide-slate-100">
            <div class="p-4">
                <div class="text-xs text-slate-500">جمع عوارض (جدول چپ — H)</div>
                <div class="text-lg font-bold text-slate-800 mt-1" dir="ltr">{{ \App\Support\Number::money($totals['lines_total']) }}</div>
            </div>
            <div class="p-4">
                <div class="text-xs text-slate-500">عوارض ساخت مسکونی (S)</div>
                <div class="text-lg font-bold text-slate-800 mt-1" dir="ltr">{{ \App\Support\Number::money($totals['residential_levy']) }}</div>
            </div>
            <div class="p-4">
                <div class="text-xs text-slate-500">عوارض تراکم مجاز (T)</div>
                <div class="text-lg font-bold text-slate-800 mt-1" dir="ltr">{{ \App\Support\Number::money($totals['density_levy']) }}</div>
            </div>
            <div class="p-4">
                <div class="text-xs text-slate-500">جمع ارزش معاملاتی (M)</div>
                <div class="text-lg font-bold text-slate-800 mt-1" dir="ltr">{{ \App\Support\Number::money($totals['transaction_value_sum']) }}</div>
            </div>
            <div class="p-4 bg-slate-50">
                <div class="text-xs text-slate-600 font-semibold">جمع نهایی عوارض (U)</div>
                <div class="text-xl font-bold text-blue-800 mt-1" dir="ltr">{{ \App\Support\Number::money($totals['final_levy']) }}</div>
            </div>
        </div>
    </div>

    {{-- آخرین پروندهها --}}
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 mb-6">
        <div class="px-4 py-3 border-b border-slate-200 font-bold text-slate-700">آخرین پروندههای بهروزشده</div>
        @if($recentFiles->isEmpty())
            <div class="p-6 text-center text-sm text-slate-500">هنوز پروندهای ثبت نشده است. با ایجاد پرونده برای هر روستا، ارقام بهصورت خودکار محاسبه میشوند.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right">
                    <thead class="bg-slate-50 text-slate-600 text-xs">
                        <tr>
                            <th class="p-3">روستا</th><th class="p-3">سال</th><th class="p-3">متقاضی</th>
                            <th class="p-3">وضعیت</th><th class="p-3">درصد تکمیل</th><th class="p-3">عوارض نهایی</th><th class="p-3">عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentFiles as $f)
                            <tr>
                                <td class="p-3 font-semibold">{{ $f->village?->name }}</td>
                                <td class="p-3" dir="ltr">{{ \App\Support\Number::fa($f->jalali_year) }}</td>
                                <td class="p-3">{{ $f->applicant_name ?: '—' }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-xs {{ $f->status === 'complete' ? 'bg-emerald-100 text-emerald-800' : ($f->status === 'incomplete' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600') }}">
                                        {{ $f->status === 'complete' ? 'تکمیلشده' : ($f->status === 'incomplete' ? 'ناقص' : 'پیشنویس') }}
                                    </span>
                                </td>
                                <td class="p-3">{{ \App\Support\Number::money($f->completion_percent) }}٪</td>
                                <td class="p-3 font-bold" dir="ltr">{{ \App\Support\Number::money($f->final_levy) }}</td>
                                <td class="p-3"><a class="text-blue-700 hover:underline" href="{{ route('admin.levy.files.show', $f) }}">مشاهده</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- دسترسی سریع --}}
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
        <div class="font-bold text-slate-700 mb-3">دسترسی سریع به گزارشها</div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.levy.reports.all') }}" class="px-3 py-2 rounded bg-slate-100 text-slate-700 text-sm hover:bg-slate-200">گزارش تجمیعی تمام روستاها</a>
            <a href="{{ route('admin.levy.reports.compare') }}" class="px-3 py-2 rounded bg-slate-100 text-slate-700 text-sm hover:bg-slate-200">گزارش مقایسهای</a>
            <a href="{{ route('admin.levy.reports.incomplete') }}" class="px-3 py-2 rounded bg-slate-100 text-slate-700 text-sm hover:bg-slate-200">گزارش اطلاعات ناقص</a>
            <a href="{{ route('admin.levy.villages') }}" class="px-3 py-2 rounded bg-slate-100 text-slate-700 text-sm hover:bg-slate-200">تنظیم ضرایب روستاها</a>
            <a href="{{ route('admin.levy.export.excel') }}" class="px-3 py-2 rounded bg-blue-700 text-white text-sm hover:bg-blue-800">دریافت خروجی اکسل</a>
        </div>
    </div>
</div>
@endsection

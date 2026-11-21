@extends(View::exists('layouts.admin') ? 'layouts.admin' : 'layouts.tariff-fallback')
@section('title', 'شماره حسابهای دهیاریها')
@section('content')
<div class="p-4 md:p-6" dir="rtl">
    @include('admin.levy._nav')

    <div class="mb-5">
        <h1 class="text-2xl font-bold text-slate-800">شماره حسابهای دهیاریها</h1>
        <p class="text-sm text-slate-500 mt-1">فهرست حسابهای مندرج در شیت دوم فایل اکسل مرجع.</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="bg-slate-50 text-slate-600 text-xs">
                <tr><th class="p-3">ردیف</th><th class="p-3">شماره حساب</th><th class="p-3">عنوان / نام روستا</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($accounts as $a)
                    <tr>
                        <td class="p-3 text-slate-500" dir="ltr">{{ \App\Support\Number::money($a->sort_order) }}</td>
                        <td class="p-3 font-mono" dir="ltr">{{ $a->account_no ?: '—' }}</td>
                        <td class="p-3">{{ $a->label ?: '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

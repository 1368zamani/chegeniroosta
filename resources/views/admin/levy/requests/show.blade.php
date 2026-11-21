@extends(View::exists('layouts.admin') ? 'layouts.admin' : 'layouts.tariff-fallback')
@section('title', 'درخواست ' . $row->tracking_code)
@section('content')
<div class="p-4 md:p-6" dir="rtl">
    @include('admin.levy._nav')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div>
            <h1 class="text-xl font-bold text-slate-800">درخواست {{ $row->tracking_code }}</h1>
            <p class="text-sm text-slate-500 mt-1">
                {{ $row->village?->name }} · {{ $row->typeLabel() }}
                · ثبتشده در {{ $row->created_at?->format('Y-m-d H:i') }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-full text-xs font-bold {{ $row->statusColor() }}">{{ $row->statusLabel() }}</span>
            <a href="{{ route('admin.levy.requests.index') }}" class="px-3 py-1.5 rounded bg-slate-100 text-slate-700 text-sm">← بازگشت</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 space-y-5">
            {{-- مشخصات متقاضی --}}
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
                <h2 class="text-sm font-bold text-slate-700 mb-3">مشخصات متقاضی</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div><span class="text-slate-500">نام:</span> <strong>{{ $row->payer_name }}</strong></div>
                    <div><span class="text-slate-500">تلفن:</span> <strong dir="ltr">{{ $row->payer_mobile }}</strong></div>
                    <div><span class="text-slate-500">کد ملی:</span> <strong dir="ltr">{{ $row->national_id ?: '—' }}</strong></div>
                    <div><span class="text-slate-500">مبلغ:</span> <strong class="text-emerald-700">{{ \App\Support\Number::money($row->amount) }} ریال</strong></div>
                    @if($row->notes)<div class="md:col-span-2"><span class="text-slate-500">توضیحات:</span> {{ $row->notes }}</div>@endif
                </div>
            </div>

            {{-- جزئیات محاسبه / اقلام --}}
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
                <h2 class="text-sm font-bold text-slate-700 mb-3">جزئیات محاسبه / اقلام</h2>
                @if($row->type === 'construction')
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-slate-100">
                            <tr><td class="py-2 text-slate-600">مساحت ساخت (Q)</td><td class="py-2 font-bold" dir="ltr">{{ \App\Support\Number::fa($row->construction_area) }}</td></tr>
                            <tr><td class="py-2 text-slate-600">مساحت ملک (R)</td><td class="py-2 font-bold" dir="ltr">{{ \App\Support\Number::fa($row->property_area ?: '—') }}</td></tr>
                            <tr><td class="py-2 text-slate-600">ارزش معاملاتی (M)</td><td class="py-2 font-bold">{{ \App\Support\Number::money($row->transaction_value) }}</td></tr>
                            <tr><td class="py-2 text-slate-600">ضریب نهایی (N)</td><td class="py-2 font-bold">{{ \App\Support\Number::money($row->final_coefficient) }}</td></tr>
                            <tr><td class="py-2 text-slate-600">عوارض مسکونی (S)</td><td class="py-2 font-bold">{{ \App\Support\Number::money($row->residential_levy) }}</td></tr>
                            <tr><td class="py-2 text-slate-600">عوارض تراکم (T)</td><td class="py-2 font-bold">{{ \App\Support\Number::money($row->density_levy) }}</td></tr>
                            <tr class="bg-emerald-50"><td class="py-2 font-bold text-emerald-800">مبلغ نهایی (U)</td><td class="py-2 font-bold text-emerald-800">{{ \App\Support\Number::money($row->final_levy) }} ریال</td></tr>
                        </tbody>
                    </table>
                @else
                    <table class="w-full text-sm">
                        <thead><tr class="text-right text-xs text-slate-500 border-b"><th class="py-2">قلم</th><th class="py-2">واحد</th><th class="py-2">تعداد</th><th class="py-2">مبلغ واحد</th><th class="py-2">مبلغ</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($row->items as $item)
                            <tr>
                                <td class="py-2">{{ $item->title_snapshot }}</td>
                                <td class="py-2 text-xs text-slate-500">{{ $item->unit }}</td>
                                <td class="py-2" dir="ltr">{{ \App\Support\Number::fa($item->quantity) }}</td>
                                <td class="py-2">{{ \App\Support\Number::money($item->unit_price) }}</td>
                                <td class="py-2 font-bold">{{ \App\Support\Number::money($item->amount) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            {{-- رویدادها --}}
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
                <h2 class="text-sm font-bold text-slate-700 mb-3">تاریخچهٔ درخواست</h2>
                <ol class="space-y-3">
                    @foreach($row->events as $event)
                    <li class="flex items-start gap-3 text-sm">
                        <span class="mt-1 w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                        <div>
                            <div class="font-bold text-slate-700">{{ $event->action }}</div>
                            @if($event->note)<div class="text-xs text-slate-500">{{ $event->note }}</div>@endif
                            <div class="text-[11px] text-slate-400" dir="ltr">{{ $event->created_at?->format('Y-m-d H:i') }} — {{ $event->user?->name ?? 'سیستم' }}</div>
                        </div>
                    </li>
                    @endforeach
                </ol>
            </div>
        </div>

        {{-- ستون بررسی --}}
        <div class="space-y-5">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
                <h2 class="text-sm font-bold text-slate-700 mb-3">رسید پرداخت</h2>
                @if($row->receipt_path)
                    @if(preg_match('/\.(jpg|jpeg|png|webp)$/i', $row->receipt_path))
                        <a href="{{ asset('storage/' . $row->receipt_path) }}" target="_blank">
                            <img src="{{ asset('storage/' . $row->receipt_path) }}" alt="رسید پرداخت"
                                 class="w-full rounded border border-slate-200 mb-3">
                        </a>
                    @endif
                    <a href="{{ asset('storage/' . $row->receipt_path) }}" target="_blank"
                       class="text-blue-700 underline text-xs">مشاهدهٔ رسید در پنجرهٔ جدید</a>
                @else
                    <div class="p-4 rounded bg-amber-50 text-amber-800 text-xs">رسیدی ارسال نشده است.</div>
                @endif
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
                <h2 class="text-sm font-bold text-slate-700 mb-3">تصمیم کارشناس</h2>
                @if($row->status === 'approved')
                    <div class="p-3 rounded bg-emerald-50 text-emerald-800 text-xs mb-3">
                        تأییدشده توسط {{ $row->reviewer?->name ?? 'کارشناس' }} در {{ $row->reviewed_at?->format('Y-m-d H:i') }}
                    </div>
                @endif
                @if($row->status !== 'approved')
                <form method="POST" action="{{ route('admin.levy.requests.approve', $row->id) }}" class="mb-4">
                    @csrf
                    <button type="submit" class="w-full py-2.5 rounded bg-emerald-700 text-white text-sm font-bold hover:bg-emerald-800">
                        ✓ تأیید رسید پرداخت
                    </button>
                </form>
                @endif
                @if($row->status !== 'rejected')
                <form method="POST" action="{{ route('admin.levy.requests.reject', $row->id) }}">
                    @csrf
                    <label class="block text-xs font-bold text-slate-600 mb-1">دلیل رد</label>
                    <textarea name="review_note" rows="3" required
                              class="w-full border border-slate-300 rounded p-2 text-sm mb-2"
                              placeholder="دلیل رد درخواست را بنویسید...">{{ old('review_note') }}</textarea>
                    @error('review_note')<div class="text-xs text-rose-600 mb-2">{{ $message }}</div>@enderror
                    <button type="submit" class="w-full py-2.5 rounded bg-rose-700 text-white text-sm font-bold hover:bg-rose-800">
                        ✗ رد درخواست
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

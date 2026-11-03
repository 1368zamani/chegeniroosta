@extends(View::exists('layouts.admin') ? 'layouts.admin' : 'layouts.tariff-fallback')
@section('title', 'جزئیات تعرفه')
@section('content')
<div class="p-4 md:p-6" dir="rtl">
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold">{{ $tariffSet->village?->name }} — {{ \App\Support\Number::fa($tariffSet->jalali_year) }}</h1>
            <p class="text-sm text-gray-600 mt-1">وضعیت: <strong>{{ $tariffSet->statusLabel() }}</strong></p>
        </div>
        <div class="flex gap-2">
            @can('update', $tariffSet)
                <a href="{{ route('admin.tariffs.edit', $tariffSet) }}" class="bg-emerald-600 text-white px-4 py-2 rounded">ویرایش</a>
            @endcan
            <a href="{{ route('admin.tariffs.exemptions.index', $tariffSet) }}" class="bg-amber-600 text-white px-4 py-2 rounded">📋 معافیت‌ها</a>
            <a href="{{ route('admin.tariffs.logs.index', $tariffSet) }}" class="bg-gray-600 text-white px-4 py-2 rounded">📜 تاریخچه</a>
            <a href="{{ route('admin.tariffs.print.form2', $tariffSet) }}" target="_blank" class="bg-gray-700 text-white px-4 py-2 rounded">🖨 فرم ۲ (عوارض)</a>
            <a href="{{ route('admin.tariffs.pdf.form2', $tariffSet) }}" class="bg-red-700 text-white px-4 py-2 rounded">📄 PDF فرم ۲</a>
            <a href="{{ route('admin.tariffs.pdf.form3', $tariffSet) }}" class="bg-red-700 text-white px-4 py-2 rounded">📄 PDF فرم ۳</a>
            <a href="{{ route('admin.tariffs.print.form3', $tariffSet) }}" target="_blank" class="bg-gray-700 text-white px-4 py-2 rounded">🖨 فرم ۳ (بهای خدمات)</a>
            <a href="{{ route('admin.tariffs.index') }}" class="px-4 py-2 border rounded">بازگشت</a>
        </div>
    </div>
    @if(session('success'))
        <div class="bg-emerald-50 border-r-4 border-emerald-500 text-emerald-800 p-3 mb-4 rounded">{{ session('success') }}</div>
    @endif
    @if(!$inflationCheck['ok'])
        <div class="bg-red-50 border-r-4 border-red-500 text-red-800 p-3 mb-4 rounded">
            <strong>⚠️ هشدار تورم (ماده ۸):</strong>
            <ul class="list-disc pr-5 mt-1 text-sm">
                @foreach($inflationCheck['violations'] as $v)
                    <li>{{ $v['title'] }} — {{ \App\Support\Number::fa($v['increase']) }}٪ (حداکثر {{ \App\Support\Number::fa($v['max']) }}٪)</li>
                @endforeach
            </ul>
        </div>
    @endif
    @php
        $transitions = [
            'draft' => ['proposed' => 'ارسال به‌عنوان پیشنهاد دهیار'],
            'proposed' => ['village_council_approved' => 'تصویب شورای روستا'],
            'village_council_approved' => ['district_council_approved' => 'تأیید شورای بخش'],
            'district_council_approved' => ['finalized' => 'قطعی‌سازی (قفل)'],
            'finalized' => ['published' => 'اعلام عمومی'],
        ];
        $next = $transitions[$tariffSet->status] ?? [];
    @endphp
    @if(!empty($next))
        <div class="bg-green-50 border-r-4 border-green-500 p-3 mb-6 rounded flex gap-2 flex-wrap items-center">
            <span class="font-medium">مرحله بعد:</span>
            @foreach($next as $to => $label)
                @can('transition', [$tariffSet, $to])
                <form method="POST" action="{{ route('admin.tariffs.transition', [$tariffSet, $to]) }}" class="inline">
                    @csrf
                    <button class="bg-green-600 text-white px-4 py-1.5 rounded text-sm">{{ $label }}</button>
                </form>
                @endcan
            @endforeach
        </div>
    @endif
    @if($tariffSet->isLocked())
        <div class="bg-amber-50 border-r-4 border-amber-500 text-amber-900 p-3 mb-6 rounded text-sm">🔒 این مجموعه قفل شده است.</div>
    @endif
    <h2 class="text-lg font-bold mb-2">آیتم‌ها ({{ $tariffSet->items->count() }})</h2>
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-right text-sm">
            <thead class="bg-gray-100"><tr>
                <th class="p-2">عنوان</th><th class="p-2">نوع</th><th class="p-2">مبلغ قبلی</th>
                <th class="p-2">٪ افزایش</th><th class="p-2">مبلغ نهایی</th>
            </tr></thead>
            <tbody>
                @foreach($tariffSet->items as $item)
                <tr class="border-t {{ !$item->is_active ? 'opacity-50' : '' }}">
                    <td class="p-2">{{ $item->title?->name_fa }}</td>
                    <td class="p-2 text-xs">{{ $item->title?->typeLabel() }}</td>
                    <td class="p-2 font-mono">{{ \App\Support\Number::fa(number_format($item->previous_amount)) }}</td>
                    <td class="p-2 font-mono">{{ \App\Support\Number::fa($item->increase_percent) }}٪</td>
                    <td class="p-2 font-mono">{{ \App\Support\Number::fa(number_format($item->final_amount)) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

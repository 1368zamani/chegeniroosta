@extends('admin.village.layout')
@section('title', 'گزارش خلاصه')

@php
    use App\Support\Number;
    $fa = fn ($n) => Number::fa(number_format((int) $n));
@endphp

@section('content')
<div dir="rtl" class="max-w-4xl mx-auto">

    <div class="mb-4">
        <a href="{{ route('admin.village.tariff-reports.index', $village->id) }}"
           class="text-emerald-700 text-sm">← بازگشت</a>
    </div>

    <h1 class="text-2xl font-bold mb-2">📈 گزارش خلاصه</h1>
    <p class="text-gray-600 mb-6">روستای {{ $village->name }}</p>

    @if($yearStats->isEmpty())
    <div class="bg-white rounded-lg shadow p-6 text-center text-gray-500">
        📭 هنوز آماری وجود ندارد.
    </div>
    @else

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-right text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3">سال</th>
                    <th class="p-3">تعداد فیش</th>
                    <th class="p-3">جمع کل (ریال)</th>
                    <th class="p-3">دریافتی (ریال)</th>
                    <th class="p-3">نرخ وصول</th>
                </tr>
            </thead>
            <tbody>
                @foreach($yearStats as $s)
                <tr class="border-t">
                    <td class="p-3 font-bold">{{ Number::fa($s->year) }}</td>
                    <td class="p-3">{{ Number::fa($s->total_count) }}</td>
                    <td class="p-3 font-mono">{{ $fa($s->total_amount) }}</td>
                    <td class="p-3 font-mono text-emerald-700">{{ $fa($s->paid_amount) }}</td>
                    <td class="p-3">
                        @php
                            $rate = $s->total_amount > 0 ? ($s->paid_amount / $s->total_amount) * 100 : 0;
                        @endphp
                        <span class="px-2 py-1 rounded text-xs {{ $rate > 50 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ Number::fa(round($rate)) }}%
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @endif
</div>
@endsection

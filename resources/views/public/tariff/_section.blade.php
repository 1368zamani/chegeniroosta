@if($items->isNotEmpty())
<section class="mb-8">
    <h2 class="text-xl font-bold mb-3 text-emerald-800">{{ $title }}</h2>
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-right text-sm">
            <thead class="bg-gray-100"><tr>
                <th class="p-3 w-12">ردیف</th><th class="p-3">عنوان</th>
                <th class="p-3">فرمول</th><th class="p-3 w-40">مبلغ (ریال)</th><th class="p-3 w-32">واحد</th>
            </tr></thead>
            <tbody>
                @foreach($items as $i => $item)
                <tr data-tariff-row class="border-t">
                    <td class="p-3 text-gray-500">{{ \App\Support\Number::fa($i + 1) }}</td>
                    <td class="p-3 font-medium">{{ $item->title->name_fa }}
                        @if($item->title->statutory_note)<div class="text-xs text-gray-500 mt-1">{{ $item->title->statutory_note }}</div>@endif
                    </td>
                    <td class="p-3 text-xs text-gray-600">{{ $item->formula_display ?: '—' }}</td>
                    <td class="p-3 font-mono">{{ $item->final_amount ? \App\Support\Number::fa(number_format($item->final_amount)) : '—' }}</td>
                    <td class="p-3 text-xs">{{ $item->unit === 'rial' ? 'ریال' : $item->unit }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endif

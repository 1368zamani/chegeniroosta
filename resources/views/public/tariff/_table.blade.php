@if($items->isEmpty())
<div class="text-center py-10 text-gray-500">
    <div class="text-4xl mb-2">📭</div>
    <p>موردی برای این دسته‌بندی ثبت نشده است.</p>
</div>
@else

{{-- جستجو --}}
<div class="mb-4">
    <input type="text"
           x-model="search"
           @input="filterRows()"
           placeholder="🔍 جستجو در جدول..."
           class="w-full p-2 border rounded-lg">
</div>

{{-- جدول --}}
<div class="overflow-x-auto">
    <table class="w-full text-right border-collapse" data-tariff-type="{{ $type }}">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-3 border-b">#</th>
                <th class="p-3 border-b">عنوان</th>
                <th class="p-3 border-b">مبلغ (ریال)</th>
                <th class="p-3 border-b">واحد</th>
                <th class="p-3 border-b">فرمول</th>
                @auth
                <th class="p-3 border-b no-print">عملیات</th>
                @endauth
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            @if($item->title)
            <tr class="tariff-row hover:bg-gray-50" data-tariff-row data-search-text="{{ $item->title->name_fa ?? '' }} {{ $item->notes ?? '' }}">
                <td class="p-3 border-b text-sm text-gray-500">{{ $index + 1 }}</td>
                <td class="p-3 border-b">
                    {{-- ستون این جدول «name_fa» است، نه «title» — قبلاً نام تعرفه خالی نمایش داده می‌شد --}}
                    <strong>{{ $item->title->name_fa }}</strong>
                    @if($item->notes)
                    <div class="text-xs text-gray-500 mt-1">{{ $item->notes }}</div>
                    @endif
                </td>
                <td class="p-3 border-b font-mono font-bold text-emerald-700">
                    {{ number_format($item->final_amount) }}
                </td>
                <td class="p-3 border-b text-sm text-gray-600">{{ $item->unit ?? '-' }}</td>
                <td class="p-3 border-b text-xs font-mono text-gray-600" dir="ltr">
                    {{ $item->formula_display ?? '-' }}
                </td>
                @auth
                <td class="p-3 border-b no-print">
                    <button @click="openEditor({{ $item->id }}, @js($item->title->name_fa))"
                            class="text-xs bg-orange-100 text-orange-800 px-3 py-1 rounded hover:bg-orange-200">
                        ✏️ ویرایش
                    </button>
                </td>
                @endauth
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
</div>

@endif

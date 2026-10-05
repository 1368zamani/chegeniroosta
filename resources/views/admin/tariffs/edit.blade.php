@extends(View::exists('layouts.admin') ? 'layouts.admin' : 'layouts.tariff-fallback')
@section('title', 'ویرایش تعرفه')
@section('content')
<div class="p-4 md:p-6" dir="rtl">
    <h1 class="text-2xl font-bold mb-2">ویرایش {{ $tariffSet->village?->name }} — {{ \App\Support\Number::fa($tariffSet->jalali_year) }}</h1>
    <p class="text-sm text-gray-600 mb-6">وضعیت: <strong>{{ $tariffSet->statusLabel() }}</strong></p>
    @if(session('success'))<div class="bg-emerald-50 border-r-4 border-emerald-500 text-emerald-800 p-3 mb-4 rounded">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="bg-red-50 border-r-4 border-red-500 text-red-800 p-3 mb-4 rounded"><ul class="list-disc pr-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('admin.tariffs.items.update', $tariffSet) }}">
        @csrf
        <div class="bg-white rounded-lg shadow overflow-x-auto mb-6">
            <table class="w-full text-right text-sm">
                <thead class="bg-gray-100"><tr>
                    <th class="p-2">فعال</th><th class="p-2">عنوان</th><th class="p-2">مبلغ قبلی</th>
                    <th class="p-2 w-24">٪ افزایش</th><th class="p-2 w-40">مبلغ نهایی</th>
                </tr></thead>
                <tbody>
                    @foreach($tariffSet->items as $i => $item)
                    <tr class="border-t">
                        <td class="p-2">
                            <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                            <input type="checkbox" name="items[{{ $i }}][is_active]" value="1" @checked($item->is_active)>
                        </td>
                        <td class="p-2">{{ $item->title?->name_fa }}</td>
                        <td class="p-2 font-mono">{{ \App\Support\Number::fa(number_format($item->previous_amount)) }}</td>
                        <td class="p-2"><input type="number" step="0.01" min="0" max="200" name="items[{{ $i }}][increase_percent]" value="{{ $item->increase_percent }}" class="w-20 border rounded p-1"></td>
                        <td class="p-2"><input type="number" min="0" name="items[{{ $i }}][final_amount]" value="{{ $item->final_amount }}" class="w-36 border rounded p-1 font-mono"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="flex gap-2">
            <button class="bg-emerald-600 text-white px-6 py-2 rounded">ذخیره</button>
            <a href="{{ route('admin.tariffs.show', $tariffSet) }}" class="px-6 py-2 rounded border">مشاهده</a>
        </div>
    </form>
</div>
@endsection

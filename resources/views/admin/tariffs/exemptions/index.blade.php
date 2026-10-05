@extends(View::exists('layouts.admin') ? 'layouts.admin' : 'layouts.tariff-fallback')
@section('title', 'مدیریت معافیت‌ها')

@section('content')
<div class="p-4 md:p-6" dir="rtl">
    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold">معافیت‌های تعرفه</h1>
            <p class="text-sm text-gray-600 mt-1">
                روستای {{ $tariffSet->village?->name }} — سال {{ \App\Support\Number::fa($tariffSet->jalali_year) }}
                <span class="mx-2">|</span>
                وضعیت: <strong>{{ $tariffSet->statusLabel() }}</strong>
            </p>
        </div>
        <a href="{{ route('admin.tariffs.show', $tariffSet) }}" class="px-4 py-2 border rounded">بازگشت</a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border-r-4 border-emerald-500 text-emerald-800 p-3 mb-4 rounded">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border-r-4 border-red-500 text-red-800 p-3 mb-4 rounded">
            <ul class="list-disc pr-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    @if($tariffSet->isLocked())
        <div class="bg-amber-50 border-r-4 border-amber-500 text-amber-900 p-3 mb-4 rounded text-sm">
            🔒 این مجموعه قفل شده است. برای تغییر معافیت‌ها ابتدا از lock خارج کنید.
        </div>
    @endif

    {{-- فرم ایجاد --}}
    @can('update', $tariffSet)
    <form method="POST" action="{{ route('admin.tariffs.exemptions.store', $tariffSet) }}"
          class="bg-white rounded-lg shadow p-4 mb-6">
        @csrf
        <h2 class="font-bold mb-3">افزودن معافیت جدید</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-sm mb-1">نوع معافیت</label>
                <select name="kind" class="w-full border rounded p-2" required>
                    @foreach(\App\Models\TariffExemption::kindOptions() as $k => $label)
                        <option value="{{ $k }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">عنوان مرتبط (اختیاری)</label>
                <select name="tariff_title_id" class="w-full border rounded p-2">
                    <option value="">— همه‌ی عناوین —</option>
                    @foreach($tariffSet->items as $it)
                        @if($it->title)
                            <option value="{{ $it->title->id }}">{{ $it->title->name_fa }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">درصد تخفیف (۱۰۰ = کامل)</label>
                <input type="number" name="discount_percent" value="100" min="0" max="100" step="0.01"
                       class="w-full border rounded p-2" required>
            </div>
            <div class="flex items-end">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span class="text-sm">فعال</span>
                </label>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
            <div>
                <label class="block text-sm mb-1">شرح</label>
                <input type="text" name="description" maxlength="500"
                       class="w-full border rounded p-2" required
                       placeholder="مثلاً: سالمندان بالای ۷۰ سال">
            </div>
            <div>
                <label class="block text-sm mb-1">یادداشت قانونی (اختیاری)</label>
                <input type="text" name="statutory_note" maxlength="2000"
                       class="w-full border rounded p-2">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
            <div>
                <label class="block text-sm mb-1">معتبر از (شمسی — YYYY/MM/DD)</label>
                <input type="text" name="valid_from" class="w-full border rounded p-2" placeholder="1405/01/01">
            </div>
            <div>
                <label class="block text-sm mb-1">معتبر تا</label>
                <input type="text" name="valid_until" class="w-full border rounded p-2" placeholder="1405/12/29">
            </div>
        </div>
        <button class="mt-4 bg-emerald-600 text-white px-6 py-2 rounded">افزودن</button>
    </form>
    @endcan

    {{-- جدول --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-right text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2">#</th>
                    <th class="p-2">نوع</th>
                    <th class="p-2">عنوان مرتبط</th>
                    <th class="p-2">درصد</th>
                    <th class="p-2">شرح</th>
                    <th class="p-2">اعتبار</th>
                    <th class="p-2">فعال</th>
                    <th class="p-2">عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($exemptions as $i => $ex)
                <tr class="border-t {{ !$ex->is_active ? 'opacity-50' : '' }}">
                    <td class="p-2">{{ \App\Support\Number::fa($i + 1) }}</td>
                    <td class="p-2">{{ $ex->kindLabel() }}</td>
                    <td class="p-2 text-xs">{{ $ex->title?->name_fa ?: '—همه—' }}</td>
                    <td class="p-2 font-mono">{{ \App\Support\Number::fa($ex->discount_percent) }}٪</td>
                    <td class="p-2 text-xs">{{ $ex->description }}</td>
                    <td class="p-2 text-xs">
                        {{ $ex->valid_from ?: '—' }} تا {{ $ex->valid_until ?: '—' }}
                    </td>
                    <td class="p-2">{!! $ex->is_active ? '✓' : '✗' !!}</td>
                    <td class="p-2">
                        @can('update', $tariffSet)
                        <form method="POST" action="{{ route('admin.tariffs.exemptions.destroy', [$tariffSet, $ex]) }}"
                              class="inline" onsubmit="return confirm('حذف شود؟')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline text-xs">حذف</button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="p-6 text-center text-gray-500">معافیتی ثبت نشده است.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

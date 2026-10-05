@extends(View::exists('layouts.admin') ? 'layouts.admin' : 'layouts.tariff-fallback')
@section('title', 'مدیریت تعرفه‌ها')
@section('content')
<div class="p-4 md:p-6" dir="rtl">
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <h1 class="text-2xl font-bold">تعرفه عوارض و بهای خدمات</h1>
        @can('create', App\Models\TariffSet::class)
            <a href="{{ route('admin.tariffs.create') }}" class="bg-emerald-600 text-white px-4 py-2 rounded">+ مجموعه سالانه جدید</a>
        @endcan
    </div>
    @if(session('success'))
        <div class="bg-emerald-50 border-r-4 border-emerald-500 text-emerald-800 p-3 mb-4 rounded">{{ session('success') }}</div>
    @endif
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-right">
            <thead class="bg-gray-100 text-sm"><tr>
                <th class="p-3">روستا</th><th class="p-3">سال</th><th class="p-3">وضعیت</th>
                <th class="p-3">نرخ تورم</th><th class="p-3">تعداد آیتم</th><th class="p-3">عملیات</th>
            </tr></thead>
            <tbody>
                @forelse($sets as $set)
                <tr class="border-t hover:bg-gray-50">
                    <td class="p-3">{{ $set->village?->name }}</td>
                    <td class="p-3 font-mono">{{ \App\Support\Number::fa($set->jalali_year) }}</td>
                    <td class="p-3"><span class="text-xs px-2 py-1 rounded bg-gray-100">{{ $set->statusLabel() }}</span></td>
                    <td class="p-3">{{ \App\Support\Number::fa($set->inflation_rate) }}٪</td>
                    <td class="p-3">{{ \App\Support\Number::fa($set->items()->count()) }}</td>
                    <td class="p-3 whitespace-nowrap">
                        <a href="{{ route('admin.tariffs.show', $set) }}" class="text-blue-600 hover:underline ml-2">مشاهده</a>
                        @can('update', $set)
                            <a href="{{ route('admin.tariffs.edit', $set) }}" class="text-emerald-600 hover:underline">ویرایش</a>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-6 text-center text-gray-500">موردی یافت نشد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $sets->links() }}</div>
</div>
@endsection

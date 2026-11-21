@extends(View::exists('layouts.admin') ? 'layouts.admin' : 'layouts.tariff-fallback')
@section('title', 'پروندههای عوارض')
@section('content')
<div class="p-4 md:p-6" dir="rtl">
    @include('admin.levy._nav')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">پروندههای مستقل روستاها</h1>
            <p class="text-sm text-slate-500 mt-1">هر روستا یک پرونده مستقل دارد و اطلاعات آن جداگانه ذخیره میشود.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.levy.files.create') }}" class="px-4 py-2 rounded bg-slate-800 text-white text-sm hover:bg-slate-900">+ پرونده جدید</a>
            <a href="{{ route('admin.levy.export.excel') }}" class="px-4 py-2 rounded bg-blue-700 text-white text-sm hover:bg-blue-800">خروجی اکسل</a>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.levy.files.index') }}" class="bg-white rounded-lg shadow-sm border border-slate-200 p-4 mb-4 no-print flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-slate-600 mb-1">جستجوی نام روستا</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="نام روستا…" class="w-full border border-slate-300 rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-slate-600 mb-1">وضعیت</label>
            <select name="status" class="border border-slate-300 rounded px-3 py-2 text-sm">
                <option value="">همه</option>
                <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>پیشنویس</option>
                <option value="incomplete" {{ $status === 'incomplete' ? 'selected' : '' }}>ناقص</option>
                <option value="complete" {{ $status === 'complete' ? 'selected' : '' }}>تکمیلشده</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 rounded bg-slate-700 text-white text-sm hover:bg-slate-800">فیلتر</button>
        @if($search !== '' || $status !== '')
            <a href="{{ route('admin.levy.files.index') }}" class="px-4 py-2 rounded bg-slate-100 text-slate-700 text-sm hover:bg-slate-200">پاک کردن</a>
        @endif
    </form>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm text-right min-w-[900px]">
            <thead class="bg-slate-50 text-slate-600 text-xs">
                <tr>
                    <th class="p-3">روستا</th><th class="p-3">سال</th><th class="p-3">متقاضی</th>
                    <th class="p-3">وضعیت</th><th class="p-3">درصد تکمیل</th><th class="p-3">عوارض نهایی</th><th class="p-3 no-print">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($files as $f)
                    <tr>
                        <td class="p-3 font-semibold">{{ $f->village?->name }}</td>
                        <td class="p-3" dir="ltr">{{ \App\Support\Number::fa($f->jalali_year) }}</td>
                        <td class="p-3">{{ $f->applicant_name ?: '—' }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded text-xs {{ $f->status === 'complete' ? 'bg-emerald-100 text-emerald-800' : ($f->status === 'incomplete' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600') }}">
                                {{ $f->status === 'complete' ? 'تکمیلشده' : ($f->status === 'incomplete' ? 'ناقص' : 'پیشنویس') }}
                            </span>
                        </td>
                        <td class="p-3">
                            <div class="flex items-center gap-2">
                                <div class="w-20 bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 bg-blue-600" style="width: {{ $f->completion_percent }}%"></div>
                                </div>
                                <span class="text-xs text-slate-600" dir="ltr">{{ \App\Support\Number::money($f->completion_percent) }}٪</span>
                            </div>
                        </td>
                        <td class="p-3 font-bold" dir="ltr">{{ \App\Support\Number::money($f->final_levy) }}</td>
                        <td class="p-3 no-print">
                            <div class="flex flex-wrap gap-2 text-xs">
                                <a class="text-blue-700 hover:underline" href="{{ route('admin.levy.files.show', $f) }}">مشاهده / ویرایش</a>
                                <a class="text-slate-700 hover:underline" href="{{ route('admin.levy.reports.village', $f) }}">گزارش</a>
                                <a class="text-blue-700 hover:underline" href="{{ route('admin.levy.export.file', $f) }}">اکسل</a>
                                <a class="text-slate-500 hover:underline" href="{{ route('admin.levy.files.audit', $f) }}">سوابق</a>
                                <form method="POST" action="{{ route('admin.levy.files.destroy', $f) }}" onsubmit="return confirm('پرونده به بایگانی منتقل شود؟');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-rose-700 hover:underline">حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-center text-slate-500">پروندهای یافت نشد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4 no-print">{{ $files->links() }}</div>
</div>
@endsection

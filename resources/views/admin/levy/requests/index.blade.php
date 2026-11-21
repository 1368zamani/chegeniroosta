@extends(View::exists('layouts.admin') ? 'layouts.admin' : 'layouts.tariff-fallback')
@section('title', 'درخواستهای مردمی عوارض')
@section('content')
<div class="p-4 md:p-6" dir="rtl">
    @include('admin.levy._nav')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">درخواستهای مردمی عوارض</h1>
            <p class="text-sm text-slate-500 mt-1">ثبتنامها، مبالغ محاسبهشده و وضعیت رسید پرداخت</p>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-5">
        @foreach([
            ['all', 'همه', $counts['all'], 'slate'],
            ['awaiting_receipt', 'در انتظار رسید', $counts['awaiting'], 'amber'],
            ['under_review', 'در بررسی', $counts['under'], 'blue'],
            ['approved', 'تأییدشده', $counts['approved'], 'emerald'],
            ['rejected', 'ردشده', $counts['rejected'], 'rose'],
        ] as $c)
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-3">
            <div class="text-[11px] text-slate-500">{{ $c[1] }}</div>
            <div class="text-xl font-bold text-{{ $c[3] }}-700 mt-1">{{ \App\Support\Number::money($c[2]) }}</div>
        </div>
        @endforeach
    </div>

    <form method="GET" class="bg-white rounded-lg shadow-sm border border-slate-200 p-4 mb-5">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">جستجو</label>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                       placeholder="کد پیگیری، نام یا تلفن"
                       class="w-full border border-slate-300 rounded p-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">نوع</label>
                <select name="type" class="w-full border border-slate-300 rounded p-2 text-sm">
                    <option value="">همه</option>
                    <option value="construction" {{ ($filters['type'] ?? '') === 'construction' ? 'selected' : '' }}>احداث بنا</option>
                    <option value="waste" {{ ($filters['type'] ?? '') === 'waste' ? 'selected' : '' }}>پسماند</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">وضعیت</label>
                <select name="status" class="w-full border border-slate-300 rounded p-2 text-sm">
                    <option value="">همه</option>
                    @foreach(['awaiting_receipt' => 'در انتظار رسید', 'under_review' => 'در بررسی', 'approved' => 'تأییدشده', 'rejected' => 'ردشده'] as $k => $v)
                        <option value="{{ $k }}" {{ ($filters['status'] ?? '') === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">روستا</label>
                <select name="village_id" class="w-full border border-slate-300 rounded p-2 text-sm">
                    <option value="">همه روستاها</option>
                    @foreach($villages as $village)
                        <option value="{{ $village->id }}" {{ (string)($filters['village_id'] ?? '') === (string)$village->id ? 'selected' : '' }}>{{ $village->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-3 flex gap-2">
            <button type="submit" class="px-4 py-2 rounded bg-slate-800 text-white text-sm">اعمال فیلتر</button>
            <a href="{{ route('admin.levy.requests.index') }}" class="px-4 py-2 rounded bg-slate-100 text-slate-700 text-sm">پاکسازی</a>
        </div>
    </form>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 text-xs">
                <tr>
                    <th class="p-3 text-right">کد پیگیری</th>
                    <th class="p-3 text-right">روستا</th>
                    <th class="p-3 text-right">نوع</th>
                    <th class="p-3 text-right">متقاضی</th>
                    <th class="p-3 text-right">مبلغ</th>
                    <th class="p-3 text-right">وضعیت</th>
                    <th class="p-3 text-right">تاریخ</th>
                    <th class="p-3 text-right">—</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($rows as $row)
                <tr class="hover:bg-slate-50">
                    <td class="p-3 text-left" dir="ltr">{{ $row->tracking_code }}</td>
                    <td class="p-3">{{ $row->village?->name }}</td>
                    <td class="p-3">{{ $row->typeLabel() }}</td>
                    <td class="p-3">
                        <div>{{ $row->payer_name }}</div>
                        <div class="text-[11px] text-slate-500" dir="ltr">{{ $row->payer_mobile }}</div>
                    </td>
                    <td class="p-3 font-bold">{{ \App\Support\Number::money($row->amount) }}</td>
                    <td class="p-3"><span class="px-2 py-1 rounded-full text-[11px] font-bold {{ $row->statusColor() }}">{{ $row->statusLabel() }}</span></td>
                    <td class="p-3 text-xs text-slate-500" dir="ltr">{{ $row->created_at?->format('Y-m-d H:i') }}</td>
                    <td class="p-3">
                        <a href="{{ route('admin.levy.requests.show', $row->id) }}" class="text-blue-700 hover:underline text-xs">مشاهده</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="p-6 text-center text-slate-500">درخواستی یافت نشد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $rows->links() }}</div>
</div>
@endsection

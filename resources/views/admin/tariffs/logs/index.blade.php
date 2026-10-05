@extends(View::exists('layouts.admin') ? 'layouts.admin' : 'layouts.tariff-fallback')
@section('title', 'تاریخچه‌ی گردش کار')

@section('content')
<div class="p-4 md:p-6" dir="rtl">
    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold">تاریخچه‌ی گردش کار</h1>
            <p class="text-sm text-gray-600 mt-1">
                روستای {{ $tariffSet->village?->name }} — سال {{ \App\Support\Number::fa($tariffSet->jalali_year) }}
            </p>
        </div>
        <a href="{{ route('admin.tariffs.show', $tariffSet) }}" class="px-4 py-2 border rounded">بازگشت</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-right text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2">#</th>
                    <th class="p-2">تاریخ</th>
                    <th class="p-2">از وضعیت</th>
                    <th class="p-2">به وضعیت</th>
                    <th class="p-2">کاربر</th>
                    <th class="p-2">نقش</th>
                    <th class="p-2">IP</th>
                    <th class="p-2">یادداشت</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $i => $log)
                <tr class="border-t">
                    <td class="p-2">{{ \App\Support\Number::fa($logs->firstItem() + $i) }}</td>
                    <td class="p-2 font-mono text-xs">{{ \Morilog\Jalali\Jalalian::fromCarbon($log->created_at)->format('Y/m/d H:i') }}</td>
                    <td class="p-2 text-xs">{{ $log->from_status ?: '—' }}</td>
                    <td class="p-2 text-xs font-medium text-emerald-700">{{ $log->toLabel() }}</td>
                    <td class="p-2 text-xs">{{ $log->user?->email ?: 'سیستم' }}</td>
                    <td class="p-2 text-xs">{{ $log->user_role ?: '—' }}</td>
                    <td class="p-2 font-mono text-xs ltr">{{ $log->ip ?: '—' }}</td>
                    <td class="p-2 text-xs">{{ $log->note ?: '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="p-6 text-center text-gray-500">تاریخچه‌ای ثبت نشده است.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
</div>
@endsection

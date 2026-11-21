@extends(View::exists('layouts.admin') ? 'layouts.admin' : 'layouts.tariff-fallback')
@section('title', 'سوابق تغییرات پرونده')
@section('content')
<div class="p-4 md:p-6" dir="rtl">
    @include('admin.levy._nav')

    <div class="mb-5">
        <h1 class="text-2xl font-bold text-slate-800">سوابق تغییرات — {{ $file->village?->name }}</h1>
        <p class="text-sm text-slate-500 mt-1">هر تغییر همراه با نام کاربر و زمان ثبت شده است.</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="bg-slate-50 text-slate-600 text-xs">
                <tr><th class="p-3">ردیف</th><th class="p-3">زمان</th><th class="p-3">کاربر</th><th class="p-3">عملیات</th><th class="p-3">تغییرات</th><th class="p-3">آدرس IP</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($audits as $i => $a)
                    <tr>
                        <td class="p-3 text-slate-500" dir="ltr">{{ \App\Support\Number::money($i + 1) }}</td>
                        <td class="p-3" dir="ltr">{{ $a->created_at?->format('Y-m-d H:i:s') }}</td>
                        <td class="p-3">{{ $a->user?->name ?? 'سیستم' }}</td>
                        <td class="p-3 font-semibold">{{ $a->action }}</td>
                        <td class="p-3">
                            @if($a->changes)
                                <pre class="text-[11px] bg-slate-50 rounded p-2 overflow-x-auto max-w-md" dir="ltr">{{ json_encode($a->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="p-3" dir="ltr">{{ $a->ip }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-slate-500">تاکنون تغییری ثبت نشده است.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.levy.files.show', $file) }}" class="text-sm text-blue-700 hover:underline">← بازگشت به پرونده</a>
    </div>
</div>
@endsection

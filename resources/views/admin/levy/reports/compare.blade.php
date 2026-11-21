@extends(View::exists('layouts.admin') ? 'layouts.admin' : 'layouts.tariff-fallback')
@section('title', 'گزارش مقایسهای روستاها')
@section('content')
<div class="p-4 md:p-6" dir="rtl">
    @include('admin.levy._nav')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-5 no-print">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">مقایسه عملکرد و ارقام روستاها</h1>
            <p class="text-sm text-slate-500 mt-1">برای مرتبسازی، عنوان ستون «عوارض نهایی» را کلیک کنید.</p>
        </div>
        <button onclick="window.print()" class="px-4 py-2 rounded bg-slate-800 text-white text-sm hover:bg-slate-900">چاپ</button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-x-auto">
        <table class="w-full text-sm text-right min-w-[900px]" id="compareTable">
            <thead class="bg-slate-50 text-slate-600 text-xs">
                <tr>
                    <th class="p-3">ردیف</th><th class="p-3">روستا</th><th class="p-3">جمع عوارض</th>
                    <th class="p-3">عوارض ساخت</th><th class="p-3">عوارض تراکم</th>
                    <th class="p-3 cursor-pointer select-none no-print" id="sortFinal" data-dir="desc">عوارض نهایی ▼</th>
                    <th class="p-3">نمودار مقایسه</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100" id="compareBody">
                @php $max = $files->max(fn($f) => $f->final_levy) ?: 1; @endphp
                @forelse($files as $i => $f)
                    <tr data-final="{{ $f->final_levy }}">
                        <td class="p-3 text-slate-500" dir="ltr">{{ \App\Support\Number::money($i + 1) }}</td>
                        <td class="p-3 font-semibold">{{ $f->village?->name }}</td>
                        <td class="p-3" dir="ltr">{{ \App\Support\Number::money($f->lines_total) }}</td>
                        <td class="p-3" dir="ltr">{{ \App\Support\Number::money($f->residential_levy) }}</td>
                        <td class="p-3" dir="ltr">{{ \App\Support\Number::money($f->density_levy) }}</td>
                        <td class="p-3 font-bold text-blue-800" dir="ltr">{{ \App\Support\Number::money($f->final_levy) }}</td>
                        <td class="p-3">
                            <div class="w-40 bg-slate-100 rounded-full h-3 overflow-hidden">
                                <div class="h-3 bg-blue-600 rounded-full" style="width: {{ max(2, round($f->final_levy / $max * 100)) }}%"></div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-center text-slate-500">پروندهای برای مقایسه وجود ندارد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        (function () {
            const btn = document.getElementById('sortFinal');
            const body = document.getElementById('compareBody');
            if (!btn || !body) return;
            btn.addEventListener('click', function () {
                const desc = this.dataset.dir === 'desc';
                const rows = Array.prototype.slice.call(body.querySelectorAll('tr'));
                rows.sort(function (a, b) {
                    const av = parseFloat(a.dataset.final || '0'), bv = parseFloat(b.dataset.final || '0');
                    return desc ? av - bv : bv - av;
                });
                rows.forEach(function (r) { body.appendChild(r); });
                this.dataset.dir = desc ? 'asc' : 'desc';
                this.textContent = desc ? 'عوارض نهایی ▲' : 'عوارض نهایی ▼';
            });
        })();
    </script>
</div>
@endsection

<div class="no-print bg-white rounded-lg shadow-sm border border-slate-200 px-4 py-3 mb-5">
    <div class="flex flex-wrap items-center gap-2">
        <span class="font-bold text-slate-700 ml-2">زیرسامانه عوارض احداث بنا</span>
        <a href="{{ route('admin.levy.dashboard') }}" class="px-3 py-1.5 rounded text-sm {{ request()->routeIs('admin.levy.dashboard') ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">داشبورد</a>
        <a href="{{ route('admin.levy.villages') }}" class="px-3 py-1.5 rounded text-sm {{ request()->routeIs('admin.levy.villages') ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">ضرایب روستاها</a>
        <a href="{{ route('admin.levy.accounts') }}" class="px-3 py-1.5 rounded text-sm {{ request()->routeIs('admin.levy.accounts') ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">حسابها</a>
        <a href="{{ route('admin.levy.files.index') }}" class="px-3 py-1.5 rounded text-sm {{ request()->routeIs('admin.levy.files.*') ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">پروندهها</a>
        <a href="{{ route('admin.levy.reports.all') }}" class="px-3 py-1.5 rounded text-sm {{ request()->routeIs('admin.levy.reports.all') ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">گزارش تجمیعی</a>
        <a href="{{ route('admin.levy.reports.compare') }}" class="px-3 py-1.5 rounded text-sm {{ request()->routeIs('admin.levy.reports.compare') ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">مقایسه روستاها</a>
        <a href="{{ route('admin.levy.reports.incomplete') }}" class="px-3 py-1.5 rounded text-sm {{ request()->routeIs('admin.levy.reports.incomplete') ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">اطلاعات ناقص</a>
        <a href="{{ route('admin.levy.export.excel') }}" class="px-3 py-1.5 rounded text-sm bg-blue-700 text-white hover:bg-blue-800 mr-auto">خروجی اکسل</a>
    </div>
</div>

@if(session('success'))
    <div class="bg-emerald-50 border-r-4 border-emerald-600 text-emerald-900 p-3 mb-4 rounded no-print">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="bg-rose-50 border-r-4 border-rose-600 text-rose-900 p-3 mb-4 rounded no-print">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="bg-rose-50 border-r-4 border-rose-600 text-rose-900 p-3 mb-4 rounded no-print">
        <ul class="list-disc pr-5 text-sm">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

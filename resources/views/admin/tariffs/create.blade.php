@extends(View::exists('layouts.admin') ? 'layouts.admin' : 'layouts.tariff-fallback')
@section('title', 'ساخت مجموعه تعرفه جدید')
@section('content')
<div class="p-4 md:p-6" dir="rtl">
    <h1 class="text-2xl font-bold mb-6">ساخت مجموعه تعرفه سالانه</h1>
    @if($errors->any())
        <div class="bg-red-50 border-r-4 border-red-500 text-red-800 p-3 mb-4 rounded">
            <ul class="list-disc pr-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif
    <form method="POST" action="{{ route('admin.tariffs.store') }}" class="bg-white rounded-lg shadow p-6 space-y-4 max-w-2xl">
        @csrf
        <div><label class="block mb-1 font-medium">روستا</label>
            <select name="village_id" required class="w-full border rounded p-2">
                <option value="">— انتخاب کنید —</option>
                @foreach($villages as $v)<option value="{{ $v->id }}" @selected(old('village_id')==$v->id)>{{ $v->name }}</option>@endforeach
            </select>
        </div>
        <div><label class="block mb-1 font-medium">سال شمسی</label>
            <input type="number" name="jalali_year" value="{{ old('jalali_year', $suggestedYear) }}" min="1404" max="1450" required class="w-full border rounded p-2 font-mono">
        </div>
        <div><label class="block mb-1 font-medium">کپی از سال قبل (اختیاری)</label>
            <select name="copy_from_id" class="w-full border rounded p-2">
                <option value="">— بدون کپی —</option>
                @foreach($previousSets as $p)<option value="{{ $p->id }}">{{ $p->village?->name }} — {{ \App\Support\Number::fa($p->jalali_year) }} ({{ $p->statusLabel() }})</option>@endforeach
            </select>
        </div>
        <div><label class="block mb-1 font-medium">نرخ تورم اعلامی (درصد)</label>
            <input type="number" step="0.01" name="inflation_rate" value="{{ old('inflation_rate', 40) }}" required class="w-full border rounded p-2">
        </div>
        <div class="flex gap-2 pt-2">
            <button class="bg-emerald-600 text-white px-6 py-2 rounded">ایجاد</button>
            <a href="{{ route('admin.tariffs.index') }}" class="px-6 py-2 rounded border">انصراف</a>
        </div>
    </form>
</div>
@endsection

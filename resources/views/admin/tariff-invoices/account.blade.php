@extends('admin.sections.layout')
@section('title', 'حساب بانکی دهیاری')

@php
    use App\Support\Number;
    $banks = \App\Models\BankAccount::bankOptions();
@endphp

@section('content')
<div class="max-w-2xl mx-auto" dir="rtl">

    <div class="mb-4">
        <a href="{{ route('admin.tariff-invoices.index') }}" class="text-emerald-700 text-sm">← لیست فیش‌ها</a>
    </div>

    <h1 class="text-2xl font-bold mb-4">🏦 حساب بانکی دهیاری</h1>

    <p class="text-sm text-gray-600 mb-4">
        این اطلاعات در فیش‌های عوارض نمایش داده می‌شود تا مودیان مبلغ را به این حساب واریز کنند.
    </p>

    @if(session('success'))
    <div class="bg-emerald-50 border-r-4 border-emerald-500 p-3 rounded mb-4 text-emerald-900 text-sm">✅ {{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border-r-4 border-red-500 p-3 rounded mb-4 text-red-800 text-sm">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    {{-- انتخاب روستا --}}
    @if($villages->count() > 1)
    <form method="GET" class="mb-4">
        <label class="block text-sm mb-1">انتخاب روستا:</label>
        <select name="village_id" onchange="this.form.submit()" class="border rounded p-2 w-full">
            @foreach($villages as $v)
            <option value="{{ $v->id }}" @selected($v->id == $villageId)>{{ $v->name }}</option>
            @endforeach
        </select>
    </form>
    @endif

    {{-- فرم حساب --}}
    <form method="POST" action="{{ route('admin.tariff-invoices.account.save') }}"
          class="bg-white rounded-lg shadow p-6 space-y-4">
        @csrf
        <input type="hidden" name="village_id" value="{{ $villageId }}">

        <div class="p-3 bg-blue-50 rounded text-sm">
            در حال ویرایش حساب بانکی روستای <b>{{ $village->name ?? '' }}</b>
        </div>

        <label class="block">
            <span class="text-sm font-medium">بانک *</span>
            <select name="bank_name" required class="w-full border rounded p-2 mt-1">
                <option value="">-- انتخاب کنید --</option>
                @foreach($banks as $key => $label)
                <option value="{{ $key }}" @selected(old('bank_name', $account->bank_name ?? '') === $key)>
                    {{ $label }}
                </option>
                @endforeach
            </select>
        </label>

        <label class="block">
            <span class="text-sm font-medium">صاحب حساب *</span>
            <input name="account_holder" required
                   value="{{ old('account_holder', $account->account_holder ?? 'دهیاری روستای ' . ($village->name ?? '')) }}"
                   class="w-full border rounded p-2 mt-1">
            <span class="text-xs text-gray-500">مثلاً: دهیاری روستای سبزوار</span>
        </label>

        <label class="block">
            <span class="text-sm font-medium">شماره شبا</span>
            <input name="iban" dir="ltr"
                   value="{{ old('iban', $account->iban ?? '') }}"
                   placeholder="IR + ۲۴ رقم"
                   maxlength="26"
                   class="w-full border rounded p-2 mt-1 font-mono">
        </label>

        <label class="block">
            <span class="text-sm font-medium">شماره کارت</span>
            <input name="card_number" dir="ltr"
                   value="{{ old('card_number', $account->card_number ?? '') }}"
                   placeholder="۱۶ رقم"
                   maxlength="19"
                   class="w-full border rounded p-2 mt-1 font-mono">
        </label>

        <label class="block">
            <span class="text-sm font-medium">شماره حساب</span>
            <input name="account_number" dir="ltr"
                   value="{{ old('account_number', $account->account_number ?? '') }}"
                   class="w-full border rounded p-2 mt-1 font-mono">
        </label>

        <label class="block">
            <span class="text-sm font-medium">توضیحات (اختیاری)</span>
            <textarea name="note" rows="2"
                      class="w-full border rounded p-2 mt-1"
                      placeholder="مثلاً: فقط در ساعات اداری واریز شود">{{ old('note', $account->note ?? '') }}</textarea>
        </label>

        <div class="pt-4 border-t flex gap-2">
            <button class="bg-emerald-700 text-white px-6 py-2 rounded">💾 ذخیره</button>
            <a href="{{ route('admin.tariff-invoices.index') }}" class="border px-6 py-2 rounded bg-white">انصراف</a>
        </div>
    </form>

</div>
@endsection

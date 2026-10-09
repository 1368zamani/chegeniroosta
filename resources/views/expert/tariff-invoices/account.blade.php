@extends('layouts.expert')
@section('title', 'حساب بانکی دهیاری')

@php
    use App\Support\Number;
    $banks = \App\Models\BankAccount::bankOptions();
@endphp

@section('content')

<div class="mb-3">
    <a href="{{ route('expert.tariff-invoices.index') }}" class="text-decoration-none">
        <i class="bi bi-arrow-right"></i> بازگشت به لیست فیش‌ها
    </a>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card card-expert">
            <div class="card-header">
                <strong>🏦 حساب بانکی دهیاری</strong>
                <div class="small text-muted mt-1">
                    این اطلاعات در فیش‌های عوارض به مودیان نمایش داده می‌شود.
                </div>
            </div>
            <div class="card-body">

                @if($villages->count() > 1)
                <form method="GET" class="mb-4">
                    <label class="form-label">انتخاب روستا:</label>
                    <select name="village_id" onchange="this.form.submit()" class="form-select">
                        @foreach($villages as $v)
                            <option value="{{ $v->id }}" @selected($v->id == $villageId)>{{ $v->name }}</option>
                        @endforeach
                    </select>
                </form>
                @endif

                <form method="POST" action="{{ route('expert.tariff-invoices.account.save') }}">
                    @csrf
                    <input type="hidden" name="village_id" value="{{ $villageId }}">

                    <div class="alert alert-info">
                        در حال ویرایش حساب روستای <b>{{ $village->name }}</b>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">بانک *</label>
                        <select name="bank_name" required class="form-select">
                            <option value="">-- انتخاب کنید --</option>
                            @foreach($banks as $key => $label)
                                <option value="{{ $key }}" @selected(old('bank_name', $account->bank_name ?? '') === $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">صاحب حساب *</label>
                        <input name="account_holder" required
                               value="{{ old('account_holder', $account->account_holder ?? 'دهیاری روستای ' . $village->name) }}"
                               class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">شماره شبا</label>
                        <input name="iban" dir="ltr" maxlength="26"
                               value="{{ old('iban', $account->iban ?? '') }}"
                               placeholder="IR + ۲۴ رقم"
                               class="form-control font-monospace">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">شماره کارت</label>
                        <input name="card_number" dir="ltr" maxlength="19"
                               value="{{ old('card_number', $account->card_number ?? '') }}"
                               class="form-control font-monospace">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">شماره حساب</label>
                        <input name="account_number" dir="ltr"
                               value="{{ old('account_number', $account->account_number ?? '') }}"
                               class="form-control font-monospace">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">توضیحات (اختیاری)</label>
                        <textarea name="note" rows="2" class="form-control">{{ old('note', $account->note ?? '') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-primary">💾 ذخیره</button>
                        <a href="{{ route('expert.tariff-invoices.index') }}" class="btn btn-outline-secondary">انصراف</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

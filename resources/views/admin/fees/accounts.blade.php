@extends('admin.fees.layout')
@section('title', 'حساب‌های بانکی')

@section('content')
<div class="card">
    <p class="muted">حساب‌های فعال در صفحه قبض به کاربر نشان داده می‌شوند. قبل از تأیید قبض، حساب را کامل کنید.</p>
</div>

<div class="card">
    <table>
        <thead><tr>
            <th>دهیاری</th><th>بانک</th><th>شماره حساب</th><th>کارت</th><th>شبا</th><th>صاحب حساب</th><th>فعال</th><th></th>
        </tr></thead>
        <tbody>
        @foreach($accounts as $a)
            <form method="POST" action="{{ route('admin.fees.accounts.update', $a->id) }}" id="ac-{{ $a->id }}">
                @csrf @method('PUT')
                <tr>
                    <td>{{ $a->dehyari?->name }}</td>
                    <td><input type="text" name="bank_name" value="{{ $a->bank_name }}" style="width:100px"></td>
                    <td><input type="text" name="account_number" value="{{ $a->account_number }}" style="width:160px" class="mono"></td>
                    <td><input type="text" name="card_number" value="{{ $a->card_number }}" style="width:140px" class="mono"></td>
                    <td><input type="text" name="sheba" value="{{ $a->sheba }}" style="width:180px" class="mono"></td>
                    <td><input type="text" name="account_holder" value="{{ $a->account_holder }}" style="width:120px"></td>
                    <td><input type="checkbox" name="is_active" value="1" @checked($a->is_active)></td>
                    <td><button type="submit" class="btn btn-green">ذخیره</button></td>
                </tr>
            </form>
        @endforeach
        </tbody>
    </table>
    <div style="margin-top:14px">{{ $accounts->links() }}</div>
</div>
@endsection

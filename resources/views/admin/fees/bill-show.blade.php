@extends('admin.fees.layout')
@section('title', 'قبض '.$bill->bill_number)

@section('content')
<div class="card">
    <h3 style="margin:0 0 12px;font-size:15px;color:#1565c0">
        🧾 قبض <span class="mono">{{ $bill->bill_number }}</span>
        <span class="pill pill-{{ $bill->status }}">{{ \App\Models\Bill::statusLabel($bill->status) }}</span>
    </h3>

    <div class="form-row">
        <div><label>مؤدی</label><div>{{ $bill->taxpayer_name }}</div></div>
        <div><label>موبایل</label><div class="mono">{{ $bill->mobile }}</div></div>
        <div><label>کد ملی</label><div class="mono">{{ $bill->national_code ?: '—' }}</div></div>
        <div><label>دهیاری</label><div>{{ $bill->dehyari?->name }}</div></div>
        <div><label>نوع عوارض</label><div>{{ $bill->fee_type_name }}</div></div>
        <div><label>مبلغ</label><div class="mono">{{ number_format($bill->amount_rial) }} ریال</div></div>
        <div><label>پرداخت‌شده</label><div class="mono">{{ number_format($bill->amount_paid) }} ریال</div></div>
        <div><label>تاریخ صدور</label><div class="mono">{{ $bill->issue_date }}</div></div>
    </div>

    <h4 style="margin:16px 0 8px;color:#1565c0;font-size:14px">تفکیک محاسبه</h4>
    <table>
        <thead><tr><th>شرح</th><th>جزئیات</th><th>مبلغ (ریال)</th></tr></thead>
        <tbody>
        @foreach($bill->items as $it)
            <tr><td>{{ $it->title }}</td><td class="muted">{{ $it->description }}</td><td class="mono">{{ number_format($it->amount_rial) }}</td></tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="card">
    <h3 style="margin:0 0 12px;font-size:15px;color:#1565c0">💳 پرداخت‌های مرتبط</h3>
    <table>
        <thead><tr><th>#</th><th>شماره پیگیری بانکی</th><th>مبلغ</th><th>تاریخ واریز</th><th>وضعیت</th><th>عکس فیش</th><th>عملیات</th></tr></thead>
        <tbody>
        @forelse($payments as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td class="mono">{{ $p->bank_reference }}</td>
                <td class="mono">{{ number_format($p->amount_rial) }}</td>
                <td class="mono">{{ $p->paid_at }}</td>
                <td><span class="pill pill-{{ $p->status }}">{{ $p->status }}</span></td>
                <td>
                    @foreach($p->receipts as $r)
                        <a href="{{ route('admin.fees.receipt', $r->id) }}" target="_blank" class="btn btn-blue">مشاهده فیش</a>
                    @endforeach
                </td>
                <td>
                    @if($p->status === 'pending_review')
                        <form method="POST" action="{{ route('admin.fees.payments.approve', $p->id) }}" style="display:inline">
                            @csrf <button class="btn btn-green">تأیید</button>
                        </form>
                        <form method="POST" action="{{ route('admin.fees.payments.reject', $p->id) }}" style="display:inline">
                            @csrf <input type="text" name="review_note" placeholder="دلیل رد" required style="width:130px;display:inline-block">
                            <button class="btn btn-red">رد</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center;color:#607d8b;padding:16px">پرداختی ثبت نشده.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection

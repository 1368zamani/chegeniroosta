@extends('admin.fees.layout')
@section('title', 'پرداخت‌ها')

@section('content')
<div class="card">
    <form method="GET" class="form-row">
        <div>
            <label>جستجو</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="کد پیگیری، مؤدی، موبایل، شماره پیگیری بانکی">
        </div>
        <div>
            <label>وضعیت</label>
            <select name="status">
                <option value="">همه</option>
                <option value="pending_review" @selected(request('status')=='pending_review')>در انتظار تأیید</option>
                <option value="approved" @selected(request('status')=='approved')>تأییدشده</option>
                <option value="rejected" @selected(request('status')=='rejected')>رد‌شده</option>
            </select>
        </div>
        <div style="display:flex;align-items:flex-end"><button class="btn btn-blue">اعمال فیلتر</button></div>
    </form>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>کد پیگیری قبض</th>
                <th>مؤدی / موبایل</th>
                <th>شماره پیگیری بانکی</th>
                <th>مبلغ (ریال)</th>
                <th>تاریخ واریز</th>
                <th>وضعیت</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
        @forelse($payments as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>
                    <a href="{{ route('admin.fees.bills.show', $p->bill_id) }}" class="mono" style="color:#1565c0">
                        {{ $p->bill?->bill_number }}
                    </a>
                </td>
                <td>
                    {{ $p->bill?->taxpayer_name }}<br>
                    <span class="muted mono">{{ $p->bill?->mobile }}</span>
                </td>
                <td class="mono">{{ $p->bank_reference }}</td>
                <td class="mono">{{ number_format($p->amount_rial) }}</td>
                <td class="mono">{{ $p->paid_at ? \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($p->paid_at))->format('Y/m/d') : '-' }}</td>
                <td><span class="pill pill-{{ $p->status }}">{{ ['pending_review'=>'در انتظار','approved'=>'تأیید','rejected'=>'رد'][$p->status] ?? $p->status }}</span></td>
                <td>
                    @if($p->status === 'pending_review')
                        <button type="button" class="btn btn-green" onclick="document.getElementById('app-{{ $p->id }}').style.display='block'">✅ تأیید</button>
                        <button type="button" class="btn btn-red" onclick="document.getElementById('rej-{{ $p->id }}').style.display='block'">❌ رد</button>

                        <form id="app-{{ $p->id }}" method="POST" action="{{ route('admin.fees.payments.approve', $p->id) }}" style="display:none;margin-top:8px;background:#e8f5e9;padding:10px;border-radius:8px">
                            @csrf
                            <label>یادداشت (اختیاری)</label>
                            <input type="text" name="review_note" maxlength="500">
                            <button class="btn btn-green" style="margin-top:6px">تأیید قطعی</button>
                        </form>

                        <form id="rej-{{ $p->id }}" method="POST" action="{{ route('admin.fees.payments.reject', $p->id) }}" style="display:none;margin-top:8px;background:#ffebee;padding:10px;border-radius:8px">
                            @csrf
                            <label>دلیل رد (الزامی)</label>
                            <input type="text" name="review_note" required maxlength="500">
                            <button class="btn btn-red" style="margin-top:6px">رد قطعی</button>
                        </form>
                    @else
                        <span class="muted">
                            {{ $p->reviewer?->name ?? '-' }}<br>
                            {{ $p->reviewed_at ? \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::parse($p->reviewed_at))->format('Y/m/d') : '' }}
                        </span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="8" style="text-align:center;color:#607d8b;padding:20px">پرداختی یافت نشد.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div style="margin-top:14px">{{ $payments->links() }}</div>
</div>
@endsection

@extends('admin.fees.layout')
@section('title', 'قبوض')

@section('content')
<div class="card">
    <form method="GET" class="form-row">
        <div><label>جستجو</label><input type="text" name="q" value="{{ request('q') }}" placeholder="کد پیگیری، نام، موبایل، کد ملی"></div>
        <div>
            <label>وضعیت</label>
            <select name="status">
                <option value="">همه</option>
                @foreach(\App\Models\Bill::statuses() as $k=>$v)
                    <option value="{{ $k }}" @selected(request('status')==$k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex;align-items:flex-end;gap:8px">
            <button class="btn btn-blue">اعمال</button>
            <a href="{{ route('admin.fees.export.bills') }}" class="btn btn-green">📥 CSV</a>
        </div>
    </form>
</div>

<div class="card">
    <table>
        <thead><tr>
            <th>#</th><th>کد پیگیری</th><th>دهیاری</th><th>مؤدی</th><th>نوع عوارض</th><th>مبلغ (ریال)</th><th>وضعیت</th><th>تاریخ</th><th></th>
        </tr></thead>
        <tbody>
        @forelse($bills as $b)
            <tr>
                <td>{{ $b->id }}</td>
                <td class="mono">{{ $b->bill_number }}</td>
                <td>{{ $b->dehyari?->name }}</td>
                <td>{{ $b->taxpayer_name }}<br><span class="muted mono">{{ $b->mobile }}</span></td>
                <td>{{ $b->fee_type_name }}</td>
                <td class="mono">{{ number_format($b->amount_rial) }}</td>
                <td><span class="pill pill-{{ $b->status }}">{{ \App\Models\Bill::statusLabel($b->status) }}</span></td>
                <td class="mono">{{ $b->issue_date }}</td>
                <td><a href="{{ route('admin.fees.bills.show', $b->id) }}" class="btn btn-blue">مشاهده</a></td>
            </tr>
        @empty
            <tr><td colspan="9" style="text-align:center;color:#607d8b;padding:20px">قبضی یافت نشد.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div style="margin-top:14px">{{ $bills->links() }}</div>
</div>
@endsection

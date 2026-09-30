@extends('admin.fees.layout')
@section('title', 'گزارش درآمد')

@section('content')
<div class="card">
    <form method="GET" class="form-row">
        <div><label>از تاریخ</label><input type="date" name="from" value="{{ $from->format('Y-m-d') }}"></div>
        <div><label>تا تاریخ</label><input type="date" name="to" value="{{ $to->format('Y-m-d') }}"></div>
        <div style="display:flex;align-items:flex-end"><button class="btn btn-blue">اعمال</button></div>
    </form>
</div>

<div class="stats">
    <div class="stat"><span class="num">{{ number_format($totalIncome) }}</span><span class="lbl">💰 مجموع درآمد (ریال)</span></div>
    <div class="stat"><span class="num">{{ number_format($countPaid) }}</span><span class="lbl">✅ قبوض پرداخت‌شده</span></div>
    <div class="stat"><span class="num">{{ number_format($totalPending) }}</span><span class="lbl">⏳ فیش در انتظار</span></div>
    <div class="stat"><span class="num">{{ number_format($pendingAmount) }}</span><span class="lbl">مبلغ در انتظار (ریال)</span></div>
</div>

<div class="card">
    <h3 style="margin:0 0 12px;font-size:15px;color:#1565c0">🏆 درآمد به تفکیک دهیاری (۲۰ دهیاری برتر)</h3>
    <table>
        <thead><tr><th>#</th><th>دهیاری</th><th>تعداد قبض</th><th>مبلغ (ریال)</th></tr></thead>
        <tbody>
        @forelse($byDehyari as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row->dehyari?->village?->name ?? $row->dehyari?->name }}</td>
                <td class="mono">{{ $row->cnt }}</td>
                <td class="mono">{{ number_format($row->total) }}</td>
            </tr>
        @empty
            <tr><td colspan="4" style="text-align:center;color:#607d8b;padding:18px">داده‌ای نیست.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection

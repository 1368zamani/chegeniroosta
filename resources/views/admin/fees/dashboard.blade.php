@extends('admin.fees.layout')
@section('title', 'داشبورد عوارض')

@section('content')
<div class="stats">
    <div class="stat"><span class="num">{{ number_format($stats['pending_payments']) }}</span><span class="lbl">💳 فیش در انتظار تأیید</span></div>
    <div class="stat"><span class="num">{{ number_format($stats['unpaid_bills']) }}</span><span class="lbl">🧾 قبوض پرداخت‌نشده</span></div>
    <div class="stat"><span class="num">{{ number_format($stats['paid_bills']) }}</span><span class="lbl">✅ قبوض پرداخت‌شده</span></div>
    <div class="stat"><span class="num">{{ number_format($stats['total_income']) }}</span><span class="lbl">💰 جمع دریافتی (ریال)</span></div>
    <div class="stat"><span class="num">{{ number_format($stats['active_dehyaris']) }}</span><span class="lbl">🏘 دهیاری فعال</span></div>
</div>

<div class="card">
    <h3 style="margin:0 0 12px;font-size:15px;color:#1565c0">دسترسی سریع</h3>
    <a href="{{ route('admin.fees.payments.index', ['status'=>'pending_review']) }}" class="btn btn-green">💳 بررسی فیش‌های در انتظار</a>
    <a href="{{ route('admin.fees.export.bills') }}" class="btn btn-blue">📥 خروجی CSV قبوض</a>
    <a href="{{ route('admin.fees.export.payments') }}" class="btn btn-blue">📥 خروجی CSV پرداخت‌ها</a>
</div>
@endsection

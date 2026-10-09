@extends('layouts.expert')
@section('title', "فیش {$invoice->tracking_code}")

@php
    use App\Support\Number;
    use Morilog\Jalali\Jalalian;
    $fa = fn ($n) => Number::fa(number_format((int) $n));
    $user = auth()->user();
    $canApprove = in_array($user->role, ['super_admin', 'section_admin'])
        || ($user->role === 'fee_officer' && $user->officer_type === 'financial');
    $canCancel = in_array($user->role, ['super_admin', 'section_admin']);
    $canDelete = $user->role === 'super_admin';
    $plan = $invoice->installment_plan;
@endphp

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('expert.tariff-invoices.index') }}" class="text-decoration-none">
        <i class="bi bi-arrow-right"></i> بازگشت به لیست
    </a>
    <a href="{{ route('village.tariff.invoice.show', $invoice->public_token) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
        👁️ نمایش عمومی
    </a>
</div>

{{-- اطلاعات اصلی --}}
<div class="card card-expert mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <strong>📄 فیش {{ $invoice->tracking_code }}</strong>
            <div class="small text-muted">
                روستای {{ $invoice->village->name }}
                · تعرفه سال {{ Number::fa($invoice->set->jalali_year ?? '') }}
            </div>
        </div>
        <span class="badge {{ $invoice->statusColor() }}">{{ $invoice->statusLabel() }}</span>
    </div>

    <div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col-md-6">پرداخت‌کننده: <b>{{ $invoice->payer_name }}</b></div>
            @if($invoice->payer_national_id)
                <div class="col-md-6">کد ملی: <b class="font-monospace">{{ Number::fa($invoice->payer_national_id) }}</b></div>
            @endif
            <div class="col-md-6">موبایل: <b class="font-monospace">{{ Number::fa($invoice->payer_mobile) }}</b></div>
            @if($invoice->issued_at)
                <div class="col-md-6">تاریخ صدور: <b>{{ Number::fa(Jalalian::fromCarbon($invoice->issued_at)->format('Y/m/d H:i')) }}</b></div>
            @endif
            @if($invoice->due_date)
                <div class="col-md-6">مهلت: <b class="{{ $invoice->isOverdue() ? 'text-danger' : '' }}">{{ Number::fa(Jalalian::fromCarbon($invoice->due_date)->format('Y/m/d')) }}</b></div>
            @endif
            @if($invoice->subject)
                <div class="col-md-12">موضوع: <b>{{ $invoice->subject }}</b></div>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">#</th>
                        <th>عنوان</th>
                        <th class="text-end">مبلغ قبل</th>
                        <th class="text-center">تخفیف</th>
                        <th class="text-end">قابل پرداخت</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->lines as $i => $l)
                    <tr>
                        <td class="text-center">{{ Number::fa($i + 1) }}</td>
                        <td>{{ $l->title_name }}</td>
                        <td class="text-end font-monospace">{{ $fa($l->amount_before) }}</td>
                        <td class="text-center">{{ (float) $l->discount_percent > 0 ? Number::fa((int) $l->discount_percent) . '٪' : '—' }}</td>
                        <td class="text-end font-monospace fw-bold">{{ $fa($l->amount) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-success">
                    <tr>
                        <th colspan="4" class="text-end">جمع کل (ریال):</th>
                        <th class="text-end font-monospace fs-5">{{ $fa($invoice->total) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($invoice->review_note)
        <div class="alert alert-info mt-3 mb-0">
            <b>توضیح کارشناس:</b> {{ $invoice->review_note }}
        </div>
        @endif
    </div>
</div>

{{-- رسیدها --}}
<div class="card card-expert mb-3">
    <div class="card-header">
        <strong>📎 رسیدهای بارگذاری‌شده</strong>
    </div>
    <div class="card-body">
        @if($invoice->receipts->isEmpty())
            <p class="text-muted mb-0">هنوز رسیدی بارگذاری نشده است.</p>
        @else
            @foreach($invoice->receipts as $r)
            <div class="border rounded p-3 mb-3 
                {{ $r->status === 'approved' ? 'bg-light border-success' : '' }}
                {{ $r->status === 'rejected' ? 'bg-light border-danger' : '' }}
                {{ $r->status === 'pending' ? 'bg-light border-warning' : '' }}">

                <div class="d-flex justify-content-between mb-2">
                    <span class="badge {{ $r->statusColor() }}">{{ $r->statusLabel() }}</span>
                    <small class="text-muted">{{ Number::fa($r->created_at?->format('Y/m/d H:i')) }}</small>
                </div>

                <div class="row g-2 small mb-2">
                    <div class="col-md-3">مبلغ: <b class="font-monospace">{{ $fa($r->paid_amount) }}</b></div>
                    <div class="col-md-3">تاریخ پرداخت: <b>{{ Number::fa($r->paid_at_jalali ?? '-') }}</b></div>
                    @if($r->bank_ref_no)
                        <div class="col-md-3">شماره پیگیری: <b class="font-monospace">{{ Number::fa($r->bank_ref_no) }}</b></div>
                    @endif
                    @if($r->installment_no !== null)
                        <div class="col-md-3">قسط: <b>{{ $r->installment_no == 0 ? 'پیش‌پرداخت' : Number::fa($r->installment_no) }}</b></div>
                    @endif
                </div>

                @if($r->fileUrl())
                    <a href="{{ $r->fileUrl() }}" target="_blank" class="btn btn-sm btn-outline-primary mb-2">
                        📎 مشاهده رسید
                    </a>
                @endif

                @php
                    $isPending = $r->status === 'pending';
                    $isFinancial = $user->role === 'fee_officer' && $user->officer_type === 'financial';
                    $isAdmin = in_array($user->role, ['super_admin', 'section_admin']);
                    $showApprove = $isPending && ($isFinancial || $isAdmin);
                @endphp

                @if($showApprove)
                <div class="d-flex gap-2 flex-wrap mt-3 pt-3 border-top">
                    <form method="POST" action="{{ route('expert.tariff-invoices.receipts.approve', $r->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            ✅ تایید و علامت پرداخت
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger" onclick="showRejectForm({{ $r->id }})">
                        ❌ رد کردن
                    </button>
                </div>
                @endif

                <div id="rejectForm-{{ $r->id }}" class="d-none mt-2">
                    <form method="POST" action="{{ route('expert.tariff-invoices.receipts.reject', $r->id) }}">
                        @csrf
                        <textarea name="reject_reason" required rows="2" class="form-control mb-2"
                                  placeholder="دلیل رد رسید"></textarea>
                        <button class="btn btn-danger btn-sm">ارسال رد</button>
                    </form>
                </div>

                @if($r->status === 'rejected' && $r->reject_reason)
                    <div class="text-danger small mt-2">
                        <b>دلیل رد:</b> {{ $r->reject_reason }}
                    </div>
                @endif

                @if($r->status === 'approved' && $r->reviewedBy)
                    <div class="text-success small mt-2">
                        تایید توسط {{ $r->reviewedBy->name }}
                    </div>
                @endif
            </div>
            @endforeach
        @endif
    </div>
</div>

{{-- عملیات --}}
@if($canCancel && !in_array($invoice->status, ['cancelled', 'paid']))
<div class="card card-expert mb-3">
    <div class="card-header"><strong>🔧 ابطال فیش</strong></div>
    <div class="card-body">
        <form method="POST" action="{{ route('expert.tariff-invoices.cancel', $invoice->id) }}"
              onsubmit="return confirm('آیا مطمئن هستید؟')">
            @csrf
            <textarea name="review_note" required rows="2" class="form-control mb-2"
                      placeholder="دلیل ابطال"></textarea>
            <button class="btn btn-warning">🚫 ابطال فیش</button>
        </form>
    </div>
</div>
@endif

@if($invoice->status === 'cancelled' && $canCancel)
<div class="card card-expert mb-3">
    <div class="card-body">
        <form method="POST" action="{{ route('expert.tariff-invoices.restore', $invoice->id) }}">
            @csrf
            <button class="btn btn-success">♻️ بازگردانی فیش</button>
        </form>
    </div>
</div>
@endif

@if($canDelete && $invoice->status !== 'paid')
<div class="card border-danger mb-3">
    <div class="card-header bg-danger text-white"><strong>⚠️ منطقه خطرناک</strong></div>
    <div class="card-body">
        <form method="POST" action="{{ route('expert.tariff-invoices.destroy', $invoice->id) }}"
              onsubmit="return confirm('حذف دائمی؟ این عملیات بازگشت‌ناپذیر است!')">
            @csrf @method('DELETE')
            <button class="btn btn-danger">🗑️ حذف دائمی</button>
        </form>
    </div>
</div>
@endif

<script>
function showRejectForm(id) {
    document.getElementById('rejectForm-' + id).classList.toggle('d-none');
}
</script>

@endsection

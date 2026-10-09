@extends('admin.sections.layout')
@section('title', "فیش {$invoice->tracking_code}")

@php
    use App\Support\Number;
    use Morilog\Jalali\Jalalian;
    $fa = fn ($n) => Number::fa(number_format((int) $n));
    $user = auth()->user();
    $canApprove = in_array($user->role, ['super_admin', 'section_admin', 'village_admin'])
        || ($user->role === 'fee_officer' && $user->officer_type === 'financial');
    $canCancel = in_array($user->role, ['super_admin', 'section_admin', 'village_admin']);
    $canDelete = $user->role === 'super_admin';
    $plan = $invoice->installment_plan;
@endphp

@section('content')
<div class="max-w-4xl mx-auto" dir="rtl">

    {{-- هدر --}}
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('admin.tariff-invoices.index') }}" class="text-emerald-700 text-sm">← بازگشت به لیست</a>
        <div class="flex gap-2">
            <a href="{{ route('village.tariff.invoice.show', $invoice->public_token) }}" target="_blank"
               class="text-sm border px-3 py-2 rounded bg-white">👁️ نمایش عمومی</a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border-r-4 border-emerald-500 p-3 rounded mb-4 text-emerald-900 text-sm">✅ {{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border-r-4 border-red-500 p-3 rounded mb-4 text-red-800 text-sm">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    {{-- اطلاعات اصلی --}}
    <div class="bg-white rounded-lg shadow p-6 mb-4">
        <div class="flex flex-wrap justify-between gap-4 border-b pb-4 mb-4">
            <div>
                <h1 class="text-xl font-bold text-emerald-800">📄 فیش {{ $invoice->tracking_code }}</h1>
                <p class="text-sm text-gray-600 mt-1">
                    روستای {{ $invoice->village->name }}
                    · تعرفه سال {{ Number::fa($invoice->set->jalali_year ?? '') }}
                </p>
            </div>
            <div class="text-left">
                <div class="text-xs text-gray-500 mb-1">وضعیت</div>
                <span class="inline-block px-3 py-1 rounded text-sm {{ $invoice->statusColor() }}">
                    {{ $invoice->statusLabel() }}
                </span>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-x-6 gap-y-3 text-sm">
            <div>پرداخت‌کننده: <b>{{ $invoice->payer_name }}</b></div>
            @if($invoice->payer_national_id)
            <div>کد ملی: <b class="font-mono">{{ Number::fa($invoice->payer_national_id) }}</b></div>
            @endif
            <div>موبایل: <b class="font-mono" dir="ltr">{{ Number::fa($invoice->payer_mobile) }}</b></div>
            @if($invoice->issued_at)
            <div>تاریخ صدور: <b>{{ Number::fa(Jalalian::fromCarbon($invoice->issued_at)->format('Y/m/d H:i')) }}</b></div>
            @endif
            @if($invoice->due_date)
            <div>مهلت: <b class="{{ $invoice->isOverdue() ? 'text-red-700' : '' }}">{{ Number::fa(Jalalian::fromCarbon($invoice->due_date)->format('Y/m/d')) }}</b></div>
            @endif
            @if($invoice->subject)
            <div>موضوع: <b>{{ $invoice->subject }}</b></div>
            @endif
        </div>

        {{-- جدول ردیف‌ها --}}
        <div class="overflow-x-auto mt-5">
            <table class="w-full text-right text-sm border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 w-12">#</th>
                        <th class="p-2">عنوان</th>
                        <th class="p-2 w-32">مبلغ قبل</th>
                        <th class="p-2 w-20">تخفیف</th>
                        <th class="p-2 w-32">قابل پرداخت</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->lines as $i => $l)
                    <tr class="border-t">
                        <td class="p-2 text-center">{{ Number::fa($i + 1) }}</td>
                        <td class="p-2">{{ $l->title_name }}</td>
                        <td class="p-2 font-mono">{{ $fa($l->amount_before) }}</td>
                        <td class="p-2 text-center">{{ (float) $l->discount_percent > 0 ? Number::fa((int) $l->discount_percent) . '٪' : '—' }}</td>
                        <td class="p-2 font-mono font-bold">{{ $fa($l->amount) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t font-bold bg-emerald-50">
                        <td colspan="4" class="p-3 text-left">جمع کل (ریال):</td>
                        <td class="p-3 font-mono text-lg text-emerald-800">{{ $fa($invoice->total) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($invoice->review_note)
        <div class="mt-4 bg-blue-50 border-r-4 border-blue-500 p-3 rounded text-sm">
            <b>توضیح کارشناس:</b> {{ $invoice->review_note }}
        </div>
        @endif
    </div>

    {{-- ═══ رسیدها ═══ --}}
    <div class="bg-white rounded-lg shadow p-6 mb-4">
        <h2 class="font-bold mb-4">📎 رسیدهای بارگذاری‌شده</h2>

        @if($invoice->receipts->isEmpty())
        <p class="text-gray-500 text-sm">هنوز رسیدی بارگذاری نشده است.</p>
        @else
        <div class="space-y-3">
            @foreach($invoice->receipts as $r)
            <div class="border rounded p-4 
                {{ $r->status === 'approved' ? 'bg-emerald-50 border-emerald-200' : '' }}
                {{ $r->status === 'rejected' ? 'bg-red-50 border-red-200' : '' }}
                {{ $r->status === 'pending' ? 'bg-amber-50 border-amber-200' : '' }}">

                <div class="flex flex-wrap justify-between gap-2 mb-2">
                    <span class="px-2 py-1 rounded text-xs {{ $r->statusColor() }}">
                        {{ $r->statusLabel() }}
                    </span>
                    <span class="text-xs text-gray-500">
                        {{ Number::fa($r->created_at?->format('Y/m/d H:i')) }}
                    </span>
                </div>

                <div class="grid md:grid-cols-4 gap-2 text-sm">
                    <div>مبلغ: <b class="font-mono">{{ $fa($r->paid_amount) }}</b></div>
                    <div>تاریخ پرداخت: <b>{{ Number::fa($r->paid_at_jalali ?? '-') }}</b></div>
                    @if($r->bank_ref_no)
                    <div>شماره پیگیری: <b class="font-mono">{{ Number::fa($r->bank_ref_no) }}</b></div>
                    @endif
                    @if($r->installment_no !== null)
                    <div>قسط: <b>{{ $r->installment_no == 0 ? 'پیش‌پرداخت' : Number::fa($r->installment_no) }}</b></div>
                    @endif
                </div>

                @if($r->fileUrl())
                <div class="mt-3">
                    <a href="{{ $r->fileUrl() }}" target="_blank"
                       class="inline-block bg-blue-600 text-white text-xs px-3 py-1 rounded">
                        📎 مشاهده رسید
                    </a>
                </div>
                @endif

                @if($r->status === 'pending' && $canApprove)
                <div class="mt-3 pt-3 border-t flex flex-wrap gap-2">
                    <form method="POST" action="{{ route('admin.tariff-invoices.receipts.approve', $r->id) }}" class="inline">
                        @csrf
                        <button class="bg-emerald-600 text-white px-4 py-2 rounded text-sm hover:bg-emerald-700">
                            ✅ تایید و علامت پرداخت
                        </button>
                    </form>

                    <button type="button" onclick="showRejectForm({{ $r->id }})"
                            class="bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700">
                        ❌ رد کردن
                    </button>
                </div>

                {{-- فرم رد (مخفی) --}}
                <div id="rejectForm-{{ $r->id }}" class="hidden mt-3 pt-3 border-t">
                    <form method="POST" action="{{ route('admin.tariff-invoices.receipts.reject', $r->id) }}">
                        @csrf
                        <label class="block text-sm mb-2">
                            <span class="font-medium">دلیل رد رسید:</span>
                            <textarea name="reject_reason" required rows="2"
                                      class="w-full border rounded p-2 mt-1"
                                      placeholder="مثلاً: مبلغ واریزی با فیش مطابقت ندارد"></textarea>
                        </label>
                        <button class="bg-red-600 text-white px-4 py-2 rounded text-sm">ارسال رد</button>
                    </form>
                </div>
                @endif

                @if($r->status === 'rejected' && $r->reject_reason)
                <div class="mt-2 text-xs text-red-700">
                    <b>دلیل رد:</b> {{ $r->reject_reason }}
                    @if($r->reviewedBy)
                    <span class="text-gray-500 mr-2">— توسط {{ $r->reviewedBy->name }}</span>
                    @endif
                </div>
                @endif

                @if($r->status === 'approved' && $r->reviewedBy)
                <div class="mt-2 text-xs text-emerald-700">
                    تایید توسط {{ $r->reviewedBy->name }}
                    در {{ $r->reviewed_at ? Number::fa(Jalalian::fromCarbon($r->reviewed_at)->format('Y/m/d H:i')) : '' }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ═══ عملیات ادمین ═══ --}}
    @if($canCancel && !in_array($invoice->status, ['cancelled', 'paid']))
    <div class="bg-white rounded-lg shadow p-6 mb-4">
        <h2 class="font-bold mb-3">🔧 عملیات</h2>

        <form method="POST" action="{{ route('admin.tariff-invoices.cancel', $invoice->id) }}"
              onsubmit="return confirm('آیا از ابطال این فیش مطمئن هستید؟')">
            @csrf
            <div class="mb-3">
                <label class="block text-sm mb-2">
                    <span class="font-medium">دلیل ابطال:</span>
                    <textarea name="review_note" required rows="2"
                              class="w-full border rounded p-2 mt-1"
                              placeholder="مثلاً: به درخواست مودی"></textarea>
                </label>
            </div>
            <div class="flex flex-wrap gap-2">
                <button class="bg-amber-600 text-white px-4 py-2 rounded text-sm">
                    🚫 ابطال فیش
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- بازگردانی --}}
    @if($invoice->status === 'cancelled' && in_array($user->role, ['super_admin', 'section_admin']))
    <div class="bg-white rounded-lg shadow p-6 mb-4">
        <form method="POST" action="{{ route('admin.tariff-invoices.restore', $invoice->id) }}">
            @csrf
            <button class="bg-emerald-600 text-white px-4 py-2 rounded text-sm">
                ♻️ بازگردانی فیش به حالت صادرشده
            </button>
        </form>
    </div>
    @endif

    {{-- حذف (فقط Super Admin) --}}
    @if($canDelete && $invoice->status !== 'paid')
    <div class="bg-white rounded-lg shadow p-6 mb-4 border border-red-200">
        <h2 class="font-bold mb-3 text-red-700">⚠️ منطقه خطرناک</h2>
        <form method="POST" action="{{ route('admin.tariff-invoices.destroy', $invoice->id) }}"
              onsubmit="return confirm('آیا از حذف دائمی این فیش مطمئن هستید؟ این عملیات بازگشت‌ناپذیر است!')">
            @csrf
            @method('DELETE')
            <button class="bg-red-600 text-white px-4 py-2 rounded text-sm">
                🗑️ حذف دائمی فیش
            </button>
        </form>
    </div>
    @endif

</div>

<script>
function showRejectForm(id) {
    document.getElementById('rejectForm-' + id).classList.toggle('hidden');
}
</script>
@endsection

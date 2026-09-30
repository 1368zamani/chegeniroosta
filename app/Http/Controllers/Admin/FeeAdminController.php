<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Dehyari;
use App\Models\Payment;
use App\Models\PaymentAccount;
use App\Models\FeeParameter;
use App\Models\FeeTariff;
use App\Models\FeeType;
use App\Models\Section;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeeAdminController extends Controller
{
    private function authorizeAdmin(): void
    {
        $u = auth()->user();
        if (!$u || (!$u->isSuperAdmin() && !$u->isSectionAdmin() && !$u->isVillageAdmin() && !$u->isFeeOfficer())) {
            abort(403, 'دسترسی ندارید.');
        }
    }

    /** لیست دهیاری‌های در دسترس کاربر بر اساس نقش */
    private function scopedDehyariIds()
    {
        $u = auth()->user();

        if ($u->isSuperAdmin()) {
            return Dehyari::pluck('id');
        }

        if ($u->isSectionAdmin()) {
            $villageIds = Village::where('section_id', $u->section_id)->pluck('id');
            return Dehyari::whereIn('village_id', $villageIds)->pluck('id');
        }

        if ($u->isVillageAdmin() && $u->village_id) {
            return Dehyari::where('village_id', $u->village_id)->pluck('id');
        }

        if ($u->isFeeOfficer() && $u->section_id) {
            $villageIds = Village::where('section_id', $u->section_id)->pluck('id');
            return Dehyari::whereIn('village_id', $villageIds)->pluck('id');
        }

        return collect();
    }

    /* ═══════════════ داشبورد ═══════════════ */
    public function dashboard()
    {
        $this->authorizeAdmin();
        $dehyariIds = $this->scopedDehyariIds();

        $stats = [
            'pending_payments' => Payment::whereHas('bill', fn($q) => $q->whereIn('dehyari_id', $dehyariIds))
                ->where('status', 'pending_review')->count(),
            'unpaid_bills'     => Bill::whereIn('dehyari_id', $dehyariIds)->where('status', 'unpaid')->count(),
            'paid_bills'       => Bill::whereIn('dehyari_id', $dehyariIds)->where('status', 'paid')->count(),
            'total_income'     => Bill::whereIn('dehyari_id', $dehyariIds)->where('status', 'paid')->sum('amount_rial'),
            'active_dehyaris'  => Dehyari::whereIn('id', $dehyariIds)->where('is_active', true)->count(),
        ];

        return view('admin.fees.dashboard', compact('stats'));
    }

    /* ═══════════════ فهرست قبوض ═══════════════ */
    public function bills(Request $request)
    {
        $this->authorizeAdmin();
        $dehyariIds = $this->scopedDehyariIds();

        $query = Bill::with(['dehyari', 'items'])->whereIn('dehyari_id', $dehyariIds);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('bill_number', 'like', "%$q%")
                  ->orWhere('taxpayer_name', 'like', "%$q%")
                  ->orWhere('mobile', 'like', "%$q%")
                  ->orWhere('national_code', 'like', "%$q%");
            });
        }

        $bills = $query->latest()->paginate(20)->withQueryString();

        return view('admin.fees.bills', compact('bills'));
    }

    /** جزئیات قبض برای ادمین */
    public function billShow(int $id)
    {
        $this->authorizeAdmin();
        $dehyariIds = $this->scopedDehyariIds();

        $bill = Bill::with(['dehyari', 'items'])->whereIn('dehyari_id', $dehyariIds)->findOrFail($id);
        $payments = Payment::with(['receipts', 'reviewer'])
            ->where('bill_id', $bill->id)
            ->latest()
            ->get();

        return view('admin.fees.bill-show', compact('bill', 'payments'));
    }

    /* ═══════════════ فهرست پرداخت‌ها ═══════════════ */
    public function payments(Request $request)
    {
        $this->authorizeAdmin();
        $dehyariIds = $this->scopedDehyariIds();

        $query = Payment::with(['bill', 'receipts', 'reviewer'])
            ->whereHas('bill', fn($q) => $q->whereIn('dehyari_id', $dehyariIds));

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('bank_reference', 'like', "%$q%")
                  ->orWhere('transaction_id', 'like', "%$q%")
                  ->orWhereHas('bill', fn($b) => $b->where('bill_number', 'like', "%$q%")
                                                    ->orWhere('taxpayer_name', 'like', "%$q%")
                                                    ->orWhere('mobile', 'like', "%$q%"));
            });
        }

        $payments = $query->latest()->paginate(20)->withQueryString();

        return view('admin.fees.payments', compact('payments'));
    }

    /** تأیید فیش */
    public function approvePayment(Request $request, int $id)
    {
        $this->authorizeAdmin();
        $dehyariIds = $this->scopedDehyariIds();

        $payment = Payment::whereHas('bill', fn($q) => $q->whereIn('dehyari_id', $dehyariIds))->findOrFail($id);

        if ($payment->status !== 'pending_review') {
            return back()->withErrors(['payment' => 'این پرداخت قبلاً بررسی شده است.']);
        }

        DB::transaction(function () use ($payment) {
            $payment->update([
                'status'       => 'approved',
                'reviewed_by'  => auth()->id(),
                'reviewed_at'  => now(),
                'review_note'  => request('review_note'),
            ]);

            $bill = $payment->bill;
            $bill->update([
                'status'      => 'paid',
                'amount_paid' => $bill->amount_paid + $payment->amount_rial,
            ]);
        });

        return back()->with('success', '✅ فیش تأیید شد و قبض پرداخت‌شده ثبت گردید.');
    }

    /** رد فیش */
    public function rejectPayment(Request $request, int $id)
    {
        $this->authorizeAdmin();
        $dehyariIds = $this->scopedDehyariIds();

        $payment = Payment::whereHas('bill', fn($q) => $q->whereIn('dehyari_id', $dehyariIds))->findOrFail($id);

        $data = $request->validate([
            'review_note' => 'required|string|max:500',
        ], ['review_note.required' => 'دلیل رد الزامی است.']);

        DB::transaction(function () use ($payment, $data) {
            $payment->update([
                'status'       => 'rejected',
                'reviewed_by'  => auth()->id(),
                'reviewed_at'  => now(),
                'review_note'  => $data['review_note'],
            ]);

            $payment->bill->update(['status' => 'unpaid']);
        });

        return back()->with('success', '❌ فیش رد شد و قبض به وضعیت پرداخت‌نشده برگشت.');
    }

    /* ═══════════════ مدیریت تعرفه‌ها ═══════════════ */
    public function tariffs(Request $request)
    {
        $this->authorizeAdmin();
        $dehyariIds = $this->scopedDehyariIds();

        $dehyaris = Dehyari::whereIn('id', $dehyariIds)->with('village')->orderBy('name')->get();

        $query = FeeTariff::with(['feeType.category', 'dehyari'])
            ->whereIn('dehyari_id', $dehyariIds);

        if ($request->filled('dehyari_id')) {
            $query->where('dehyari_id', $request->dehyari_id);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $tariffs = $query->orderBy('dehyari_id')->orderBy('fee_type_id')->paginate(30)->withQueryString();

        return view('admin.fees.tariffs', compact('tariffs', 'dehyaris'));
    }

    public function tariffUpdate(Request $request, int $id)
    {
        $this->authorizeAdmin();
        $dehyariIds = $this->scopedDehyariIds();

        $tariff = FeeTariff::whereIn('dehyari_id', $dehyariIds)->findOrFail($id);

        $data = $request->validate([
            'base_amount' => 'required|integer|min:0',
            'unit_rate'   => 'required|integer|min:0',
            'coefficient' => 'required|numeric|min:0',
            'is_active'   => 'nullable|boolean',
            'description' => 'nullable|string|max:1000',
        ]);

        $tariff->update([
            'base_amount' => $data['base_amount'],
            'unit_rate'   => $data['unit_rate'],
            'coefficient' => $data['coefficient'],
            'is_active'   => $request->boolean('is_active'),
            'description' => $data['description'] ?? null,
        ]);

        return back()->with('success', '✅ تعرفه به‌روزرسانی شد.');
    }

    /* ═══════════════ مدیریت حساب‌های بانکی ═══════════════ */
    public function accounts(Request $request)
    {
        $this->authorizeAdmin();
        $dehyariIds = $this->scopedDehyariIds();

        $accounts = PaymentAccount::with('dehyari')
            ->whereIn('dehyari_id', $dehyariIds)
            ->orderBy('dehyari_id')
            ->paginate(30);

        return view('admin.fees.accounts', compact('accounts'));
    }

    public function accountUpdate(Request $request, int $id)
    {
        $this->authorizeAdmin();
        $dehyariIds = $this->scopedDehyariIds();

        $account = PaymentAccount::whereIn('dehyari_id', $dehyariIds)->findOrFail($id);

        $data = $request->validate([
            'bank_name'      => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'card_number'    => 'nullable|string|max:30',
            'sheba'          => 'nullable|string|max:30',
            'account_holder' => 'nullable|string|max:255',
            'is_active'      => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $account->update($data);

        return back()->with('success', '✅ حساب بانکی به‌روزرسانی شد.');
    }

    /* ═══════════════ خروجی CSV ═══════════════ */
    public function exportBills()
    {
        $this->authorizeAdmin();
        $dehyariIds = $this->scopedDehyariIds();

        $bills = Bill::with('dehyari')->whereIn('dehyari_id', $dehyariIds)->latest()->get();

        return response()->streamDownload(function () use ($bills) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM برای Excel فارسی
            fputcsv($out, ['کد پیگیری', 'دهیاری', 'مؤدی', 'موبایل', 'کد ملی', 'نوع عوارض', 'سال', 'مبلغ (ریال)', 'وضعیت', 'تاریخ صدور']);
            foreach ($bills as $b) {
                fputcsv($out, [
                    $b->bill_number, $b->dehyari?->name, $b->taxpayer_name, $b->mobile,
                    $b->national_code, $b->fee_type_name, $b->year,
                    $b->amount_rial, Bill::statusLabel($b->status), $b->issue_date,
                ]);
            }
            fclose($out);
        }, 'bills-' . now()->format('Ymd-His') . '.csv');
    }

    public function exportPayments()
    {
        $this->authorizeAdmin();
        $dehyariIds = $this->scopedDehyariIds();

        $payments = Payment::with('bill.dehyari')->whereHas('bill', fn($q) => $q->whereIn('dehyari_id', $dehyariIds))->latest()->get();

        return response()->streamDownload(function () use ($payments) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['کد پیگیری قبض', 'دهیاری', 'مؤدی', 'شماره پیگیری بانکی', 'مبلغ (ریال)', 'تاریخ واریز', 'وضعیت', 'تأییدکننده', 'تاریخ بررسی']);
            foreach ($payments as $p) {
                fputcsv($out, [
                    $p->bill?->bill_number, $p->bill?->dehyari?->name, $p->bill?->taxpayer_name,
                    $p->bank_reference, $p->amount_rial, $p->paid_at,
                    $p->status, $p->reviewer?->name, $p->reviewed_at,
                ]);
            }
            fclose($out);
        }, 'payments-' . now()->format('Ymd-His') . '.csv');
    }
}

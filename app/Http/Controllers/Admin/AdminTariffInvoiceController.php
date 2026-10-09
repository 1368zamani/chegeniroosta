<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\TariffInvoice;
use App\Models\TariffReceipt;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class AdminTariffInvoiceController extends Controller
{
    /**
     * لیست فیش‌ها
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = TariffInvoice::with(['village', 'set'])
            ->orderByDesc('created_at');

        // فیلتر بر اساس نقش
        if ($user->role === 'section_admin') {
            $query->whereHas('village', fn($q) => $q->where('section_id', $user->section_id));
        } elseif ($user->role === 'village_admin') {
            $query->where('village_id', $user->village_id);
        } elseif ($user->role === 'fee_officer') {
            // کارشناس فنی/مالی همه فیش‌ها رو می‌بینن
            // (تایید بر اساس officer_type محدود می‌شه)
        }

        // فیلتر وضعیت
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        // جستجو
        if ($q = $request->query('q')) {
            $query->where(function ($qq) use ($q) {
                $qq->where('tracking_code', 'like', "%{$q}%")
                   ->orWhere('payer_name', 'like', "%{$q}%")
                   ->orWhere('payer_mobile', 'like', "%{$q}%")
                   ->orWhere('payer_national_id', 'like', "%{$q}%");
            });
        }

        $invoices = $query->paginate(20)->withQueryString();

        // شمارش برای تب‌ها
        $counts = [
            'all'              => TariffInvoice::count(),
            'pending_review'   => TariffInvoice::where('status', 'pending_review')->count(),
            'issued'           => TariffInvoice::where('status', 'issued')->count(),
            'receipt_uploaded' => TariffInvoice::where('status', 'receipt_uploaded')->count(),
            'paid'             => TariffInvoice::where('status', 'paid')->count(),
            'cancelled'        => TariffInvoice::where('status', 'cancelled')->count(),
        ];

        $villages = Village::orderBy('name')->get();

        return view('admin.tariff-invoices.index', compact('invoices', 'counts', 'villages'));
    }

    /**
     * نمایش و بررسی یک فیش
     */
    public function show(Request $request, int $id)
    {
        $invoice = TariffInvoice::with(['lines', 'receipts.reviewedBy', 'village', 'set'])
            ->findOrFail($id);

        // بررسی دسترسی
        $this->authorizeInvoice($request->user(), $invoice);

        $account = BankAccount::getDefaultForVillage($invoice->village_id);

        return view('admin.tariff-invoices.show', compact('invoice', 'account'));
    }

    /**
     * تایید رسید — فیش پرداخت‌شده می‌شه
     */
    public function approveReceipt(Request $request, int $receiptId)
    {
        $receipt = TariffReceipt::with('invoice.village')->findOrFail($receiptId);
        $invoice = $receipt->invoice;

        $this->authorizeInvoice($request->user(), $invoice);

        // فقط کارشناس مالی یا ادمین‌های ارشد می‌تونن تایید کنن
        $user = $request->user();
        $canApprove = in_array($user->role, ['super_admin', 'section_admin', 'village_admin'])
            || ($user->role === 'fee_officer' && $user->officer_type === 'financial');

        if (!$canApprove) {
            return back()->withErrors(['error' => 'شما مجاز به تایید رسید نیستید.']);
        }

        try {
            DB::beginTransaction();

            $receipt->approve($user->id);

            DB::commit();

            return redirect()
                ->route('admin.tariff-invoices.show', $invoice->id)
                ->with('success', 'رسید تایید شد و فیش پرداخت‌شده علامت خورد.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Receipt approve failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'خطا: ' . $e->getMessage()]);
        }
    }

    /**
     * رد رسید
     */
    public function rejectReceipt(Request $request, int $receiptId)
    {
        $receipt = TariffReceipt::with('invoice.village')->findOrFail($receiptId);
        $invoice = $receipt->invoice;

        $this->authorizeInvoice($request->user(), $invoice);

        // فقط کارشناس مالی یا ادمین‌های ارشد
        $user = $request->user();
        $canReject = in_array($user->role, ['super_admin', 'section_admin', 'village_admin'])
            || ($user->role === 'fee_officer' && $user->officer_type === 'financial');

        if (!$canReject) {
            return back()->withErrors(['error' => 'شما مجاز به رد رسید نیستید.']);
        }

        $data = $request->validate([
            'reject_reason' => ['required', 'string', 'max:1000'],
        ]);

        try {
            DB::beginTransaction();
            $receipt->reject($user->id, $data['reject_reason']);
            DB::commit();

            return redirect()
                ->route('admin.tariff-invoices.show', $invoice->id)
                ->with('success', 'رسید رد شد.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Receipt reject failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'خطا: ' . $e->getMessage()]);
        }
    }

    /**
     * ابطال فیش
     */
    public function cancel(Request $request, int $id)
    {
        $invoice = TariffInvoice::findOrFail($id);

        $this->authorizeInvoice($request->user(), $invoice);

        // فقط ادمین‌های ارشد می‌تونن ابطال کنن
        $user = $request->user();
        if (!in_array($user->role, ['super_admin', 'section_admin', 'village_admin'])) {
            return back()->withErrors(['error' => 'شما مجاز به ابطال فیش نیستید.']);
        }

        if ($invoice->status === 'paid') {
            return back()->withErrors(['error' => 'فیش پرداخت‌شده قابل ابطال نیست.']);
        }

        $data = $request->validate([
            'review_note' => ['required', 'string', 'max:1000'],
        ]);

        try {
            DB::beginTransaction();

            $invoice->update([
                'status' => 'cancelled',
                'review_note' => $data['review_note'],
            ]);

            DB::commit();

            return redirect()
                ->route('admin.tariff-invoices.show', $invoice->id)
                ->with('success', 'فیش ابطال شد.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'خطا: ' . $e->getMessage()]);
        }
    }

    /**
     * بازگردانی فیش ابطال‌شده به حالت صادرشده
     */
    public function restore(Request $request, int $id)
    {
        $invoice = TariffInvoice::findOrFail($id);
        $this->authorizeInvoice($request->user(), $invoice);

        if (!in_array($request->user()->role, ['super_admin', 'section_admin'])) {
            return back()->withErrors(['error' => 'دسترسی ندارید.']);
        }

        if ($invoice->status !== 'cancelled') {
            return back()->withErrors(['error' => 'فقط فیش‌های ابطال‌شده قابل بازگردانی هستند.']);
        }

        $invoice->update(['status' => 'issued', 'review_note' => null]);

        return back()->with('success', 'فیش بازگردانی شد.');
    }

    /**
     * حذف فیش (فقط Super Admin)
     */
    public function destroy(Request $request, int $id)
    {
        $invoice = TariffInvoice::findOrFail($id);
        $this->authorizeInvoice($request->user(), $invoice);

        if ($request->user()->role !== 'super_admin') {
            return back()->withErrors(['error' => 'فقط مدیر کل می‌تواند حذف کند.']);
        }

        if ($invoice->status === 'paid') {
            return back()->withErrors(['error' => 'فیش پرداخت‌شده قابل حذف نیست.']);
        }

        $tracking = $invoice->tracking_code;
        $invoice->delete();

        return redirect()
            ->route('admin.tariff-invoices.index')
            ->with('success', "فیش {$tracking} حذف شد.");
    }

    /**
     * تغییر سریع وضعیت (از لیست)
     */
    public function quickChangeStatus(Request $request, int $id)
    {
        $invoice = TariffInvoice::findOrFail($id);
        $this->authorizeInvoice($request->user(), $invoice);

        $data = $request->validate([
            'status' => ['required', 'in:issued,pending_review,cancelled'],
        ]);

        if ($request->user()->role === 'fee_officer') {
            return back()->withErrors(['error' => 'کارشناسان نمی‌توانند وضعیت را تغییر دهند.']);
        }

        $invoice->update(['status' => $data['status']]);

        return back()->with('success', 'وضعیت تغییر یافت.');
    }

    /**
     * بررسی دسترسی به یک فیش
     */
    private function authorizeInvoice($user, TariffInvoice $invoice): void
    {
        if ($user->role === 'super_admin') {
            return;
        }

        if ($user->role === 'section_admin') {
            if ($invoice->village?->section_id !== $user->section_id) {
                abort(403, 'دسترسی ندارید.');
            }
            return;
        }

        if ($user->role === 'village_admin') {
            if ($invoice->village_id !== $user->village_id) {
                abort(403, 'دسترسی ندارید.');
            }
            return;
        }

        if ($user->role === 'fee_officer') {
            // کارشناسان فنی/مالی همه رو می‌بینن ولی فقط بر اساس نوع می‌تونن عمل کنن
            return;
        }

        abort(403);
    }
}

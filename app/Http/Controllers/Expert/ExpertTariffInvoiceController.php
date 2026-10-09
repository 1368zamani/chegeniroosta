<?php

namespace App\Http\Controllers\Expert;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\TariffInvoice;
use App\Models\TariffReceipt;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpertTariffInvoiceController extends Controller
{
    /**
     * لیست فیش‌ها
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = TariffInvoice::with(['village', 'set'])
            ->orderByDesc('created_at');

        // کارشناس مالی همه رو می‌بینه، کارشناس فنی فقط levy (بر اساس آیتم‌ها)
        // برای سادگی، همه فیش‌ها رو نشون می‌دیم چون نقششون مالی/فنی است
        // در صورت نیاز می‌تونیم فیلتر کنیم

        // فیلتر وضعیت
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        // فیلتر روستا (اختیاری)
        if ($villageId = $request->query('village_id')) {
            $query->where('village_id', $villageId);
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

        $counts = [
            'all'              => TariffInvoice::count(),
            'pending_review'   => TariffInvoice::where('status', 'pending_review')->count(),
            'issued'           => TariffInvoice::where('status', 'issued')->count(),
            'receipt_uploaded' => TariffInvoice::where('status', 'receipt_uploaded')->count(),
            'paid'             => TariffInvoice::where('status', 'paid')->count(),
            'cancelled'        => TariffInvoice::where('status', 'cancelled')->count(),
        ];

        $villages = Village::orderBy('name')->get();

        return view('expert.tariff-invoices.index', compact('invoices', 'counts', 'villages'));
    }

    /**
     * نمایش فیش
     */
    public function show(Request $request, int $id)
    {
        $invoice = TariffInvoice::with(['lines', 'receipts.reviewedBy', 'village', 'set'])
            ->findOrFail($id);

        $account = BankAccount::getDefaultForVillage($invoice->village_id);

        return view('expert.tariff-invoices.show', compact('invoice', 'account'));
    }

    /**
     * تایید رسید
     */
    public function approveReceipt(Request $request, int $receiptId)
    {
        $receipt = TariffReceipt::with('invoice')->findOrFail($receiptId);
        $invoice = $receipt->invoice;

        $user = $request->user();
        $canApprove = in_array($user->role, ['super_admin', 'section_admin'])
            || ($user->role === 'fee_officer' && $user->officer_type === 'financial');

        if (!$canApprove) {
            return back()->withErrors(['error' => 'شما مجاز به تایید رسید نیستید.']);
        }

        try {
            DB::beginTransaction();
            $receipt->approve($user->id);
            DB::commit();

            return redirect()
                ->route('expert.tariff-invoices.show', $invoice->id)
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
        $receipt = TariffReceipt::with('invoice')->findOrFail($receiptId);
        $invoice = $receipt->invoice;

        $user = $request->user();
        $canReject = in_array($user->role, ['super_admin', 'section_admin'])
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
                ->route('expert.tariff-invoices.show', $invoice->id)
                ->with('success', 'رسید رد شد.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'خطا: ' . $e->getMessage()]);
        }
    }

    /**
     * ابطال فیش
     */
    public function cancel(Request $request, int $id)
    {
        $invoice = TariffInvoice::findOrFail($id);

        if (!in_array($request->user()->role, ['super_admin', 'section_admin'])) {
            return back()->withErrors(['error' => 'شما مجاز به ابطال فیش نیستید.']);
        }

        if ($invoice->status === 'paid') {
            return back()->withErrors(['error' => 'فیش پرداخت‌شده قابل ابطال نیست.']);
        }

        $data = $request->validate([
            'review_note' => ['required', 'string', 'max:1000'],
        ]);

        $invoice->update([
            'status' => 'cancelled',
            'review_note' => $data['review_note'],
        ]);

        return redirect()
            ->route('expert.tariff-invoices.show', $invoice->id)
            ->with('success', 'فیش ابطال شد.');
    }

    /**
     * بازگردانی
     */
    public function restore(Request $request, int $id)
    {
        if (!in_array($request->user()->role, ['super_admin', 'section_admin'])) {
            return back()->withErrors(['error' => 'دسترسی ندارید.']);
        }

        $invoice = TariffInvoice::findOrFail($id);

        if ($invoice->status !== 'cancelled') {
            return back()->withErrors(['error' => 'فقط فیش‌های ابطال‌شده قابل بازگردانی هستند.']);
        }

        $invoice->update(['status' => 'issued', 'review_note' => null]);

        return back()->with('success', 'فیش بازگردانی شد.');
    }

    /**
     * حذف (فقط Super Admin)
     */
    public function destroy(Request $request, int $id)
    {
        if ($request->user()->role !== 'super_admin') {
            return back()->withErrors(['error' => 'فقط مدیر کل می‌تواند حذف کند.']);
        }

        $invoice = TariffInvoice::findOrFail($id);

        if ($invoice->status === 'paid') {
            return back()->withErrors(['error' => 'فیش پرداخت‌شده قابل حذف نیست.']);
        }

        $tracking = $invoice->tracking_code;
        $invoice->delete();

        return redirect()
            ->route('expert.tariff-invoices.index')
            ->with('success', "فیش {$tracking} حذف شد.");
    }
}

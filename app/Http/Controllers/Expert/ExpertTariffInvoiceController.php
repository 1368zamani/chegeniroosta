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
     * لیست فیش‌ها — با فیلتر بخش
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = TariffInvoice::with(['village', 'set'])
            ->orderByDesc('created_at');

        // ═══ فیلتر بخش ═══
        $this->applySectionFilter($query, $user);

        // فیلتر وضعیت
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        // فیلتر روستا
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

        // ═══ Counts با فیلتر بخش ═══
        $countsQuery = fn() => $this->filteredInvoicesQuery($user);

        $counts = [
            'all'              => $countsQuery()->count(),
            'pending_review'   => $countsQuery()->where('status', 'pending_review')->count(),
            'issued'           => $countsQuery()->where('status', 'issued')->count(),
            'receipt_uploaded' => $countsQuery()->where('status', 'receipt_uploaded')->count(),
            'paid'             => $countsQuery()->where('status', 'paid')->count(),
            'cancelled'        => $countsQuery()->where('status', 'cancelled')->count(),
        ];

        // ═══ لیست روستاهای مجاز ═══
        $villages = $this->allowedVillages($user);

        return view('expert.tariff-invoices.index', compact('invoices', 'counts', 'villages'));
    }

    /**
     * نمایش فیش
     */
    public function show(Request $request, int $id)
    {
        $user = $request->user();
        $invoice = TariffInvoice::with(['lines', 'receipts.reviewedBy', 'village', 'set'])
            ->findOrFail($id);

        // چک دسترسی
        $this->authorizeVillageAccess($user, $invoice->village_id);

        $account = BankAccount::getDefaultForVillage($invoice->village_id);

        return view('expert.tariff-invoices.show', compact('invoice', 'account'));
    }

    /**
     * تایید رسید
     */
    public function approveReceipt(Request $request, int $receiptId)
    {
        $user = $request->user();
        $receipt = TariffReceipt::with('invoice.village')->findOrFail($receiptId);
        $invoice = $receipt->invoice;

        $this->authorizeVillageAccess($user, $invoice->village_id);

        $canApprove = $user->role === 'super_admin'
            || $user->role === 'section_admin'
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
            return back()->withErrors(['error' => 'خطا: ' . $e->getMessage()]);
        }
    }

    /**
     * رد رسید
     */
    public function rejectReceipt(Request $request, int $receiptId)
    {
        $user = $request->user();
        $receipt = TariffReceipt::with('invoice.village')->findOrFail($receiptId);
        $invoice = $receipt->invoice;

        $this->authorizeVillageAccess($user, $invoice->village_id);

        $canReject = $user->role === 'super_admin'
            || $user->role === 'section_admin'
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
        $user = $request->user();
        $invoice = TariffInvoice::findOrFail($id);

        $this->authorizeVillageAccess($user, $invoice->village_id);

        if (!in_array($user->role, ['super_admin', 'section_admin'])) {
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
        $user = $request->user();
        $invoice = TariffInvoice::findOrFail($id);

        $this->authorizeVillageAccess($user, $invoice->village_id);

        if (!in_array($user->role, ['super_admin', 'section_admin'])) {
            return back()->withErrors(['error' => 'دسترسی ندارید.']);
        }

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
            ->with("success", "فیش {$tracking} حذف شد.");
    }

    /**
     * ═══ فیلتر بخش بر اساس نقش کاربر ═══
     */
    private function applySectionFilter($query, $user): void
    {
        if ($user->role === 'super_admin') {
            return;
        }

        $query->whereHas('village', function ($q) use ($user) {
            if ($user->role === 'section_admin' && $user->section_id) {
                $q->where('section_id', $user->section_id);
            } elseif ($user->role === 'village_admin' && $user->village_id) {
                $q->where('id', $user->village_id);
            } elseif ($user->role === 'fee_officer' && $user->section_id) {
                $q->where('section_id', $user->section_id);
            }
        });
    }

    /**
     * کوئری فیش‌ها با فیلتر
     */
    private function filteredInvoicesQuery($user)
    {
        $q = TariffInvoice::query();
        $this->applySectionFilter($q, $user);
        return $q;
    }

    /**
     * لیست روستاهای مجاز
     */
    private function allowedVillages($user)
    {
        $q = Village::orderBy('name');

        if ($user->role === 'section_admin' && $user->section_id) {
            $q->where('section_id', $user->section_id);
        } elseif ($user->role === 'village_admin' && $user->village_id) {
            $q->where('id', $user->village_id);
        } elseif ($user->role === 'fee_officer' && $user->section_id) {
            $q->where('section_id', $user->section_id);
        }

        return $q->get();
    }

    /**
     * ═══ چک دسترسی به یک روستا ═══
     */
    private function authorizeVillageAccess($user, int $villageId): void
    {
        if ($user->role === 'super_admin') {
            return;
        }

        $village = Village::find($villageId);
        if (!$village) {
            abort(404);
        }

        if ($user->role === 'section_admin' && $user->section_id) {
            if ((int) $village->section_id !== (int) $user->section_id) {
                abort(403, 'شما فقط به روستاهای بخش خود دسترسی دارید.');
            }
            return;
        }

        if ($user->role === 'village_admin' && $user->village_id) {
            if ((int) $village->id !== (int) $user->village_id) {
                abort(403, 'شما فقط به روستای خود دسترسی دارید.');
            }
            return;
        }

        if ($user->role === 'fee_officer' && $user->section_id) {
            if ((int) $village->section_id !== (int) $user->section_id) {
                abort(403, 'شما فقط به روستاهای بخش خود دسترسی دارید.');
            }
            return;
        }
    }
}

<?php

namespace App\Http\Controllers\VillageAdmin;

use App\Http\Controllers\Controller;
use App\Models\TariffInvoice;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TariffReportController extends Controller
{
    /**
     * لیست گزارش فیش‌های روستا (خواندنی)
     */
    public function index(Request $request, Village $village)
    {
        $user = $request->user();

        // بررسی دسترسی — دهیار فقط روستای خودش
        if ($user->role === 'village_admin' && (int) $user->village_id !== (int) $village->id) {
            abort(403, 'دسترسی ندارید.');
        }

        if ($user->role === 'section_admin' && $village->section_id !== $user->section_id) {
            abort(403, 'دسترسی ندارید.');
        }

        $query = TariffInvoice::with('set')
            ->where('village_id', $village->id)
            ->orderByDesc('created_at');

        // فیلتر وضعیت
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        // فیلتر سال
        if ($year = $request->query('year')) {
            $query->whereHas('set', fn($q) => $q->where('jalali_year', $year));
        }

        $invoices = $query->paginate(20)->withQueryString();

        // آمار
        $stats = [
            'total'              => TariffInvoice::where('village_id', $village->id)->count(),
            'pending_review'     => TariffInvoice::where('village_id', $village->id)->where('status', 'pending_review')->count(),
            'issued'             => TariffInvoice::where('village_id', $village->id)->where('status', 'issued')->count(),
            'receipt_uploaded'   => TariffInvoice::where('village_id', $village->id)->where('status', 'receipt_uploaded')->count(),
            'paid'               => TariffInvoice::where('village_id', $village->id)->where('status', 'paid')->count(),
            'cancelled'          => TariffInvoice::where('village_id', $village->id)->where('status', 'cancelled')->count(),
        ];

        // مجموع درآمد
        $income = [
            'total_amount'   => TariffInvoice::where('village_id', $village->id)->sum('total'),
            'paid_amount'    => TariffInvoice::where('village_id', $village->id)->where('status', 'paid')->sum('paid_amount'),
            'unpaid_amount'  => TariffInvoice::where('village_id', $village->id)
                ->whereIn('status', ['issued', 'pending_review', 'receipt_uploaded'])
                ->sum('total'),
        ];

        // سال‌ها
        $years = TariffInvoice::where('village_id', $village->id)
            ->whereHas('set')
            ->with('set:id,jalali_year')
            ->get()
            ->pluck('set.jalali_year')
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        return view('admin.village.tariff-reports.index',
            compact('village', 'invoices', 'stats', 'income', 'years'));
    }

    /**
     * نمایش یک فیش (خواندنی)
     */
    public function show(Request $request, Village $village, int $id)
    {
        $user = $request->user();

        if ($user->role === 'village_admin' && (int) $user->village_id !== (int) $village->id) {
            abort(403);
        }

        $invoice = TariffInvoice::with(['lines', 'receipts.reviewedBy', 'set'])
            ->where('village_id', $village->id)
            ->findOrFail($id);

        return view('admin.village.tariff-reports.show', compact('village', 'invoice'));
    }

    /**
     * گزارش خلاصه ماهانه/سالانه
     */
    public function summary(Request $request, Village $village)
    {
        $user = $request->user();

        if ($user->role === 'village_admin' && (int) $user->village_id !== (int) $village->id) {
            abort(403);
        }

        // آمار سالانه
        $yearStats = TariffInvoice::where('village_id', $village->id)
            ->select(
                DB::raw('YEAR(issued_at) as year'),
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(total) as total_amount'),
                DB::raw('SUM(CASE WHEN status = "paid" THEN paid_amount ELSE 0 END) as paid_amount')
            )
            ->whereNotNull('issued_at')
            ->groupBy('year')
            ->orderByDesc('year')
            ->get();

        return view('admin.village.tariff-reports.summary', compact('village', 'yearStats'));
    }
}

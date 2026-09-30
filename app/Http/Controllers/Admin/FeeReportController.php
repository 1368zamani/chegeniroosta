<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Dehyari;
use App\Models\Payment;
use App\Models\Village;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeeReportController extends Controller
{
    private function scopedDehyariIds()
    {
        $u = auth()->user();
        if ($u->isSuperAdmin()) return Dehyari::pluck('id');
        if ($u->isSectionAdmin() || $u->isFeeOfficer()) {
            $villageIds = Village::where('section_id', $u->section_id)->pluck('id');
            return Dehyari::whereIn('village_id', $villageIds)->pluck('id');
        }
        if ($u->isVillageAdmin() && $u->village_id) {
            return Dehyari::where('village_id', $u->village_id)->pluck('id');
        }
        return collect();
    }

    public function index(Request $request)
    {
        $u = auth()->user();
        if (!$u || (!$u->isSuperAdmin() && !$u->isSectionAdmin() && !$u->isFeeOfficer())) {
            abort(403);
        }

        $dehyariIds = $this->scopedDehyariIds();

        $from = $request->filled('from') ? Carbon::parse($request->from) : now()->subDays(30);
        $to   = $request->filled('to')   ? Carbon::parse($request->to)   : now();

        $paid = Bill::whereIn('dehyari_id', $dehyariIds)
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$from, $to]);

        $totalIncome = (clone $paid)->sum('amount_rial');
        $countPaid = (clone $paid)->count();

        $pending = Payment::whereHas('bill', fn($q) => $q->whereIn('dehyari_id', $dehyariIds))
            ->where('status', 'pending_review');

        $totalPending = (clone $pending)->count();
        $pendingAmount = (clone $pending)->sum('amount_rial');

        // درآمد به تفکیک دهیاری
        $byDehyari = Bill::select('dehyari_id', DB::raw('COUNT(*) as cnt'), DB::raw('SUM(amount_rial) as total'))
            ->whereIn('dehyari_id', $dehyariIds)
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$from, $to])
            ->groupBy('dehyari_id')
            ->with('dehyari')
            ->orderByDesc('total')
            ->limit(20)
            ->get();

        return view('admin.fees.reports', compact(
            'from', 'to', 'totalIncome', 'countPaid',
            'totalPending', 'pendingAmount', 'byDehyari'
        ));
    }
}

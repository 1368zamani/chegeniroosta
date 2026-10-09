<?php

namespace App\Http\Controllers\Expert;

use App\Http\Controllers\Controller;
use App\Models\TariffSet;
use App\Models\TariffItem;
use App\Models\TariffParameter;
use App\Models\TariffExemption;
use App\Models\TariffInvoice;
use App\Models\Village;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ExpertDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ═══ شناسه روستاهای مجاز بر اساس بخش کاربر ═══
        $villageIds = null;
        if ($user->role !== 'super_admin') {
            $villageQuery = Village::query();
            if ($user->section_id) {
                $villageQuery->where('section_id', $user->section_id);
            } elseif ($user->village_id) {
                $villageQuery->where('id', $user->village_id);
            }
            $villageIds = $villageQuery->pluck('id')->toArray();
        }

        // ═══ آمار کامل ═══
        $stats = [
            'total_tariffs'    => $villageIds
                ? TariffSet::whereIn('village_id', $villageIds)->count()
                : TariffSet::count(),
            'total_items'      => $villageIds
                ? TariffItem::whereHas('set', fn($q) => $q->whereIn('village_id', $villageIds))->count()
                : TariffItem::count(),
            'total_parameters' => $villageIds
                ? TariffParameter::whereHas('set', fn($q) => $q->whereIn('village_id', $villageIds))->count()
                : TariffParameter::count(),
            'total_exemptions' => $villageIds
                ? TariffExemption::whereHas('set', fn($q) => $q->whereIn('village_id', $villageIds))->count()
                : TariffExemption::count(),
            'total_villages'   => $villageIds ? count($villageIds) : Village::count(),
            'total_users'      => User::count(),
            'total_invoices'   => $villageIds
                ? TariffInvoice::whereIn('village_id', $villageIds)->count()
                : TariffInvoice::count(),
        ];

        // ═══ تعرفه‌های اخیر ═══
        $tariffQuery = TariffSet::with('village')->orderByDesc('id')->limit(10);
        if ($villageIds) {
            $tariffQuery->whereIn('village_id', $villageIds);
        }
        $recentTariffs = $tariffQuery->get();

        // ═══ پیش‌نویس‌ها ═══
        $draftQuery = TariffSet::with('village')->where('status', 'draft')->orderByDesc('id')->limit(10);
        if ($villageIds) {
            $draftQuery->whereIn('village_id', $villageIds);
        }
        $draftTariffs = $draftQuery->get();

        return view('expert.dashboard', compact('user', 'stats', 'recentTariffs', 'draftTariffs'));
    }
}

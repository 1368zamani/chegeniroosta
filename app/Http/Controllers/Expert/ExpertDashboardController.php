<?php

namespace App\Http\Controllers\Expert;

use App\Http\Controllers\Controller;
use App\Models\TariffSet;
use App\Models\TariffItem;
use App\Models\TariffParameter;
use App\Models\TariffExemption;
use App\Models\Village;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ExpertDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total_tariffs'     => TariffSet::count(),
            'total_items'       => TariffItem::count(),
            'total_parameters'  => TariffParameter::count(),
            'total_exemptions'  => TariffExemption::count(),
            'total_villages'    => Village::count(),
            'total_users'       => User::count(),
        ];

        // تعرفه‌های اخیر (۱۰ تا)
        $recentTariffs = TariffSet::with('village')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        // ست‌های در حالت پیش‌نویس (نیاز به بررسی)
        $draftTariffs = TariffSet::with('village')
            ->where('status', 'draft')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('expert.dashboard', compact('user', 'stats', 'recentTariffs', 'draftTariffs'));
    }
}

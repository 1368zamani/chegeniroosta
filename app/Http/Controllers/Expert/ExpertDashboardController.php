<?php

namespace App\Http\Controllers\Expert;

use App\Http\Controllers\Controller;
use App\Models\LevyFile;
use App\Models\LevyPublicRequest;
use App\Models\TariffTitle;
use App\Models\Village;
use Illuminate\Support\Facades\Auth;

class ExpertDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // روستاهای مجاز بر اساس بخش/روستای کاربر
        $villageIds = null;
        if (!$user->isSuperAdmin()) {
            $query = Village::query();
            if ($user->section_id) {
                $query->where('section_id', $user->section_id);
            } elseif ($user->village_id) {
                $query->where('id', $user->village_id);
            }
            $villageIds = $query->pluck('id')->all();
        }

        $filesQuery = LevyFile::query();
        $requestQuery = LevyPublicRequest::query();
        if ($villageIds !== null) {
            $filesQuery->whereIn('village_id', $villageIds);
            $requestQuery->whereIn('village_id', $villageIds);
        }

        $stats = [
            'total_files'    => $filesQuery->count(),
            'waste_titles'   => TariffTitle::where('category', 'waste')->where('is_active', 1)->count(),
            'pending_reqs'   => (clone $requestQuery)->whereIn('status', ['awaiting_receipt', 'under_review'])->count(),
            'total_villages' => $villageIds !== null ? count($villageIds) : Village::count(),
        ];

        $recentRequests = (clone $requestQuery)
            ->with('village')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('expert.dashboard', compact('user', 'stats', 'recentRequests'));
    }
}

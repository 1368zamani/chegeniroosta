<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Village;
use App\Models\User;
use App\Models\SectionNews;
use App\Models\SectionRequest;
use App\Models\VillageRequest;

class AdminSectionDashboardController extends Controller
{
    public function index($sectionId)
    {
        $section = $this->authorizeSection($sectionId);

        $villageIds = Village::where('section_id', $section->id)->pluck('id');

        $stats = [
            'villages'        => $villageIds->count(),
            'village_admins'  => User::where('role', 'village_admin')->whereIn('village_id', $villageIds)->count(),
            'section_requests'=> SectionRequest::where('section_id', $section->id)->count(),
            'village_requests'=> VillageRequest::whereIn('village_id', $villageIds)->count(),
            'news'            => SectionNews::where('section_id', $section->id)->count(),
            'pending_requests'=> SectionRequest::where('section_id', $section->id)
                                    ->whereIn('status', ['submitted','reviewing','assigned','processing'])->count()
                                + VillageRequest::whereIn('village_id', $villageIds)
                                    ->whereIn('status', ['submitted','reviewing','assigned','processing'])->count(),
            'resolved_requests' => SectionRequest::where('section_id', $section->id)->where('status', 'resolved')->count()
                                + VillageRequest::whereIn('village_id', $villageIds)->where('status', 'resolved')->count(),
        ];

        $recentSectionRequests = SectionRequest::where('section_id', $section->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentVillageRequests = VillageRequest::whereIn('village_id', $villageIds)
            ->with('village')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $villages = Village::where('section_id', $section->id)
            ->withCount('news')
            ->orderBy('name')
            ->limit(8)
            ->get();

        return view('admin.sections.dashboard', compact(
            'section', 'stats',
            'recentSectionRequests', 'recentVillageRequests',
            'villages'
        ));
    }

    private function authorizeSection($sectionId): Section
    {
        $section = Section::findOrFail($sectionId);
        if (!auth()->user()->canManageSection($section->id)) abort(403);
        return $section;
    }
}

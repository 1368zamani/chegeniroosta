<?php

namespace App\Http\Controllers\VillageAdmin;

use App\Http\Controllers\Controller;
use App\Models\Village;

class DashboardController extends Controller
{
    public function index($villageId)
    {
        $village = $this->authorizeVillage($villageId);

        $stats = [
            'news'        => \App\Models\VillageNews::where('village_id', $village->id)->count(),
            'projects'    => \App\Models\VillageProject::where('village_id', $village->id)->count(),
            'events'      => \App\Models\VillageEvent::where('village_id', $village->id)->count(),
            'facilities'  => \App\Models\VillageFacility::where('village_id', $village->id)->count(),
            'council'     => \App\Models\VillageCouncilMember::where('village_id', $village->id)->count(),
            'attractions' => \App\Models\VillageAttraction::where('village_id', $village->id)->count(),
            'gallery'     => \App\Models\VillageImage::where('village_id', $village->id)->count(),
            'requests'    => \App\Models\VillageRequest::where('village_id', $village->id)->count(),
        ];

        return view('admin.village.dashboard', compact('village', 'stats'));
    }

    private function authorizeVillage($villageId): Village
    {
        $village = Village::findOrFail($villageId);
        if (!auth()->user()->canManageVillage($village)) {
            abort(403);
        }
        return $village;
    }
}

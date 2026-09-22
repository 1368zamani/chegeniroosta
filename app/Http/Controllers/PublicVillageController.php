<?php

namespace App\Http\Controllers;

use App\Models\Village;
use App\Models\VillageImage;
use App\Models\VillageNews;
use App\Models\VillageProject;
use App\Models\VillageRequest;
use App\Models\VillageFacility;
use App\Models\VillageCouncilMember;
use App\Models\VillageAttraction;

class PublicVillageController extends Controller
{
    public function show($slug)
    {
        $village = Village::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // گالری
        $images = VillageImage::where('village_id', $village->id)
            ->orderBy('id', 'desc')
            ->limit(12)
            ->get();

        // اخبار
        $news = VillageNews::where('village_id', $village->id)
            ->where('is_published', true)
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // آمار کارت‌ها
        $stats = [
            'last_news_date'    => optional(VillageNews::where('village_id', $village->id)->where('is_published', true)->orderBy('created_at', 'desc')->first())->created_at,
            'active_projects'   => VillageProject::where('village_id', $village->id)->whereIn('status', ['planned', 'in_progress'])->count(),
            'tracking_requests' => VillageRequest::where('village_id', $village->id)->whereIn('status', ['submitted', 'reviewing', 'assigned', 'processing'])->count(),
            'pinned_news'       => VillageNews::where('village_id', $village->id)->where('is_published', true)->where('is_pinned', true)->count(),
        ];

        // پروژه‌ها
        $projects = VillageProject::where('village_id', $village->id)
            ->orderBy('sort_order')
            ->limit(6)
            ->get();
        // امکانات
        $facilities = VillageFacility::where('village_id', $village->id)
            ->orderBy('sort_order')
            ->limit(15)
            ->get();

        // شورا
        $councilMembers = VillageCouncilMember::where('village_id', $village->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // جاذبه‌ها
        $attractions = VillageAttraction::where('village_id', $village->id)
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        return view('village.show', compact(
            'village',
            'images',
            'news',
            'stats',
            'projects',
            'facilities',
            'councilMembers',
            'attractions'
        ));
    }
}

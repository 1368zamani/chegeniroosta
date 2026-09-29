<?php

namespace App\Http\Controllers;

use App\Models\Village;
use App\Models\VillageNews;
use App\Models\VillageImage;
use App\Models\VillageProject;
use App\Models\VillageRequest;
use App\Models\VillageEvent;
use App\Models\VillageFacility;
use App\Models\VillageCouncilMember;
use App\Models\VillageAttraction;
use Illuminate\Http\Request;

class PublicVillageSiteController extends Controller
{
    public function home($slug)
    {
        $village = $this->getVillage($slug);

        $stats = [
            'last_news'         => optional(VillageNews::where('village_id', $village->id)->where('is_published', true)->orderBy('created_at', 'desc')->first())->created_at,
            'active_projects'   => VillageProject::where('village_id', $village->id)->whereIn('status', ['planned', 'in_progress'])->count(),
            'tracking_requests' => VillageRequest::where('village_id', $village->id)->whereIn('status', ['submitted', 'reviewing', 'assigned', 'processing'])->count(),
            'new_announcements' => VillageNews::where('village_id', $village->id)->where('is_published', true)->where('is_pinned', true)->count(),
        ];

        $latestNews = VillageNews::where('village_id', $village->id)
            ->where('is_published', true)
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        $upcomingEvents = VillageEvent::where('village_id', $village->id)
            ->where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->limit(5)
            ->get();

        $projects = VillageProject::where('village_id', $village->id)
            ->orderBy('sort_order')
            ->limit(4)
            ->get();

        $facilities = VillageFacility::where('village_id', $village->id)
            ->orderBy('sort_order')
            ->limit(12)
            ->get();

        return view('village.site.home', compact('village', 'stats', 'latestNews', 'upcomingEvents', 'projects', 'facilities'));
    }

    public function newsList($slug)
    {
        $village = $this->getVillage($slug);

        $news = VillageNews::where('village_id', $village->id)
            ->where('is_published', true)
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('village.site.news', compact('village', 'news'));
    }

    public function newsShow($slug, $newsSlug)
    {
        $village = $this->getVillage($slug);

        $news = VillageNews::where('village_id', $village->id)
            ->where('slug', $newsSlug)
            ->where('is_published', true)
            ->firstOrFail();

        $related = VillageNews::where('village_id', $village->id)
            ->where('is_published', true)
            ->where('id', '!=', $news->id)
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();

        return view('village.site.news-show', compact('village', 'news', 'related'));
    }

    public function projects($slug)
    {
        $village = $this->getVillage($slug);

        $projects = VillageProject::where('village_id', $village->id)
            ->orderBy('status')
            ->orderBy('sort_order')
            ->get();

        return view('village.site.projects', compact('village', 'projects'));
    }

    public function about($slug)
    {
        $village = $this->getVillage($slug);

        $attractions = VillageAttraction::where('village_id', $village->id)
            ->orderBy('sort_order')
            ->get();

        $gallery = VillageImage::where('village_id', $village->id)
            ->orderBy('sort_order')
            ->limit(20)
            ->get();

        return view('village.site.about', compact('village', 'attractions', 'gallery'));
    }

    public function dehyar($slug)
    {
        $village = $this->getVillage($slug);

        return view('village.site.dehyar', compact('village'));
    }

    public function council($slug)
    {
        $village = $this->getVillage($slug);

        $members = VillageCouncilMember::where('village_id', $village->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('village.site.council', compact('village', 'members'));
    }

    public function resolutions($slug)
    {
        $village = $this->getVillage($slug);

        $resolutions = \App\Models\VillageCouncilResolution::where('village_id', $village->id)
            ->where('is_public', true)
            ->orderBy('session_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('village.site.resolutions', compact('village', 'resolutions'));
    }

    public function facilities($slug)
    {
        $village = $this->getVillage($slug);

        $facilities = VillageFacility::where('village_id', $village->id)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        return view('village.site.facilities', compact('village', 'facilities'));
    }

    public function events($slug)
    {
        $village = $this->getVillage($slug);

        $upcoming = VillageEvent::where('village_id', $village->id)
            ->where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->get();

        $past = VillageEvent::where('village_id', $village->id)
            ->where('event_date', '<', now()->toDateString())
            ->orderBy('event_date', 'desc')
            ->limit(20)
            ->get();

        return view('village.site.events', compact('village', 'upcoming', 'past'));
    }

    public function requestCreate($slug)
    {
        $village = $this->getVillage($slug);

        $subjects = [
            'آب', 'برق', 'گاز', 'مخابرات', 'راه',
            'پسماند', 'روشنایی', 'عمران', 'دهیاری', 'سایر'
        ];

        return view('village.site.request-create', compact('village', 'subjects'));
    }

    public function requestStore(Request $request, $slug)
    {
        $village = $this->getVillage($slug);

        $data = $request->validate([
            'full_name'   => 'required|string|max:100',
            'phone'       => 'required|string|max:20',
            'subject'     => 'required|string|max:100',
            'description' => 'required|string|max:2000',
            'attachment'  => 'nullable|file|max:5120',
        ]);

        $data['village_id'] = $village->id;
        $data['tracking_code'] = VillageRequest::generateTrackingCode();
        $data['status'] = 'submitted';
        $data['ip_address'] = $request->ip();

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('village-requests/' . $village->id, 'public');
        }

        $newRequest = VillageRequest::create($data);

        return redirect()
            ->route('village.site.request.success', ['slug' => $slug, 'code' => $newRequest->tracking_code]);
    }

    public function requestSuccess($slug, $code)
    {
        $village = $this->getVillage($slug);

        $request = VillageRequest::where('village_id', $village->id)
            ->where('tracking_code', $code)
            ->firstOrFail();

        return view('village.site.request-success', compact('village', 'request'));
    }

    public function requestTrack($slug)
    {
        $village = $this->getVillage($slug);
        return view('village.site.request-track', compact('village'));
    }

    public function requestTrackResult(Request $request, $slug)
    {
        $village = $this->getVillage($slug);

        $request->validate(['tracking_code' => 'required|string']);

        $found = VillageRequest::where('village_id', $village->id)
            ->where('tracking_code', $request->tracking_code)
            ->first();

        return view('village.site.request-track-result', compact('village', 'found'));
    }

    private function getVillage($slug): Village
    {
        return Village::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
    }
}

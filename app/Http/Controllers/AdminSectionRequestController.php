<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\SectionRequest;
use Illuminate\Http\Request;

class AdminSectionRequestController extends Controller
{
    public function index(Request $request, $sectionId)
    {
        $section = $this->authorizeSection($sectionId);

        $query = SectionRequest::where('section_id', $section->id);

        if ($request->filled('status') && in_array($request->status, ['submitted','reviewing','assigned','processing','resolved','rejected'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('tracking_code', 'like', "%{$q}%")
                    ->orWhere('full_name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('subject', 'like', "%{$q}%");
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $stats = [
            'total'    => SectionRequest::where('section_id', $section->id)->count(),
            'pending'  => SectionRequest::where('section_id', $section->id)->whereIn('status', ['submitted','reviewing','assigned','processing'])->count(),
            'resolved' => SectionRequest::where('section_id', $section->id)->where('status', 'resolved')->count(),
            'rejected' => SectionRequest::where('section_id', $section->id)->where('status', 'rejected')->count(),
        ];

        return view('admin.sections.requests.index', compact('section', 'requests', 'stats'));
    }

    public function show($sectionId, $id)
    {
        $section = $this->authorizeSection($sectionId);
        $request = SectionRequest::where('section_id', $section->id)->findOrFail($id);

        return view('admin.sections.requests.show', compact('section', 'request'));
    }

    public function update(Request $request, $sectionId, $id)
    {
        $section = $this->authorizeSection($sectionId);
        $sectionRequest = SectionRequest::where('section_id', $section->id)->findOrFail($id);

        $data = $request->validate([
            'status'         => 'required|in:submitted,reviewing,assigned,processing,resolved,rejected',
            'admin_response' => 'nullable|string|max:3000',
        ]);

        if ($data['status'] === 'resolved') {
            $data['resolved_at'] = now();
        }

        $sectionRequest->update($data);

        return redirect()
            ->route('admin.sections.requests.show', [$section->id, $sectionRequest->id])
            ->with('success', 'درخواست به‌روزرسانی شد.');
    }

    public function allRequests(Request $request, $sectionId)
    {
        $section = $this->authorizeSection($sectionId);

        $villageIds = \App\Models\Village::where('section_id', $section->id)->pluck('id');

        // درخواست‌های بخشداری
        $sectionQuery = \App\Models\SectionRequest::where('section_id', $section->id);

        // درخواست‌های روستاها
        $villageQuery = \App\Models\VillageRequest::whereIn('village_id', $villageIds)->with('village');

        // فیلتر وضعیت
        if ($request->filled('status') && in_array($request->status, ['submitted','reviewing','assigned','processing','resolved','rejected'])) {
            $sectionQuery->where('status', $request->status);
            $villageQuery->where('status', $request->status);
        }

        // فیلتر روستا
        if ($request->filled('village_id')) {
            $villageQuery->where('village_id', $request->village_id);
            // اگر روستا فیلتر شده، درخواست‌های بخشداری نمایش داده نشن
            $sectionQuery->whereRaw('1 = 0');
        }

        // جستجو
        if ($request->filled('q')) {
            $q = trim($request->q);
            $search = function ($sub) use ($q) {
                $sub->where('tracking_code', 'like', "%{$q}%")
                    ->orWhere('full_name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('subject', 'like', "%{$q}%");
            };
            $sectionQuery->where($search);
            $villageQuery->where($search);
        }

        // گرفتن همه و ادغام
        $sectionReqs = $sectionQuery->get()->map(function ($r) {
            $r->source = 'بخشداری';
            $r->source_slug = null;
            return $r;
        });

        $villageReqs = $villageQuery->get()->map(function ($r) {
            $r->source = optional($r->village)->name ?? 'روستا';
            $r->source_slug = optional($r->village)->slug;
            return $r;
        });

        $all = $sectionReqs->concat($villageReqs)->sortByDesc('created_at')->values();

        // صفحه‌بندی دستی
        $perPage = 20;
        $page = (int) $request->get('page', 1);
        $total = $all->count();
        $items = $all->slice(($page - 1) * $perPage, $perPage)->values();

        $requests = new \Illuminate\Pagination\LengthAwarePaginator(
            $items, $total, $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // آمار
        $stats = [
            'total'    => $total,
            'section'  => $sectionReqs->count(),
            'villages' => $villageReqs->count(),
            'pending'  => $all->whereIn('status', ['submitted','reviewing','assigned','processing'])->count(),
            'resolved' => $all->where('status', 'resolved')->count(),
            'rejected' => $all->where('status', 'rejected')->count(),
        ];

        $villages = \App\Models\Village::where('section_id', $section->id)->orderBy('name')->get();

        return view('admin.sections.requests.all', compact('section', 'requests', 'stats', 'villages'));
    }

    public function showVillageRequest($sectionId, $requestId)
    {
        $section = $this->authorizeSection($sectionId);

        $villageIds = \App\Models\Village::where('section_id', $section->id)->pluck('id');
        $request = \App\Models\VillageRequest::whereIn('village_id', $villageIds)->findOrFail($requestId);

        return view('admin.sections.requests.show-village', compact('section', 'request'));
    }

    private function authorizeSection($sectionId): Section
    {
        $section = Section::findOrFail($sectionId);
        if (!auth()->user()->canManageSection($section->id)) abort(403);
        return $section;
    }
}

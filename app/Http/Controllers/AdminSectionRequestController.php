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

    private function authorizeSection($sectionId): Section
    {
        $section = Section::findOrFail($sectionId);
        if (!auth()->user()->canManageSection($section->id)) abort(403);
        return $section;
    }
}

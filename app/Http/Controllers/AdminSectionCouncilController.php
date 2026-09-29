<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\SectionCouncilMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSectionCouncilController extends Controller
{
    public function index($sectionId)
    {
        $section = $this->authorizeSection($sectionId);
        $members = SectionCouncilMember::where('section_id', $section->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.sections.council.index', compact('section', 'members'));
    }

    public function create($sectionId)
    {
        $section = $this->authorizeSection($sectionId);
        return view('admin.sections.council.form', compact('section'));
    }

    public function store(Request $request, $sectionId)
    {
        $section = $this->authorizeSection($sectionId);

        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'position'   => 'required|string|max:100',
            'phone'      => 'nullable|string|max:20',
            'photo'      => 'nullable|image|max:5120',
            'bio'        => 'nullable|string|max:2000',
            'is_active'  => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $data['section_id'] = $section->id;
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('section-council/' . $section->id, 'public');
        }

        SectionCouncilMember::create($data);

        return redirect()
            ->route('admin.sections.council.index', $section->id)
            ->with('success', 'عضو شورای بخش اضافه شد.');
    }

    public function edit($sectionId, $id)
    {
        $section = $this->authorizeSection($sectionId);
        $member = SectionCouncilMember::where('section_id', $section->id)->findOrFail($id);
        return view('admin.sections.council.form', compact('section', 'member'));
    }

    public function update(Request $request, $sectionId, $id)
    {
        $section = $this->authorizeSection($sectionId);
        $member = SectionCouncilMember::where('section_id', $section->id)->findOrFail($id);

        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'position'   => 'required|string|max:100',
            'phone'      => 'nullable|string|max:20',
            'photo'      => 'nullable|image|max:5120',
            'bio'        => 'nullable|string|max:2000',
            'is_active'  => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('photo')) {
            if ($member->photo && Storage::disk('public')->exists($member->photo)) {
                Storage::disk('public')->delete($member->photo);
            }
            $data['photo'] = $request->file('photo')->store('section-council/' . $section->id, 'public');
        }

        $member->update($data);

        return redirect()
            ->route('admin.sections.council.index', $section->id)
            ->with('success', 'اطلاعات عضو شورا به‌روزرسانی شد.');
    }

    public function destroy($sectionId, $id)
    {
        $section = $this->authorizeSection($sectionId);
        $member = SectionCouncilMember::where('section_id', $section->id)->findOrFail($id);

        if ($member->photo && Storage::disk('public')->exists($member->photo)) {
            Storage::disk('public')->delete($member->photo);
        }

        $member->delete();

        return back()->with('success', 'عضو شورای بخش حذف شد.');
    }

    private function authorizeSection($sectionId): Section
    {
        $section = Section::findOrFail($sectionId);
        if (!auth()->user()->canManageSection($section->id)) abort(403);
        return $section;
    }
}

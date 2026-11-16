<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\SectionStaff;
use Illuminate\Http\Request;

class AdminSectionStaffController extends Controller
{
    private function checkAccess($section_id) {
        if (!auth()->user()->canManageSection($section_id)) abort(403);
    }

    public function index($section_id) {
        $this->checkAccess($section_id);
        $section = Section::findOrFail($section_id);
        $staffs = SectionStaff::where('section_id', $section_id)->orderBy('sort_order')->get();
        return view('admin.sections.staff.index', compact('section', 'staffs'));
    }

    public function create($section_id) {
        $this->checkAccess($section_id);
        $section = Section::findOrFail($section_id);
        return view('admin.sections.staff.create', compact('section'));
    }

    public function store(Request $request, $section_id) {
        $this->checkAccess($section_id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
        ]);
        $data['section_id'] = $section_id;
        SectionStaff::create($data);
        return redirect()->route('admin.sections.staff.index', $section_id)->with('success', 'همکار جدید اضافه شد.');
    }

    public function edit($section_id, $id) {
        $this->checkAccess($section_id);
        $section = Section::findOrFail($section_id);
        $staff = SectionStaff::where('section_id', $section_id)->findOrFail($id);
        return view('admin.sections.staff.edit', compact('section', 'staff'));
    }

    public function update(Request $request, $section_id, $id) {
        $this->checkAccess($section_id);
        $staff = SectionStaff::where('section_id', $section_id)->findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
        ]);
        $staff->update($data);
        return redirect()->route('admin.sections.staff.index', $section_id)->with('success', 'اطلاعات همکار به‌روزرسانی شد.');
    }

    public function destroy($section_id, $id) {
        $this->checkAccess($section_id);
        $staff = SectionStaff::where('section_id', $section_id)->findOrFail($id);
        $staff->delete();
        return redirect()->route('admin.sections.staff.index', $section_id)->with('success', 'همکار حذف شد.');
    }
}

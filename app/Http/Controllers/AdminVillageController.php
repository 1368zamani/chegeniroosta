<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Village;
use Illuminate\Http\Request;

class AdminVillageController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $villages = Village::with('section')->orderBy('name')->get();
        } elseif ($user->isSectionAdmin()) {
            $villages = Village::with('section')
                ->where('section_id', $user->section_id)
                ->orderBy('name')
                ->get();
        } else {
            $villages = Village::with('section')
                ->where('id', $user->village_id)
                ->get();
        }

        return view('admin.villages.index', compact('villages'));
    }

    public function bySection($sectionId)
    {
        $section = Section::findOrFail($sectionId);

        if (!auth()->user()->canManageSection($section->id)) {
            abort(403, 'شما به این بخش دسترسی ندارید.');
        }

        $villages = Village::where('section_id', $section->id)
            ->orderBy('name')
            ->get();

        return view('admin.villages.section', compact('section', 'villages'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $sections = Section::orderBy('name')->get();
        } elseif ($user->isSectionAdmin()) {
            $sections = Section::where('id', $user->section_id)->get();
        } else {
            abort(403);
        }

        $defaultSection = $request->query('section_id');
        return view('admin.villages.create', compact('sections', 'defaultSection'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'slug'              => 'required|string|max:255|unique:villages,slug',
            'subdomain'         => 'nullable|string|max:255|unique:villages,subdomain',
            'section_id'        => 'required|exists:sections,id',
            'district'          => 'nullable|string|max:255',
            'county'            => 'nullable|string|max:255',
            'province'          => 'nullable|string|max:255',
            'description'       => 'nullable|string',
            'address'           => 'nullable|string',
            'phone'             => 'nullable|string|max:50',
            'email'             => 'nullable|email|max:255',
            'population'        => 'nullable|integer|min:0',
            'households'        => 'nullable|integer|min:0',
            'area'              => 'nullable|numeric|min:0',
            'dehyar_name'       => 'nullable|string|max:255',
            'dehyar_instagram'  => 'nullable|string|max:255',
            'dehyar_phone'      => 'nullable|string|max:50',
            'dehyar_email'      => 'nullable|email|max:100',
            'dehyar_hours'      => 'nullable|string|max:255',
            'dehyar_bio'        => 'nullable|string|max:2000',
            'dehyar_education'  => 'nullable|string|max:2000',
            'dehyar_experience' => 'nullable|string|max:2000',
            'council_head_name' => 'nullable|string|max:255',
            'is_active'         => 'nullable|boolean',
        ]);

        if (!$user->canManageSection($data['section_id'])) {
            abort(403);
        }

        $section = Section::find($data['section_id']);
        $data['section'] = $section->name;
        $data['is_active'] = $request->boolean('is_active', true);

        Village::create($data);

        return redirect()
            ->route('admin.sections.villages', $data['section_id'])
            ->with('success', 'روستای جدید با موفقیت اضافه شد.');
    }

    public function edit($id)
    {
        $village = Village::findOrFail($id);

        if (!auth()->user()->canManageVillage($village)) {
            abort(403, 'شما به این روستا دسترسی ندارید.');
        }

        return view('admin.villages.edit', compact('village'));
    }

    public function update(Request $request, $id)
    {
        $village = Village::findOrFail($id);

        if (!auth()->user()->canManageVillage($village)) {
            abort(403);
        }

        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'slug'              => ['required', 'string', 'max:255', 'unique:villages,slug,' . $village->id],
            'subdomain'         => ['nullable', 'string', 'max:255', 'unique:villages,subdomain,' . $village->id],
            'county'            => ['nullable', 'string', 'max:255'],
            'section'           => ['nullable', 'string', 'max:255'],
            'district'          => ['nullable', 'string', 'max:255'],
            'province'          => ['nullable', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],
            'address'           => ['nullable', 'string'],
            'phone'             => ['nullable', 'string', 'max:50'],
            'email'             => ['nullable', 'email', 'max:255'],
            'population'        => ['nullable', 'integer', 'min:0'],
            'households'        => ['nullable', 'integer', 'min:0'],
            'area'              => ['nullable', 'numeric', 'min:0'],
            'dehyar_name'       => ['nullable', 'string', 'max:255'],
            'dehyar_instagram'  => ['nullable', 'string', 'max:255'],
            'dehyar_phone'      => ['nullable', 'string', 'max:50'],
            'dehyar_email'      => ['nullable', 'email', 'max:100'],
            'dehyar_hours'      => ['nullable', 'string', 'max:255'],
            'dehyar_bio'        => ['nullable', 'string', 'max:2000'],
            'dehyar_education'  => ['nullable', 'string', 'max:2000'],
            'dehyar_experience' => ['nullable', 'string', 'max:2000'],
            'council_head_name' => ['nullable', 'string', 'max:255'],
            'is_active'         => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $village->update($validated);

        return redirect()
            ->route('admin.sections.villages', $village->section_id)
            ->with('success', 'اطلاعات روستا با موفقیت به‌روزرسانی شد.');
    }

    public function toggleActive($id)
    {
        $village = Village::findOrFail($id);

        if (!auth()->user()->canManageVillage($village)) {
            abort(403);
        }

        $village->is_active = !$village->is_active;
        $village->save();

        $state = $village->is_active ? 'فعال' : 'غیرفعال';
        return back()->with('success', "روستای «{$village->name}» {$state} شد.");
    }

    /**
     * فعال/غیرفعال کردن گروهی همه‌ی روستاهای یک بخش — با یک دکمه.
     *
     * POST admin/sections/{sectionId}/villages/bulk-toggle
     *
     * دسترسی: سوپرادمین (همه‌ی بخش‌ها) و ادمین بخش (فقط بخش خودش).
     * فقط روستاهایی که وضعیتشان متفاوت است تغییر می‌کنند، پس اگر
     * همه از قبل فعال باشند، هیچ کوئری نوشتنی اجرا نمی‌شود.
     */
    public function bulkToggle(Request $request, $sectionId)
    {
        $section = Section::findOrFail($sectionId);

        if (!auth()->user()->canManageSection($section->id)) {
            abort(403, 'شما به این بخش دسترسی ندارید.');
        }

        $data = $request->validate([
            'state' => ['required', 'in:active,inactive'],
        ], [
            'state.required' => 'وضعیت هدف مشخص نیست.',
            'state.in'       => 'وضعیت انتخاب‌شده معتبر نیست.',
        ]);

        $target = $data['state'] === 'active';

        $affected = Village::where('section_id', $section->id)
            ->where('is_active', '!=', $target)
            ->update(['is_active' => $target]);

        $label = $target ? 'فعال' : 'غیرفعال';

        if ($affected === 0) {
            return back()->with('success', "همه‌ی روستاهای بخش «{$section->name}» از قبل {$label} بودند — تغییری لازم نبود.");
        }

        return back()->with('success', "«{$affected}» روستای بخش «{$section->name}» با موفقیت {$label} شدند.");
    }

    public function destroy($id)
    {
        $village = Village::findOrFail($id);

        if (!auth()->user()->canManageVillage($village)) {
            abort(403);
        }

        $sectionId = $village->section_id;
        $name = $village->name;
        $village->delete();

        return redirect()
            ->route('admin.sections.villages', $sectionId)
            ->with('success', "روستای «{$name}» حذف شد.");
    }
}

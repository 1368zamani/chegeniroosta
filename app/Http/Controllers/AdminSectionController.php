<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSectionController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $sections = Section::orderBy('name')->get();
        } elseif ($user->isSectionAdmin()) {
            $sections = Section::where('id', $user->section_id)->get();
        } else {
            $sections = collect();
        }

        return view('admin.sections.index', compact('sections'));
    }

    public function edit($id)
    {
        $section = Section::findOrFail($id);

        if (!auth()->user()->canManageSection($section->id)) {
            abort(403, 'شما به این بخش دسترسی ندارید.');
        }

        return view('admin.sections.edit', compact('section'));
    }

    public function update(Request $request, $id)
    {
        $section = Section::findOrFail($id);

        if (!auth()->user()->canManageSection($section->id)) {
            abort(403);
        }

        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'subdomain'         => 'nullable|string|max:100',
            'county'            => 'nullable|string|max:100',
            'province'          => 'nullable|string|max:100',
            'description'       => 'nullable|string',
            'address'           => 'nullable|string|max:500',
            'phone'             => 'nullable|string|max:50',
            'email'             => 'nullable|email|max:100',
            'is_active'         => 'nullable|boolean',
            'bakhshdar_name'    => 'nullable|string|max:255',
            'bakhshdar_bio'     => 'nullable|string',
            'bakhshdar_phone'   => 'nullable|string|max:50',
            'bakhshdar_email'   => 'nullable|email|max:100',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $section->update($data);

        return redirect()->route('admin.sections.edit', $section->id)
            ->with('success', 'اطلاعات بخش با موفقیت ذخیره شد.');
    }

    public function uploadPhoto(Request $request, $id)
    {
        $section = Section::findOrFail($id);

        if (!auth()->user()->canManageSection($section->id)) {
            abort(403);
        }

        $request->validate(['photo' => 'required|image|max:5120']);

        if ($section->bakhshdar_photo && Storage::disk('public')->exists($section->bakhshdar_photo)) {
            Storage::disk('public')->delete($section->bakhshdar_photo);
        }

        $path = $request->file('photo')->store('bakhshdars', 'public');
        $section->bakhshdar_photo = $path;
        $section->save();

        return redirect()->route('admin.sections.edit', $section->id)
            ->with('success', 'عکس بخشدار با موفقیت آپلود شد.');
    }

    public function deletePhoto($id)
    {
        $section = Section::findOrFail($id);

        if (!auth()->user()->canManageSection($section->id)) {
            abort(403);
        }

        if ($section->bakhshdar_photo && Storage::disk('public')->exists($section->bakhshdar_photo)) {
            Storage::disk('public')->delete($section->bakhshdar_photo);
        }

        $section->bakhshdar_photo = null;
        $section->save();

        return redirect()->route('admin.sections.edit', $section->id)
            ->with('success', 'عکس بخشدار حذف شد.');
    }
}

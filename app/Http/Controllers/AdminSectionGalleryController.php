<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\SectionGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSectionGalleryController extends Controller
{
    public function index($sectionId)
    {
        $section = Section::findOrFail($sectionId);
        if (!auth()->user()->canManageSection($section->id)) abort(403);

        $images = SectionGallery::where('section_id', $section->id)
            ->orderBy('sort_order')->orderByDesc('id')->get();

        return view('admin.section-gallery.index', compact('section', 'images'));
    }

    public function create($sectionId)
    {
        $section = Section::findOrFail($sectionId);
        if (!auth()->user()->canManageSection($section->id)) abort(403);
        return view('admin.section-gallery.create', compact('section'));
    }

    public function store(Request $request, $sectionId)
    {
        $section = Section::findOrFail($sectionId);
        if (!auth()->user()->canManageSection($section->id)) abort(403);

        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|max:5120',
            'caption' => 'nullable|string|max:255',
        ]);

        $count = 0;
        foreach ($request->file('images') as $file) {
            $path = $file->store('section-gallery/' . $section->id, 'public');
            SectionGallery::create([
                'section_id' => $section->id,
                'image_path' => $path,
                'caption' => $request->input('caption'),
                'sort_order' => $count,
            ]);
            $count++;
        }

        return redirect()->route('admin.sections.gallery.index', $section->id)
            ->with('success', "{$count} تصویر آپلود شد.");
    }

    public function destroy($sectionId, $imageId)
    {
        $section = Section::findOrFail($sectionId);
        if (!auth()->user()->canManageSection($section->id)) abort(403);

        $image = SectionGallery::findOrFail($imageId);
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }
        $image->delete();

        return redirect()->route('admin.sections.gallery.index', $section->id)
            ->with('success', 'تصویر حذف شد.');
    }
}

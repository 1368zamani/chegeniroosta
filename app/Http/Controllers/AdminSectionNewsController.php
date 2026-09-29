<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\SectionNews;
use App\Models\SectionNewsImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminSectionNewsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $sectionId = $request->query('section_id');

        $query = SectionNews::with(['section', 'user'])->orderBy('created_at', 'desc');

        if ($user->isSuperAdmin()) {
            if ($sectionId) {
                $query->where('section_id', $sectionId);
            }
        } elseif ($user->isSectionAdmin()) {
            $query->where('section_id', $user->section_id);
        } else {
            abort(403);
        }

        $news = $query->paginate(20);
        $sections = $user->isSuperAdmin()
            ? Section::orderBy('name')->get()
            : Section::where('id', $user->section_id)->get();

        return view('admin.section-news.index', compact('news', 'sections', 'sectionId'));
    }

    public function create()
    {
        $user = auth()->user();
        $sections = $user->isSuperAdmin()
            ? Section::orderBy('name')->get()
            : Section::where('id', $user->section_id)->get();

        return view('admin.section-news.create', compact('sections'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'section_id'   => 'required|exists:sections,id',
            'title'        => 'required|string|max:255',
            'excerpt'      => 'nullable|string|max:500',
            'content'      => 'required|string',
            'image'        => 'nullable|image|max:25600',
            'images'       => 'nullable|array',
            'images.*'     => 'image|max:25600',
            'is_pinned'    => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ]);

        if (!auth()->user()->canManageSection($data['section_id'])) {
            abort(403);
        }

        $data['user_id'] = auth()->id();
        $data['slug'] = $this->makeUniqueSlug($data['title']);
        $data['is_pinned'] = $request->boolean('is_pinned');
        $data['is_published'] = $request->boolean('is_published', true);
        $data['published_at'] = $data['is_published'] ? now() : null;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('section-news', 'public');
        }

        $news = SectionNews::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
                if (!$file || !$file->isValid()) continue;
                $path = $file->store('section-news/' . $news->id . '/gallery', 'public');
                SectionNewsImage::create([
                    'news_id'    => $news->id,
                    'image'      => $path,
                    'sort_order' => $i,
                ]);
            }
        }

        return redirect()
            ->route('admin.section-news.index')
            ->with('success', 'خبر با موفقیت منتشر شد.');
    }

    public function edit($id)
    {
        $news = SectionNews::findOrFail($id);

        if (!auth()->user()->canManageSection($news->section_id)) {
            abort(403);
        }

        $user = auth()->user();
        $sections = $user->isSuperAdmin()
            ? Section::orderBy('name')->get()
            : Section::where('id', $user->section_id)->get();

        return view('admin.section-news.edit', compact('news', 'sections'));
    }

    public function update(Request $request, $id)
    {
        $news = SectionNews::findOrFail($id);

        if (!auth()->user()->canManageSection($news->section_id)) {
            abort(403);
        }

        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'excerpt'      => 'nullable|string|max:500',
            'content'      => 'required|string',
            'image'        => 'nullable|image|max:25600',
            'images'       => 'nullable|array',
            'images.*'     => 'image|max:25600',
            'is_pinned'    => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ]);

        $data['is_pinned'] = $request->boolean('is_pinned');
        $data['is_published'] = $request->boolean('is_published');

        if ($data['is_published'] && !$news->published_at) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('image')) {
            if ($news->image && Storage::disk('public')->exists($news->image)) {
                Storage::disk('public')->delete($news->image);
            }
            $data['image'] = $request->file('image')->store('section-news', 'public');
        }

        $news->update($data);

        if ($request->hasFile('images')) {
            $maxOrder = $news->images()->max('sort_order') ?? 0;
            foreach ($request->file('images') as $i => $file) {
                if (!$file || !$file->isValid()) continue;
                $path = $file->store('section-news/' . $news->id . '/gallery', 'public');
                SectionNewsImage::create([
                    'news_id'    => $news->id,
                    'image'      => $path,
                    'sort_order' => $maxOrder + $i + 1,
                ]);
            }
        }

        return redirect()
            ->route('admin.section-news.index')
            ->with('success', 'خبر به‌روزرسانی شد.');
    }

    public function destroy($id)
    {
        $news = SectionNews::findOrFail($id);

        if (!auth()->user()->canManageSection($news->section_id)) {
            abort(403);
        }

        foreach ($news->images as $img) {
            if ($img->image && Storage::disk('public')->exists($img->image)) {
                Storage::disk('public')->delete($img->image);
            }
            $img->delete();
        }

        if ($news->image && Storage::disk('public')->exists($news->image)) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        return redirect()
            ->route('admin.section-news.index')
            ->with('success', 'خبر حذف شد.');
    }

    public function destroyImage($id, $imageId)
    {
        $news = SectionNews::findOrFail($id);

        if (!auth()->user()->canManageSection($news->section_id)) {
            abort(403);
        }

        $img = SectionNewsImage::where('news_id', $news->id)->findOrFail($imageId);

        if ($img->image && Storage::disk('public')->exists($img->image)) {
            Storage::disk('public')->delete($img->image);
        }
        $img->delete();

        return back()->with('success', 'عکس حذف شد.');
    }

    private function makeUniqueSlug($title)
    {
        $base = Str::slug($title, '-', 'fa') ?: 'news-' . time();
        $slug = $base;
        $i = 1;
        while (SectionNews::where('slug', $slug)->exists()) {
            $slug = $base . '-' . (++$i);
        }
        return $slug;
    }
}

<?php

namespace App\Http\Controllers\VillageAdmin;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillageNews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index($villageId)
    {
        $village = $this->authorizeVillage($villageId);
        $news = VillageNews::where('village_id', $village->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('admin.village.news.index', compact('village', 'news'));
    }

    public function create($villageId)
    {
        $village = $this->authorizeVillage($villageId);
        return view('admin.village.news.form', compact('village'));
    }

    public function store(Request $request, $villageId)
    {
        $village = $this->authorizeVillage($villageId);

        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'excerpt'      => 'nullable|string|max:500',
            'body'         => 'required|string',
            'image'        => 'nullable|image|max:5120',
            'is_pinned'    => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        $data['village_id'] = $village->id;
        $data['user_id'] = auth()->id();
        $data['slug'] = $this->uniqueSlug($village->id, $data['title']);
        $data['is_pinned'] = $request->boolean('is_pinned');
        $data['is_published'] = $request->boolean('is_published', true);
        $data['is_active'] = true;
        $data['published_at'] = !empty($data['published_at']) ? $data['published_at'] : now();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('village-news/' . $village->id, 'public');
        }

        VillageNews::create($data);

        return redirect()->route('admin.village.news.index', $village->id)
            ->with('success', 'خبر با موفقیت ثبت شد.');
    }

    public function edit($villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $news = VillageNews::where('village_id', $village->id)->findOrFail($id);
        return view('admin.village.news.form', compact('village', 'news'));
    }

    public function update(Request $request, $villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $news = VillageNews::where('village_id', $village->id)->findOrFail($id);

        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'excerpt'      => 'nullable|string|max:500',
            'body'         => 'required|string',
            'image'        => 'nullable|image|max:5120',
            'is_pinned'    => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        $data['is_pinned'] = $request->boolean('is_pinned');
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            if ($news->image && Storage::disk('public')->exists($news->image)) {
                Storage::disk('public')->delete($news->image);
            }
            $data['image'] = $request->file('image')->store('village-news/' . $village->id, 'public');
        }

        $news->update($data);

        return redirect()->route('admin.village.news.index', $village->id)
            ->with('success', 'خبر به‌روزرسانی شد.');
    }

    public function destroy($villageId, $id)
    {
        $village = $this->authorizeVillage($villageId);
        $news = VillageNews::where('village_id', $village->id)->findOrFail($id);
        if ($news->image && Storage::disk('public')->exists($news->image)) {
            Storage::disk('public')->delete($news->image);
        }
        $news->delete();
        return back()->with('success', 'خبر حذف شد.');
    }

    private function authorizeVillage($villageId): Village
    {
        $village = Village::findOrFail($villageId);
        if (!auth()->user()->canManageVillage($village)) abort(403);
        return $village;
    }

    private function uniqueSlug($villageId, $title): string
    {
        $base = Str::slug($title, '-', 'fa') ?: 'news-' . time();
        $slug = $base;
        $i = 1;
        while (VillageNews::where('village_id', $villageId)->where('slug', $slug)->exists()) {
            $slug = $base . '-' . (++$i);
        }
        return $slug;
    }
}

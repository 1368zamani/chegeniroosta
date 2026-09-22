<?php

namespace App\Http\Controllers;

use App\Models\Village;
use App\Models\VillageNews;
use Illuminate\Http\Request;

class AdminVillageNewsController extends Controller
{
    public function index(Village $village)
    {
        $news = $village->news()
            ->latest()
            ->get();

        return view('admin.news.index', compact('village', 'news'));
    }

    public function create(Village $village)
    {
        return view('admin.news.create', compact('village'));
    }

    public function store(Request $request, Village $village)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        if (empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug(
                $validated['title']
            );
        }

        $village->news()->create($validated);

        return redirect()
            ->route('admin.village.news.index', $village)
            ->with('success', 'خبر با موفقیت ثبت شد.');
    }

    public function edit(Village $village, VillageNews $news)
    {
        abort_unless($news->village_id === $village->id, 404);

        return view('admin.news.edit', compact('village', 'news'));
    }

    public function update(
        Request $request,
        Village $village,
        VillageNews $news
    ) {
        abort_unless($news->village_id === $village->id, 404);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        if (empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug(
                $validated['title']
            );
        }

        $news->update($validated);

        return redirect()
            ->route('admin.village.news.index', $village)
            ->with('success', 'خبر با موفقیت ویرایش شد.');
    }

    public function destroy(Village $village, VillageNews $news)
    {
        abort_unless($news->village_id === $village->id, 404);

        $news->delete();

        return redirect()
            ->route('admin.village.news.index', $village)
            ->with('success', 'خبر حذف شد.');
    }
}

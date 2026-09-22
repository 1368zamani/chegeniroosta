<?php

namespace App\Http\Controllers;

use App\Models\Village;
use App\Models\VillageNews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VillageNewsController extends Controller
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
            'body' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')
                ->store('news', 'public');
        }

        VillageNews::create([
            'village_id' => $village->id,
            'title' => $validated['title'],
            'body' => $validated['body'],
            'image' => $imagePath,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.news.index', $village->id)
            ->with('success', 'خبر با موفقیت ثبت شد.');
    }

    public function edit(Village $village, VillageNews $news)
    {
        abort_unless($news->village_id === $village->id, 404);

        return view('admin.news.edit', compact('village', 'news'));
    }

    public function update(Request $request, Village $village, VillageNews $news)
    {
        abort_unless($news->village_id === $village->id, 404);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {

            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }

            $news->image = $request->file('image')
                ->store('news', 'public');
        }

        $news->title = $validated['title'];
        $news->body = $validated['body'];
        $news->is_active = $request->boolean('is_active');
        $news->save();

        return redirect()
            ->route('admin.news.index', $village->id)
            ->with('success', 'خبر با موفقیت ویرایش شد.');
    }

    public function destroy(Village $village, VillageNews $news)
    {
        abort_unless($news->village_id === $village->id, 404);

        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        return redirect()
            ->route('admin.news.index', $village->id)
            ->with('success', 'خبر با موفقیت حذف شد.');
    }
}

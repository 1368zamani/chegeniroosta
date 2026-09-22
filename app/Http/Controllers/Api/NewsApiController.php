<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\SectionNews;

class NewsApiController extends Controller
{
    public function index()
    {
        $sectionSlug = request('section');

        $query = SectionNews::with('section')
            ->where('is_published', true);

        if ($sectionSlug) {
            $section = Section::where('slug', $sectionSlug)->first();
            if ($section) $query->where('section_id', $section->id);
        }

        $news = $query->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->paginate(20);

        $data = collect($news->items())->map(function ($n) {
            $sec = $n->section;
            $sub = $sec ? $sec->subdomain : '';
            return [
                'id' => $n->id,
                'title' => $n->title,
                'slug' => $n->slug,
                'excerpt' => $n->excerpt,
                'image' => $n->image ? asset('storage/' . $n->image) : null,
                'section' => $sec ? $sec->name : null,
                'is_pinned' => (bool) $n->is_pinned,
                'published_at' => ($n->published_at ?: $n->created_at)->toIso8601String(),
                'url' => $sub
                    ? request()->getScheme() . '://' . $sub . '.' . config('app.base_domain') . '/news/' . $n->slug
                    : null,
            ];
        });

        return response()->json([
            'success' => true,
            'pagination' => [
                'current_page' => $news->currentPage(),
                'total' => $news->total(),
                'per_page' => $news->perPage(),
                'last_page' => $news->lastPage(),
            ],
            'data' => $data,
        ]);
    }

    public function show($slug)
    {
        $news = SectionNews::with('section')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $section = $news->section;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $news->id,
                'title' => $news->title,
                'slug' => $news->slug,
                'excerpt' => $news->excerpt,
                'content' => $news->content,
                'image' => $news->image ? asset('storage/' . $news->image) : null,
                'section' => $section ? [
                    'name' => $section->name,
                    'slug' => $section->slug,
                ] : null,
                'is_pinned' => (bool) $news->is_pinned,
                'published_at' => ($news->published_at ?: $news->created_at)->toIso8601String(),
                'url' => $section
                    ? request()->getScheme() . '://' . $section->subdomain . '.' . config('app.base_domain') . '/news/' . $news->slug
                    : null,
            ],
        ]);
    }
}

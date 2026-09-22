<?php

namespace App\Http\Controllers;

use App\Models\SectionNews;
use Illuminate\Http\Request;

class PublicSectionNewsController extends Controller
{
    public function index(Request $request)
    {
        if (!app()->bound('currentSection')) {
            abort(404);
        }

        $section = app('currentSection');

        $news = $section->sectionNews()
            ->where('is_published', true)
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return view('section.news.index', compact('section', 'news'));
    }

    public function show(Request $request, $slug)
    {
        if (!app()->bound('currentSection')) {
            abort(404);
        }

        $section = app('currentSection');

        $news = $section->sectionNews()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        // اخبار مرتبط (آخرین اخبار)
        $related = $section->sectionNews()
            ->where('is_published', true)
            ->where('id', '!=', $news->id)
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();

        return view('section.news.show', compact('section', 'news', 'related'));
    }
}

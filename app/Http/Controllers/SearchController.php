<?php

namespace App\Http\Controllers;

use App\Models\Village;
use App\Models\Section;
use App\Models\SectionNews;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q = trim($request->input('q', ''));

        if (mb_strlen($q) < 2) {
            return view('search.results', [
                'query' => $q,
                'villages' => collect(),
                'sections' => collect(),
                'news' => collect(),
            ]);
        }

        $villages = Village::where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%")
                    ->orWhere('dehyar_name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(50)
            ->get();

        $sections = Section::where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get();

        $news = SectionNews::where('is_published', true)
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('excerpt', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%");
            })
            ->orderBy('published_at', 'desc')
            ->limit(30)
            ->get();

        return view('search.results', compact('q', 'villages', 'sections', 'news') + ['query' => $q]);
    }
}

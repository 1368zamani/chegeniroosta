<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SectionSiteController extends Controller
{
    public function home(Request $request)
    {
        if (!app()->bound('currentSection')) {
            abort(404);
        }

        $section = app('currentSection');

        $villages = $section->villages()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $news = $section->sectionNews()
            ->where('is_published', true)
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        // گالری تصاویر بخش
        $gallery = [];
        if (method_exists($section, 'galleryImages')) {
            $gallery = $section->galleryImages()->limit(8)->get();
        }


        return view('section.home', compact(
            'section',
            'villages',
            'news',
            'gallery'
        ));
    }

    public function bakhshdar(Request $request)
    {
        if (!app()->bound('currentSection')) {
            abort(404);
        }

        $section = app('currentSection');
        return view('section.bakhshdar', compact('section'));
    }
}

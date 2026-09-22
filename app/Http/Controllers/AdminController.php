<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Village;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            $sections = Section::withCount('villages')->orderBy('name')->get();
        } elseif ($user->isSectionAdmin()) {
            $sections = Section::where('id', $user->section_id)
                ->withCount('villages')
                ->get();
        } else {
            $sections = collect();
        }

        $stats = [
            'total_villages'    => \App\Models\Village::count(),
            'total_sections'    => \App\Models\Section::count(),
            'total_users'       => \App\Models\User::count(),
            'total_news'        => class_exists('App\Models\SectionNews') ? \App\Models\SectionNews::count() : 0,
            'total_gallery'     => class_exists('App\Models\SectionGallery') ? \App\Models\SectionGallery::count() : 0,
            'total_village_img' => class_exists('App\Models\VillageImage') ? \App\Models\VillageImage::count() : 0,
            'total_population'  => \App\Models\Village::sum('population') ?: 0,
            'total_households'  => \App\Models\Village::sum('households') ?: 0,
            'recent_news'       => class_exists('App\Models\SectionNews')
                ? \App\Models\SectionNews::with('section')->orderBy('created_at', 'desc')->limit(5)->get()
                : collect(),
            'recent_villages'   => \App\Models\Village::orderBy('created_at', 'desc')->limit(5)->get(),
        ];

        return view('admin.dashboard', compact('sections', 'stats'));
    }

    public function create()
    {
        $sections = Section::orderBy('name')->get();
        return view('admin.villages.create', compact('sections'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => ['required', 'string', 'max:255', 'unique:villages,slug'],
            'section_id'  => ['nullable', 'exists:sections,id'],
            'county'      => ['nullable', 'string', 'max:255'],
            'section'     => ['nullable', 'string', 'max:255'],
            'province'    => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        Village::create($validated);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'روستا با موفقیت ثبت شد.');
    }
}

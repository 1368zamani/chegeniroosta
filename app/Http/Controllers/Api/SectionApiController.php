<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Village;
use App\Models\SectionNews;

class SectionApiController extends Controller
{
    public function index()
    {
        $sections = Section::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($s) => $this->formatSection($s));

        return response()->json([
            'success' => true,
            'data' => $sections,
        ]);
    }

    public function show($slug)
    {
        $section = Section::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $this->formatSection($section, true),
        ]);
    }

    public function villages($slug)
    {
        $section = Section::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $villages = Village::where('section_id', $section->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($v) => $this->formatVillageBasic($v));

        return response()->json([
            'success' => true,
            'section' => $section->name,
            'total' => $villages->count(),
            'data' => $villages,
        ]);
    }

    public function news($slug)
    {
        $section = Section::where('slug', $slug)->firstOrFail();

        $news = SectionNews::where('section_id', $section->id)
            ->where('is_published', true)
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->limit(20)
            ->get()
            ->map(fn($n) => [
                'id' => $n->id,
                'title' => $n->title,
                'slug' => $n->slug,
                'excerpt' => $n->excerpt,
                'image' => $n->image ? asset('storage/' . $n->image) : null,
                'is_pinned' => (bool) $n->is_pinned,
                'published_at' => ($n->published_at ?: $n->created_at)->toIso8601String(),
                'url' => request()->getScheme() . '://' . $section->subdomain . '.' . config('app.base_domain') . '/news/' . $n->slug,
            ]);

        return response()->json([
            'success' => true,
            'section' => $section->name,
            'total' => $news->count(),
            'data' => $news,
        ]);
    }

    public function stats()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_sections' => Section::where('is_active', true)->count(),
                'total_villages' => Village::where('is_active', true)->count(),
                'total_news' => SectionNews::where('is_published', true)->count(),
                'total_population' => (int) Village::sum('population'),
                'total_households' => (int) Village::sum('households'),
            ],
        ]);
    }

    private function formatSection($s, $detailed = false)
    {
        $base = [
            'id' => $s->id,
            'name' => $s->name,
            'slug' => $s->slug,
            'subdomain' => $s->subdomain,
            'county' => $s->county,
            'province' => $s->province,
            'description' => $s->description,
            'villages_count' => $s->villages()->count(),
            'url' => request()->getScheme() . '://' . $s->subdomain . '.' . config('app.base_domain'),
        ];

        if ($detailed) {
            $base['bakhshdar'] = [
                'name' => $s->bakhshdar_name,
                'photo' => $s->bakhshdar_photo ? asset('storage/' . $s->bakhshdar_photo) : null,
                'bio' => $s->bakhshdar_bio,
                'phone' => $s->bakhshdar_phone,
                'email' => $s->bakhshdar_email,
            ];
            $base['address'] = $s->address;
            $base['phone'] = $s->phone;
            $base['email'] = $s->email;
        }

        return $base;
    }

    private function formatVillageBasic($v)
    {
        return [
            'id' => $v->id,
            'name' => $v->name,
            'slug' => $v->slug,
            'district' => $v->district,
            'population' => (int) $v->population,
            'households' => (int) $v->households,
            'dehyar_name' => $v->dehyar_name,
            'phone' => $v->phone,
            'url' => request()->getScheme() . '://' . config('app.base_domain') . '/village/' . $v->slug,
        ];
    }
}

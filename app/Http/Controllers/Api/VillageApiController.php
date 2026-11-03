<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Village;
use App\Models\VillageImage;

class VillageApiController extends Controller
{
    public function index()
    {
        $village_id = request('section_id');

        $query = Village::with('section')->where('is_active', true);

        if ($village_id) {
            $query->where('section_id', $village_id);
        }

        $villages = $query->orderBy('name')->paginate(50);

        return response()->json([
            'success' => true,
            'pagination' => [
                'current_page' => $villages->currentPage(),
                'total' => $villages->total(),
                'per_page' => $villages->perPage(),
                'last_page' => $villages->lastPage(),
            ],
            'data' => $villages->items(),
        ]);
    }

    public function show($slug)
    {
        $village = Village::with('section')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $images = VillageImage::where('village_id', $village->id)
            ->orderBy('id')
            ->limit(20)
            ->get()
            ->map(fn($i) => [
                'id' => $i->id,
                'url' => asset('storage/' . $i->image_path),
                'caption' => $i->caption,
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $village->id,
                'name' => $village->name,
                'slug' => $village->slug,
                'section' => $village->section,
                'district' => $village->district,
                'county' => $village->county,
                'province' => $village->province,
                'description' => $village->description,
                'address' => $village->address,
                'phone' => $village->phone,
                'email' => $village->email,
                'population' => (int) $village->population,
                'households' => (int) $village->households,
                'area' => (float) $village->area,
                'dehyar_name' => $village->dehyar_name,
                'council_head_name' => $village->council_head_name,
                'cover_image' => $village->cover_image ? asset('storage/' . $village->cover_image) : null,
                'gallery' => $images,
                'url' => request()->getScheme() . '://' . config('app.base_domain') . '/village/' . $village->slug,
            ],
        ]);
    }

    public function gallery($slug)
    {
        $village = Village::where('slug', $slug)->firstOrFail();

        $images = VillageImage::where('village_id', $village->id)
            ->orderBy('id')
            ->get()
            ->map(fn($i) => [
                'id' => $i->id,
                'url' => asset('storage/' . $i->image_path),
                'caption' => $i->caption,
            ]);

        return response()->json([
            'success' => true,
            'total' => $images->count(),
            'data' => $images,
        ]);
    }

    public function news($slug)
    {
        $village = Village::where('slug', $slug)->firstOrFail();

        $news = [];
        if (class_exists('App\\Models\\VillageNews')) {
            $news = \App\Models\VillageNews::where('village_id', $village->id)
                ->where('is_published', true)
                ->orderBy('published_at', 'desc')
                ->limit(20)
                ->get()
                ->map(fn($n) => [
                    'id' => $n->id,
                    'title' => $n->title,
                    'slug' => $n->slug,
                    'excerpt' => $n->excerpt,
                    'image' => $n->image ? asset('storage/' . $n->image) : null,
                    'published_at' => ($n->published_at ?: $n->created_at)->toIso8601String(),
                ]);
        }

        return response()->json([
            'success' => true,
            'village' => $village->name,
            'total' => count($news),
            'data' => $news,
        ]);
    }

    public function search()
    {
        $q = trim(request('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'حداقل ۲ کاراکتر وارد کنید',
                'data' => [],
            ], 422);
        }

        $villages = Village::where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('dehyar_name', 'like', "%{$q}%");
            })
            ->limit(30)
            ->get()
            ->map(fn($v) => [
                'id' => $v->id,
                'name' => $v->name,
                'slug' => $v->slug,
                'section' => $v->section,
                'population' => (int) $v->population,
                'url' => request()->getScheme() . '://' . config('app.base_domain') . '/village/' . $v->slug,
            ]);

        return response()->json([
            'success' => true,
            'query' => $q,
            'total' => $villages->count(),
            'data' => $villages,
        ]);
    }
}

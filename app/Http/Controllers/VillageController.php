<?php

namespace App\Http\Controllers;

use App\Models\Village;

class VillageController extends Controller
{
    public function show($slug)
    {
        $village = Village::with([
            'images',
            'contact',
            'news' => function ($query) {
                $query->where('is_active', true)
                    ->latest();
            }
        ])
        ->where('slug', $slug)
        ->where('is_active', true)
        ->firstOrFail();

        return view('village.show', compact('village'));
    }
}

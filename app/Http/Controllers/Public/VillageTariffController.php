<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\TariffSet;
use App\Models\Village;
use Illuminate\Http\Request;

class VillageTariffController extends Controller
{
    /**
     * صفحه عمومی تعرفه یک روستا.
     * فقط مجموعه‌های published (اعلام عمومی) نمایش داده می‌شوند.
     */
    public function show(Request $request, string $slug)
    {
        $village = Village::where('slug', $slug)->firstOrFail();

        // لیست سال‌های منتشرشده
        $publishedYears = TariffSet::where('village_id', $village->id)
            ->where('status', 'published')
            ->orderByDesc('jalali_year')
            ->pluck('jalali_year');

        if ($publishedYears->isEmpty()) {
            return view('village.tariff.empty', compact('village'));
        }

        $year = (int) ($request->query('year') ?? $publishedYears->first());

        $set = TariffSet::with(['items.title', 'parameters'])
            ->where('village_id', $village->id)
            ->where('jalali_year', $year)
            ->where('status', 'published')
            ->firstOrFail();

        $levyItems = $set->items
            ->filter(fn($i) => $i->title?->type === 'levy' && $i->is_active)
            ->sortBy(fn($i) => $i->title?->sort_order);

        $serviceItems = $set->items
            ->filter(fn($i) => $i->title?->type === 'service_fee' && $i->is_active)
            ->sortBy(fn($i) => $i->title?->sort_order);

        $penaltyItems = $set->items
            ->filter(fn($i) => $i->title?->type === 'penalty' && $i->is_active);

        return view('village.tariff.show', compact(
            'village', 'set', 'year', 'publishedYears',
            'levyItems', 'serviceItems', 'penaltyItems'
        ));
    }
}

<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\TariffSet;
use App\Models\Village;
use App\Services\Tariff\Calculators\CalculatorRegistry;
use Illuminate\Http\Request;

class TariffController extends Controller
{
    public function show(Request $request, string $slug, CalculatorRegistry $registry)
    {
        $village = Village::where('slug', $slug)->firstOrFail();

        $years = TariffSet::where('village_id', $village->id)
            ->where('status', 'published')
            ->orderByDesc('jalali_year')
            ->pluck('jalali_year');

        if ($years->isEmpty()) {
            abort(404, 'تعرفه‌ای برای این روستا اعلام نشده است.');
        }

        $year = (int) ($request->query('year') ?: $years->first());

        $set = TariffSet::with(['items' => fn($q) => $q->where('is_active', true), 'items.title'])
            ->where('village_id', $village->id)
            ->where('jalali_year', $year)
            ->where('status', 'published')
            ->firstOrFail();

        $grouped = $set->items
            ->filter(fn($i) => $i->title)
            ->groupBy(fn($i) => $i->title->type);

        // ساخت schema ماشین‌حساب برای آیتم‌های دارای calculator
        $schemas = [];
        foreach ($set->items as $item) {
            if (!$item->is_active || !$item->title) continue;
            $key = $item->title->formula_key;
            if (!$key || !$registry->has($key)) continue;
            $calc = $registry->get($key);
            $schemas[$item->id] = [
                'label'  => $calc->label(),
                'schema' => $calc->inputSchema(),
            ];
        }

        return view('public.tariff.show', compact('village', 'set', 'grouped', 'years', 'year', 'schemas'));
    }
}

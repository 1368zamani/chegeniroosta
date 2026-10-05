<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\TariffSet;
use App\Models\Village;
use Illuminate\Http\Request;

class PublicSectionTariffController extends Controller
{
    public function index(Request $request)
    {
        $section = $this->resolveSection($request);

        $villages = Village::where('section_id', $section->id)->orderBy('name')->get();
        $villageIds = $villages->pluck('id');

        $allSets = TariffSet::whereIn('village_id', $villageIds)
            ->orderByDesc('jalali_year')->get()->groupBy('village_id');

        $published = TariffSet::whereIn('village_id', $villageIds)
            ->where('status', 'published')->orderByDesc('jalali_year')
            ->get()->groupBy('village_id')->map(fn($s) => $s->first());

        $stats = [
            'total'     => $villages->count(),
            'published' => $published->count(),
            'pending'   => $allSets->filter(fn($s) => $s->first()->status !== 'published')->count(),
            'none'      => $villages->count() - $allSets->count(),
        ];

        return view('section.site.tariff', compact('section', 'villages', 'allSets', 'published', 'stats'));
    }

    private function resolveSection(Request $request): Section
    {
        if ($request->attributes->has('section')) return $request->attributes->get('section');
        if (app()->bound('currentSection')) return app('currentSection');
        try { $s = view()->shared('section'); if ($s) return $s; } catch (\Throwable $e) {}

        $parts = explode('.', $request->getHost());
        if (count($parts) > 2) {
            $s = Section::where('subdomain', $parts[0])->first();
            if ($s) return $s;
        }
        return Section::firstOrFail();
    }
}

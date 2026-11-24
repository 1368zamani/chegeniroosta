<?php

namespace App\Http\Controllers;

use App\Models\LevyVillageParam;
use App\Models\Section;
use App\Models\Village;
use Illuminate\Http\Request;

class PublicSectionTariffController extends Controller
{
    public function index(Request $request)
    {
        // This route belongs to section subdomains only; never fall back to a section on the main domain.
        if (!app()->bound('currentSection')) {
            abort(404);
        }

        $section = $this->resolveSection($request);

        $villages = Village::where('section_id', $section->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $params = LevyVillageParam::whereIn('village_id', $villages->pluck('id'))
            ->get()
            ->keyBy('village_id');

        $stats = [
            'total'   => $villages->count(),
            'ready'   => $params->count(),
            'pending' => $villages->count() - $params->count(),
        ];

        return view('section.site.tariff', compact('section', 'villages', 'params', 'stats'));
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

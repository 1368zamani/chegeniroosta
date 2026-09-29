<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\SectionCouncilMember;

class PublicSectionController extends Controller
{
    private function getSection(): Section
    {
        if (!app()->bound('currentSection')) {
            abort(404, 'بخش یافت نشد.');
        }
        return app('currentSection');
    }

    public function council()
    {
        $section = $this->getSection();

        $members = SectionCouncilMember::where('section_id', $section->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('section.site.council', compact('section', 'members'));
    }
}

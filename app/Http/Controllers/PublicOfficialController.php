<?php

namespace App\Http\Controllers;

use App\Models\CountyOfficial;
use App\Models\Section;

class PublicOfficialController extends Controller
{
    public function governor()
    {
        $governor = CountyOfficial::governor();

        if (!$governor) {
            abort(404, 'اطلاعات فرماندار ثبت نشده است.');
        }

        return view('public.officials.governor', compact('governor'));
    }

    public function deputies()
    {
        $deputies = CountyOfficial::deputies();
        return view('public.officials.deputies', compact('deputies'));
    }

    public function deputy($id)
    {
        $deputy = CountyOfficial::where('type', 'deputy')->active()->findOrFail($id);
        return view('public.officials.deputy', compact('deputy'));
    }

    public function bakhshdars()
    {
        $sections = Section::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('public.officials.bakhshdars', compact('sections'));
    }
}

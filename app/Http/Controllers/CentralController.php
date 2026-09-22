<?php

namespace App\Http\Controllers;

use App\Models\Section;

class CentralController extends Controller
{
    public function home()
    {
        $sections = Section::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('central.home', compact('sections'));
    }
}

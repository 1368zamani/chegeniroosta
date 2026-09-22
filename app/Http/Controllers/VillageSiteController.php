<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VillageSiteController extends Controller
{
    public function home(Request $request)
    {
        if (!app()->bound('currentVillage')) {
            abort(404);
        }

        $village = app('currentVillage');

        return view('village.home', compact('village'));
    }
}

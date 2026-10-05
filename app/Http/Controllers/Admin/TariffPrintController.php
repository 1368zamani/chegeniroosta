<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TariffSet;
use Illuminate\Support\Facades\Gate;

class TariffPrintController extends Controller
{
    public function form2(TariffSet $tariffSet)
    {
        Gate::authorize('view', $tariffSet);
        $tariffSet->load(['village', 'items.title']);
        return view('admin.tariffs.print.form2', compact('tariffSet'));
    }

    public function form3(TariffSet $tariffSet)
    {
        Gate::authorize('view', $tariffSet);
        $tariffSet->load(['village', 'items.title']);
        return view('admin.tariffs.print.form3', compact('tariffSet'));
    }
}

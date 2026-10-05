<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TariffSet;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;

class TariffPdfController extends Controller
{
    public function form2(TariffSet $tariffSet)
    {
        Gate::authorize('view', $tariffSet);
        $tariffSet->load(['village', 'items.title']);

        $pdf = Pdf::loadView('admin.tariffs.print.form2', [
            'tariffSet' => $tariffSet,
            'pdf'       => true,
        ])->setPaper('a4', 'landscape');

        return $pdf->download("form2-{$tariffSet->village?->slug}-{$tariffSet->jalali_year}.pdf");
    }

    public function form3(TariffSet $tariffSet)
    {
        Gate::authorize('view', $tariffSet);
        $tariffSet->load(['village', 'items.title']);

        $pdf = Pdf::loadView('admin.tariffs.print.form3', [
            'tariffSet' => $tariffSet,
            'pdf'       => true,
        ])->setPaper('a4', 'landscape');

        return $pdf->download("form3-{$tariffSet->village?->slug}-{$tariffSet->jalali_year}.pdf");
    }
}

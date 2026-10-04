<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TariffSet;
use Barryvdh\DomPDF\Facade\Pdf;

class TariffFormController extends Controller
{
    /**
     * فرم شماره ۲ — عوارض محلی
     */
    public function form2(int $id)
    {
        $set = TariffSet::with(['village.section', 'items.title'])->findOrFail($id);
        $this->authorize('view', $set);

        $items = $set->items
            ->filter(fn($i) => $i->title?->type === 'levy')
            ->sortBy(fn($i) => $i->title?->sort_order);

        $pdf = Pdf::loadView('admin.tariffs.pdf.form2', compact('set', 'items'));
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions(['defaultFont' => 'Vazirmatn', 'isRemoteEnabled' => true, 'direction' => 'rtl']);

        return $pdf->download("form2-{$set->jalali_year}-{$set->village?->slug}.pdf");
    }

    /**
     * فرم شماره ۳ — بهای خدمات
     */
    public function form3(int $id)
    {
        $set = TariffSet::with(['village.section', 'items.title'])->findOrFail($id);
        $this->authorize('view', $set);

        $items = $set->items
            ->filter(fn($i) => $i->title?->type === 'service_fee')
            ->sortBy(fn($i) => $i->title?->sort_order);

        $pdf = Pdf::loadView('admin.tariffs.pdf.form3', compact('set', 'items'));
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions(['defaultFont' => 'Vazirmatn', 'isRemoteEnabled' => true, 'direction' => 'rtl']);

        return $pdf->download("form3-{$set->jalali_year}-{$set->village?->slug}.pdf");
    }
}

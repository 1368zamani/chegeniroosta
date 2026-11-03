<?php

namespace App\Http\Controllers;

use App\Models\TariffSet;
use App\Models\VillageCouncilResolution;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * دانلود عمومی فایل‌ها + شمارش تعداد دانلود.
 *
 * توجه امنیتی: این کنترلر فقط چیزهایی را سرو می‌کند که
 * از قبل برای عموم قابل مشاهده‌اند (مصوبات عمومی و تعرفه‌ی منتشرشده).
 * پیوست درخواست‌های مردمی از این مسیر سرو نمی‌شود.
 */
class DownloadController extends Controller
{
    /** نام کوتاه در URL => [کلاس مدل، نام ستون فایل] */
    private const FILE_TYPES = [
        'resolution' => [VillageCouncilResolution::class, 'attachment'],
    ];

    /**
     * دانلود فایل پیوست.
     * GET /download/{type}/{id}
     */
    public function file(Request $request, string $type, int $id)
    {
        abort_unless(isset(self::FILE_TYPES[$type]), 404);

        [$modelClass, $column] = self::FILE_TYPES[$type];

        /** @var \Illuminate\Database\Eloquent\Model $record */
        $record = $modelClass::findOrFail($id);

        $this->authorizeFile($type, $record);

        $path = $record->{$column};
        abort_if(blank($path), 404);
        abort_unless(Storage::disk('public')->exists($path), 404);

        if (method_exists($record, 'recordDownload')) {
            $record->recordDownload();
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $downloadName = $this->downloadName($record, $extension);

        return Storage::disk('public')->download($path, $downloadName);
    }

    /**
     * دانلود PDF رسمی تعرفه‌ی منتشرشده (فرم ۲ یا ۳).
     * GET /download/tariff-pdf/{tariffSet}/{form}
     */
    public function tariffPdf(TariffSet $tariffSet, string $form = 'form2')
    {
        // فقط تعرفه‌ی اعلام عمومی‌شده قابل دانلود است
        abort_unless($tariffSet->status === 'published', 404);
        abort_unless(in_array($form, ['form2', 'form3'], true), 404);

        $tariffSet->load(['village', 'items.title']);

        if (method_exists($tariffSet, 'recordDownload')) {
            $tariffSet->recordDownload();
        }

        $pdf = Pdf::loadView("admin.tariffs.print.{$form}", [
            'tariffSet' => $tariffSet,
            'pdf'       => true,
        ])->setPaper('a4', 'landscape');

        $slug = $tariffSet->village?->slug ?? 'village';

        return $pdf->download("tariff-{$form}-{$slug}-{$tariffSet->jalali_year}.pdf");
    }

    /**
     * کنترل دسترسی — به‌صورت پیش‌فرض: ممنوع.
     */
    private function authorizeFile(string $type, $record): void
    {
        if ($type === 'resolution') {
            abort_unless((bool) $record->is_public, 404);
            abort_unless((bool) optional($record->village)->is_active, 404);

            return;
        }

        abort(404);
    }

    private function downloadName($record, string $extension): string
    {
        $base = $record->tracking_code
            ?? $record->session_number
            ?? ('file-' . $record->getKey());

        $base = preg_replace('/[^A-Za-z0-9\-_]/', '-', (string) $base) ?: ('file-' . $record->getKey());

        return $extension !== '' ? "{$base}.{$extension}" : $base;
    }
}

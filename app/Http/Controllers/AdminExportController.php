<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Village;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminExportController extends Controller
{
    public function exportVillages(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $sectionId = $request->query('section_id');

        $query = Village::with('section')->orderBy('section_id')->orderBy('name');
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        $villages = $query->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="villages-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($villages) {
            $out = fopen('php://output', 'w');

            // BOM برای UTF-8 در Excel
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'شناسه', 'نام روستا', 'بخش', 'دهستان', 'شهرستان', 'استان',
                'خانوار', 'جمعیت', 'دهیار', 'تلفن', 'Subdomain', 'وضعیت'
            ]);

            foreach ($villages as $v) {
                fputcsv($out, [
                    $v->id,
                    $v->name,
                    $v->section ?? '',
                    $v->district ?? '',
                    $v->county ?? '',
                    $v->province ?? '',
                    $v->households ?? 0,
                    $v->population ?? 0,
                    $v->dehyar_name ?? '',
                    $v->phone ?? '',
                    $v->subdomain ?? '',
                    $v->is_active ? 'فعال' : 'غیرفعال',
                ]);
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}

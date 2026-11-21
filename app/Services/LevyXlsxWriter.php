<?php

namespace App\Services;

/**
 * تولیدکنندهٔ حداقلی فایل XLSX معتبر (بدون وابستگی خارجی).
 * فرمولها بهصورت زنده در سلولها نوشته میشوند تا با تغییر ورودیها در اکسل،
 * خروجیها دوباره محاسبه شوند — دقیقاً مشابه فایل مرجع.
 */
class LevyXlsxWriter
{
    private const NS = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';

    private static function esc(string $s): string
    {
        return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private static function col(int $index): string
    {
        $s = '';
        $n = $index + 1;
        while ($n > 0) {
            $n--;
            $s = chr(65 + ($n % 26)) . $s;
            $n = intdiv($n, 26);
        }
        return $s;
    }

    /** @return array<int,array<int,array{v:mixed,f:?string,s:int,t:?string}>> */
    private static function blankSheet(): array
    {
        return [];
    }

    private static function set(array &$sheet, int $row, int $col, $value, int $style = 0, ?string $formula = null): void
    {
        $sheet[$row][$col] = [
            'v' => $value,
            'f' => $formula,
            's' => $style,
            't' => is_string($value) ? 'inlineStr' : null,
        ];
    }

    private static function renderSheet(array $sheet, bool $rtl = true): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="' . self::NS . '">'
            . '<sheetViews><sheetView workbookViewId="0"' . ($rtl ? ' rightToLeft="1"' : '') . '/></sheetViews>'
            . '<sheetFormatPr defaultRowHeight="16"/>'
            . '<sheetData>';

        ksort($sheet);
        foreach ($sheet as $r => $cols) {
            $xml .= '<row r="' . ($r + 1) . '">';
            ksort($cols);
            foreach ($cols as $c => $cell) {
                $ref = self::col($c) . ($r + 1);
                $style = $cell['s'] ? ' s="' . $cell['s'] . '"' : '';
                $xml .= '<c r="' . $ref . '"' . $style;
                if ($cell['t']) {
                    $xml .= ' t="inlineStr"><is><t xml:space="preserve">' . self::esc((string) $cell['v']) . '</t></is>';
                } else {
                    if ($cell['f'] !== null) {
                        $xml .= '><f>' . self::esc($cell['f']) . '</f>';
                        $xml .= '<v>' . (is_numeric($cell['v']) ? $cell['v'] : 0) . '</v>';
                    } else {
                        $xml .= '><v>' . (is_numeric($cell['v']) ? $cell['v'] : 0) . '</v>';
                    }
                }
                $xml .= '</c>';
            }
            $xml .= '</row>';
        }
        $xml .= '</sheetData></worksheet>';
        return $xml;
    }

    private static function styles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="' . self::NS . '">'
            . '<fonts count="2">'
            . '<font><sz val="11"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="11"/><name val="Calibri"/></font>'
            . '</fonts>'
            . '<fills count="3">'
            . '<fill><patternFill patternType="none"/></fill>'
            . '<fill><patternFill patternType="gray125"/></fill>'
            . '<fill><patternFill patternType="solid"><fgColor rgb="FFDCE6F1"/><bgColor indexed="64"/></patternFill></fill>'
            . '</fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="3">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/>'
            . '<xf numFmtId="3" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
            . '</cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';
    }

    /**
     * @param \Illuminate\Support\Collection $files
     */
    public static function build($files): string
    {
        // ─── شیت ۱: جزئیات هر پرونده (مشابه ساختار اکسل اصلی) ───
        $detail = self::blankSheet();
        $r = 0;
        $seq = 0;

        foreach ($files as $file) {
            $village = $file->village;
            $params  = $file->params;
            $seq++;

            self::set($detail, $r, 0, 'عوارض احداث بنا در محدوده قانونی روستاها — ' . $village?->name . ' (' . $file->jalali_year . ')', 1);
            $r++;

            self::set($detail, $r, 0, 'نام و نام خانوادگی متقاضی', 1);
            self::set($detail, $r, 1, (string) $file->applicant_name);
            self::set($detail, $r, 2, 'نام دهیاری و شماره حساب', 1);
            self::set($detail, $r, 3, (string) $file->dehyari_name . ' ' . (string) $file->dehyari_account_no);
            $r++;

            self::set($detail, $r, 0, 'گروه روستا', 1);
            self::set($detail, $r, 1, (string) $file->village_group);
            self::set($detail, $r, 2, 'وضعیت طبقه', 1);
            self::set($detail, $r, 3, (string) $file->class_status);
            $r++;

            self::set($detail, $r, 0, 'عنوان عوارض', 1);
            self::set($detail, $r, 1, 'مبلغ عوارض (ریال)', 1);
            $r++;
            $firstLine = $r;
            foreach ($file->lines as $line) {
                self::set($detail, $r, 0, $line->title);
                self::set($detail, $r, 1, (int) $line->amount, 2);
                $r++;
            }
            $lastLine = $r - 1;
            self::set($detail, $r, 0, 'مبلغ نهایی عوارض احداث بنا (ریال)', 1);
            self::set($detail, $r, 1, $file->lines_total, 2, "SUM(B" . ($firstLine + 1) . ":B" . ($lastLine + 1) . ")");
            $r++;

            self::set($detail, $r, 0, 'شماره شبا', 1);
            self::set($detail, $r, 1, (string) $file->iban);
            self::set($detail, $r, 2, 'شماره شناسه', 1);
            self::set($detail, $r, 3, (string) $file->id_number);
            $r += 2;

            // جدول مرجع سمت راست اکسل
            $headers = ['ردیف', 'نام روستا', 'گروه', 'ارزش معاملاتی (M)', 'ضریب نهایی (N)', 'ضریب ساخت (O)',
                'ضریب تراکم (P)', 'مساحت ساخت (Q)', 'مساحت ملک (R)', 'عوارض ساخت (S)', 'عوارض تراکم (T)', 'عوارض نهایی (U)'];
            foreach ($headers as $c => $h) {
                self::set($detail, $r, $c, $h, 1);
            }
            $r++;

            $dataRow = $r; // 0-based
            $excelRow = $dataRow + 1;
            self::set($detail, $dataRow, 0, $seq, 2);
            self::set($detail, $dataRow, 1, (string) $village?->name);
            self::set($detail, $dataRow, 2, (string) $params?->group);
            self::set($detail, $dataRow, 3, (int) ($params?->transaction_value ?? 0), 2);
            self::set($detail, $dataRow, 4, $file->final_coefficient, 2, "D{$excelRow}*0.14");
            self::set($detail, $dataRow, 5, (int) ($params?->construction_coefficient ?? 0), 2);
            self::set($detail, $dataRow, 6, (int) ($params?->density_coefficient ?? 0), 2);
            self::set($detail, $dataRow, 7, (float) $file->construction_area, 2);
            self::set($detail, $dataRow, 8, (float) $file->property_area, 2);
            self::set($detail, $dataRow, 9, $file->residential_levy, 2, "H{$excelRow}*E{$excelRow}*F{$excelRow}");
            $source = $params?->density_area_source === 'R' ? 'I' : 'H';
            self::set($detail, $dataRow, 10, $file->density_levy, 2, "E{$excelRow}*{$source}{$excelRow}*G{$excelRow}");
            self::set($detail, $dataRow, 11, $file->final_levy, 2, "MAX(J{$excelRow}:K{$excelRow})");
            $r += 3;
        }

        // ─── شیت ۲: خلاصه تجمیعی ───
        $summary = self::blankSheet();
        $heads = ['روستا', 'سال', 'متقاضی', 'جمع عوارض (H)', 'عوارض ساخت (S)', 'عوارض تراکم (T)', 'عوارض نهایی (U)', 'وضعیت'];
        foreach ($heads as $c => $h) {
            self::set($summary, 0, $c, $h, 1);
        }
        $i = 1;
        foreach ($files as $file) {
            self::set($summary, $i, 0, (string) $file->village?->name);
            self::set($summary, $i, 1, (string) $file->jalali_year);
            self::set($summary, $i, 2, (string) $file->applicant_name);
            self::set($summary, $i, 3, $file->lines_total, 2);
            self::set($summary, $i, 4, $file->residential_levy, 2);
            self::set($summary, $i, 5, $file->density_levy, 2);
            self::set($summary, $i, 6, $file->final_levy, 2);
            self::set($summary, $i, 7, (string) $file->status);
            $i++;
        }
        $totalRow = $i + 1;
        $excelTotal = $totalRow + 1;
        self::set($summary, $totalRow, 0, 'جمع کل بخش', 1);
        foreach ([3, 4, 5, 6] as $c) {
            $col = self::col($c);
            self::set($summary, $totalRow, $c, 0, 2, "SUM({$col}2:{$col}{$i})");
        }

        // ─── شیت ۳: جدول مرجع ضرایب تمام روستاها ───
        $ref = self::blankSheet();
        $refHeads = ['ردیف', 'نام روستا', 'گروه', 'ارزش معاملاتی (M)', 'ضریب نهایی (N=M*0.14)', 'ضریب ساخت (O)',
            'ضریب تراکم (P)', 'منبع مساحت تراکم'];
        foreach ($refHeads as $c => $h) {
            self::set($ref, 0, $c, $h, 1);
        }
        $params = \App\Models\LevyVillageParam::with('village')->get()
            ->sortBy(fn($p) => $p->village?->name ?? '');
        $i = 0;
        foreach ($params as $param) {
            $row = ++$i;
            $excelRow = $row + 1;
            self::set($ref, $row, 0, $i, 2);
            self::set($ref, $row, 1, (string) $param->village?->name);
            self::set($ref, $row, 2, (string) $param->group);
            self::set($ref, $row, 3, (int) $param->transaction_value, 2);
            self::set($ref, $row, 4, $param->final_coefficient, 2, "D{$excelRow}*0.14");
            self::set($ref, $row, 5, (int) $param->construction_coefficient, 2);
            self::set($ref, $row, 6, (int) $param->density_coefficient, 2);
            self::set($ref, $row, 7, $param->area_source_label);
        }

        // ─── بستهبندی XLSX ───
        $sheets = [$detail, $summary, $ref];
        $names = ['عوارض', 'خلاصه تجمیعی', 'جدول مرجع'];
        $tmp = tempnam(sys_get_temp_dir(), 'levyxlsx_');
        $zip = new \ZipArchive();
        $zip->open($tmp, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>';
        foreach ([1, 2, 3] as $n) {
            $contentTypes .= '<Override PartName="/xl/worksheets/sheet' . $n . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }
        $contentTypes .= '</Types>';

        $zip->addFromString('[Content_Types].xml', $contentTypes);
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>');

        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="' . self::NS . '" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets>';
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
        foreach ($names as $i => $name) {
            $id = $i + 1;
            $workbook .= '<sheet name="' . self::esc($name) . '" sheetId="' . $id . '" r:id="rId' . $id . '"/>';
            $rels .= '<Relationship Id="rId' . $id . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $id . '.xml"/>';
        }
        $rels .= '<Relationship Id="rId4" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>';
        $workbook .= '</sheets></workbook>';

        $zip->addFromString('xl/workbook.xml', $workbook);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $rels);
        $zip->addFromString('xl/styles.xml', self::styles());
        foreach ($sheets as $i => $sheet) {
            $zip->addFromString('xl/worksheets/sheet' . ($i + 1) . '.xml', self::renderSheet($sheet));
        }
        $zip->close();

        $bytes = file_get_contents($tmp);
        unlink($tmp);
        return $bytes;
    }
}

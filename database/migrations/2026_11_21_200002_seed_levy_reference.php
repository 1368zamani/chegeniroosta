<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * ضرایب مرجع اکسل را روی «تمام روستاهای موجود سامانه» اعمال میکند.
     * روستایی که نامش در اکسل نیامده، پیشفرض گروه الف (M=39000, O=30, P=30) میگیرد
     * و کارشناس میتواند آن را ویرایش کند.
     */
    public function up(): void
    {
        $villagesFile = database_path('data/levy_villages.json');
        $accountsFile = database_path('data/levy_accounts.json');

        if (!file_exists($villagesFile)) {
            throw new RuntimeException("Missing reference file: {$villagesFile}");
        }

        $excelRows = json_decode(file_get_contents($villagesFile), true, 512, JSON_THROW_ON_ERROR);

        // نرملایز نام برای تطبیق با روستاهای دیتابیس
        $norm = static function (string $s): string {
            $s = str_replace(['ي', 'ى', 'ئ', 'ك', 'ـ', ' '], ['ی', 'ی', 'ی', 'ک', '', ''], $s);
            $s = preg_replace('/^روستای/u', '', $s);
            return mb_strtolower(trim($s), 'UTF-8');
        };

        $byName = [];
        foreach ($excelRows as $row) {
            $byName[$norm($row['name'])] = $row;
        }

        $now = now();
        $matched = 0;
        $rows = [];

        foreach (DB::table('villages')->select('id', 'name')->orderBy('id')->get() as $village) {
            $key = $norm($village->name);
            $ref = $byName[$key] ?? null;

            if ($ref) {
                $matched++;
                $group   = $ref['group'];
                $m       = $ref['transaction_value'];
                $o       = $ref['construction_coefficient']; // میشکر already fixed to 30 in seed file
                $p       = $ref['density_coefficient'];
                $source  = $ref['density_area_source'];
                $note    = 'از جدول مرجع اکسل (ردیف ' . $ref['row_no'] . ')';
            } else {
                $group   = 'الف';
                $m       = 39000;
                $o       = 30;
                $p       = 30;
                $source  = 'Q';
                $note    = 'پیشفرض گروه الف — قابل ویرایش توسط کارشناس';
            }

            $rows[] = [
                'village_id'               => $village->id,
                'group'                    => $group,
                'transaction_value'        => $m,
                'construction_coefficient' => $o,
                'density_coefficient'      => $p,
                'density_area_source'      => $source,
                'source_note'              => $note,
                'created_at'               => $now,
                'updated_at'               => $now,
            ];
        }

        $paramsCount = count($rows);
        DB::table('levy_village_params')->insert($rows);

        if (file_exists($accountsFile)) {
            $accounts = json_decode(file_get_contents($accountsFile), true, 512, JSON_THROW_ON_ERROR);
            $rows = [];
            foreach ($accounts as $i => $a) {
                $rows[] = [
                    'account_no' => $a['account_no'],
                    'label'      => $a['label'],
                    'sort_order' => $i + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            DB::table('levy_accounts')->insert($rows);
        }

        info("Levy seed complete: params for {$paramsCount} villages, {$matched} matched by Excel name.");
    }

    public function down(): void
    {
        DB::table('levy_village_params')->truncate();
        DB::table('levy_accounts')->truncate();
    }
};

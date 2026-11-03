<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * آمار یک روزِ یک قطعه محتوا.
 *
 * چرا لازم است؟ جدول `content_stats` فقط «مجموع کل» را دارد و از آن
 * نمی‌شود فهمید کدام روز چند بازدید داشته‌ایم. این جدول برای نمودار
 * روند و «بازدید امروز» استفاده می‌شود.
 */
class ContentStatDaily extends Model
{
    protected $table = 'content_stat_daily';

    protected $fillable = [
        'stat_date',
        'statable_type',
        'statable_id',
        'section_id',
        'village_id',
        'views',
        'downloads',
    ];

    protected $casts = [
        'stat_date' => 'date',
        'views'     => 'integer',
        'downloads' => 'integer',
    ];

    /**
     * افزایش اتمی شمارنده‌ی امروز.
     */
    public static function bump(
        string $type,
        int $id,
        string $column,
        ?int $sectionId = null,
        ?int $villageId = null
    ): void {
        if (!in_array($column, ['views', 'downloads'], true)) {
            throw new \InvalidArgumentException("شمارنده‌ی نامعتبر: {$column}");
        }

        $today = Carbon::today()->toDateString();
        $now   = now();

        $affected = static::query()
            ->where('stat_date', $today)
            ->where('statable_type', $type)
            ->where('statable_id', $id)
            ->update([
                $column      => DB::raw("`{$column}` + 1"),
                'updated_at' => $now,
            ]);

        if ($affected > 0) {
            return;
        }

        try {
            static::create([
                'stat_date'     => $today,
                'statable_type' => $type,
                'statable_id'   => $id,
                'section_id'    => $sectionId,
                'village_id'    => $villageId,
                $column         => 1,
            ]);
        } catch (UniqueConstraintViolationException $e) {
            static::query()
                ->where('stat_date', $today)
                ->where('statable_type', $type)
                ->where('statable_id', $id)
                ->update([
                    $column      => DB::raw("`{$column}` + 1"),
                    'updated_at' => $now,
                ]);
        }
    }

    /**
     * مجموع بازدید/دانلود به تفکیک روز برای N روز اخیر.
     *
     * @return array<string, array{views:int, downloads:int}>  کلید = تاریخ (Y-m-d)
     */
    public static function dailyTotals(int $days = 30, ?int $sectionId = null): array
    {
        $from = Carbon::today()->subDays($days - 1)->toDateString();

        $q = static::query()
            ->where('stat_date', '>=', $from)
            ->groupBy('stat_date')
            ->select([
                'stat_date',
                DB::raw('SUM(views) as v'),
                DB::raw('SUM(downloads) as d'),
            ]);

        if ($sectionId !== null) {
            $q->where('section_id', $sectionId);
        }

        $out = [];
        foreach ($q->get() as $row) {
            $key = Carbon::parse($row->stat_date)->toDateString();
            $out[$key] = [
                'views'     => (int) $row->v,
                'downloads' => (int) $row->d,
            ];
        }

        return $out;
    }
}

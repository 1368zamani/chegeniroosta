<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

/**
 * آمار بازدید و دانلود یک قطعه محتوا.
 */
class ContentStat extends Model
{
    protected $table = 'content_stats';

    protected $fillable = [
        'statable_type',
        'statable_id',
        'views',
        'downloads',
        'last_viewed_at',
        'last_downloaded_at',
    ];

    protected $casts = [
        'views'              => 'integer',
        'downloads'          => 'integer',
        'last_viewed_at'     => 'datetime',
        'last_downloaded_at' => 'datetime',
    ];

    public function statable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * افزایش اتمی یک شمارنده.
     *
     * اگر ردیف آمار وجود نداشته باشد ساخته می‌شود؛ اگر بین خواندن و
     * نوشتن، درخواست دیگری همان ردیف را بسازد، مقدار دوباره افزایش می‌یابد
     * تا شمارش گم نشود.
     */
    public static function bump(string $type, int $id, string $column, ?int $sectionId = null, ?int $villageId = null): void
    {
        if (!in_array($column, ['views', 'downloads'], true)) {
            throw new \InvalidArgumentException("شمارنده‌ی نامعتبر: {$column}");
        }

        $timeColumn = $column === 'views' ? 'last_viewed_at' : 'last_downloaded_at';
        $now = now();

        // ═══ ۱) مجموع کل ═══
        $affected = static::query()
            ->where('statable_type', $type)
            ->where('statable_id', $id)
            ->update([
                $column      => DB::raw("`{$column}` + 1"),
                $timeColumn  => $now,
                'updated_at' => $now,
            ]);

        if ($affected === 0) {
            try {
                static::create([
                    'statable_type' => $type,
                    'statable_id'   => $id,
                    $column         => 1,
                    $timeColumn     => $now,
                ]);
            } catch (UniqueConstraintViolationException $e) {
                // درخواست هم‌زمان دیگری ردیف را ساخت → فقط افزایش بده
                static::query()
                    ->where('statable_type', $type)
                    ->where('statable_id', $id)
                    ->update([
                        $column      => DB::raw("`{$column}` + 1"),
                        $timeColumn  => $now,
                        'updated_at' => $now,
                    ]);
            }
        }

        // ═══ ۲) آمار امروز (برای نمودار روند) ═══
        // اگر این مرحله خطا بدهد، شمارش کلی نباید از دست برود.
        try {
            ContentStatDaily::bump($type, $id, $column, $sectionId, $villageId);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('ثبت آمار روزانه ناموفق بود', [
                'type'  => $type,
                'id'    => $id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

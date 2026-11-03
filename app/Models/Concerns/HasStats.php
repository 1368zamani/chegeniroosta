<?php

namespace App\Models\Concerns;

use App\Models\ContentStat;
use App\Models\Village;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * به مدل‌هایی اضافه می‌شود که باید بازدید و دانلودشان شمرده شود.
 *
 * در قالب‌ها:
 *     {{ $news->stat_views }}        تعداد بازدید
 *     {{ $news->stat_downloads }}    تعداد دانلود
 *
 * در کنترلر:
 *     $news->recordView();           ثبت یک بازدید (هر نشست یک بار)
 *     $resolution->recordDownload(); ثبت یک دانلود
 */
trait HasStats
{
    public function stat(): MorphOne
    {
        return $this->morphOne(ContentStat::class, 'statable');
    }

    /** تعداد بازدید (۰ اگر هنوز آماری ثبت نشده) */
    public function getStatViewsAttribute(): int
    {
        return (int) ($this->stat->views ?? 0);
    }

    /** تعداد دانلود (۰ اگر هنوز آماری ثبت نشده) */
    public function getStatDownloadsAttribute(): int
    {
        return (int) ($this->stat->downloads ?? 0);
    }

    /**
     * ثبت یک بازدید.
     * برای جلوگیری از باد کردن عدد با رفرش‌های پیاپی، در هر نشست
     * هر محتوا فقط یک بار شمرده می‌شود.
     */
    public function recordView(): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        $key = $this->statSessionKey('v');

        if (session()->has($key)) {
            return;
        }

        session()->put($key, true);

        [$sectionId, $villageId] = $this->statScopeIds();

        ContentStat::bump($this->getMorphClass(), (int) $this->getKey(), 'views', $sectionId, $villageId);
        $this->unsetRelation('stat');
    }

    /** ثبت یک دانلود (بدون محدودیت نشست — هر دانلود شمرده می‌شود) */
    public function recordDownload(): void
    {
        [$sectionId, $villageId] = $this->statScopeIds();

        ContentStat::bump($this->getMorphClass(), (int) $this->getKey(), 'downloads', $sectionId, $villageId);
        $this->unsetRelation('stat');
    }

    /**
     * شناسه‌ی بخش و روستای مرتبط با این محتوا.
     *
     * برای اینکه هر ادمین بخش فقط آمار بخش خودش را ببیند، هنگام ثبت آمار
     * این دو مقدار هم ذخیره می‌شوند. اگر مدل خودش «بخش» باشد، بخشِ آمار
     * خالی می‌ماند و همه‌ی سوپرادمین‌ها آن را می‌بینند.
     *
     * @return array{0: ?int, 1: ?int}
     */
    private function statScopeIds(): array
    {
        $villageId = $this->village_id ?? null;
        $sectionId = $this->section_id ?? null;

        // اگر خودِ مدل «روستا» است، شناسه‌اش همان village_id است
        if ($this->getTable() === 'villages') {
            $villageId = $this->getKey();
        }

        // اگر فقط روستا داریم، بخشش را از خود روستا می‌گیریم
        if ($sectionId === null && $villageId !== null) {
            $sectionId = Village::query()->whereKey($villageId)->value('section_id');
        }

        return [
            $sectionId !== null ? (int) $sectionId : null,
            $villageId !== null ? (int) $villageId : null,
        ];
    }

    private function statSessionKey(string $prefix): string
    {
        return 'stat.' . $prefix . '.' . md5($this->getMorphClass() . '|' . $this->getKey());
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentStat;
use App\Models\ContentStatDaily;
use App\Models\Section;
use App\Models\SectionNews;
use App\Models\TariffSet;
use App\Models\Village;
use App\Models\VillageCouncilResolution;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * صفحه‌ی «آمار سایت» در پنل ادمین.
 *
 * دسترسی:
 *   • سوپرادمین  → آمار کل سایت
 *   • ادمین بخش  → فقط آمار بخش خودش
 *   • بقیه نقش‌ها → ۴۰۳
 */
class StatsController extends Controller
{
    private const TREND_DAYS = 30;
    private const TOP_LIMIT  = 10;

    public function index()
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $isSuper = $user->isSuperAdmin();

        $sectionId = (! $isSuper && $user->isSectionAdmin() && $user->section_id)
            ? (int) $user->section_id
            : null;

        abort_unless($isSuper || $sectionId !== null, 403, 'شما اجازه دیدن آمار را ندارید.');

        return view('admin.stats.index', [
            'isSuper'      => $isSuper,
            'section'      => $sectionId ? Section::find($sectionId) : null,
            'summary'      => $this->summary($sectionId),
            'topNews'      => $this->topContent(SectionNews::class, 'title', $sectionId),
            'topVillages'  => $this->topContent(Village::class, 'name', $sectionId),
            'topDownloads' => $this->topDownloads($sectionId),
            'trend'        => $this->trend($sectionId),
            'trendMax'     => $this->trendMax,
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    // کارت‌های خلاصه
    // ══════════════════════════════════════════════════════════════
    private function summary(?int $sectionId): array
    {
        $today = Carbon::today()->toDateString();

        if ($sectionId === null) {
            $totals = ContentStat::query()
                ->selectRaw('COALESCE(SUM(views),0) as v, COALESCE(SUM(downloads),0) as d')
                ->first();
        } else {
            $villageIds = Village::where('section_id', $sectionId)->pluck('id')->all();
            $newsIds    = SectionNews::where('section_id', $sectionId)->pluck('id')->all();
            $setIds     = TariffSet::whereIn('village_id', $villageIds)->pluck('id')->all();
            $resIds     = VillageCouncilResolution::whereIn('village_id', $villageIds)->pluck('id')->all();

            $totals = ContentStat::query()
                ->where(function ($q) use ($villageIds, $newsIds, $setIds, $resIds) {
                    $q->orWhere(fn ($x) => $x->where('statable_type', Village::class)
                                              ->whereIn('statable_id', $villageIds))
                      ->orWhere(fn ($x) => $x->where('statable_type', SectionNews::class)
                                              ->whereIn('statable_id', $newsIds))
                      ->orWhere(fn ($x) => $x->where('statable_type', TariffSet::class)
                                              ->whereIn('statable_id', $setIds))
                      ->orWhere(fn ($x) => $x->where('statable_type', VillageCouncilResolution::class)
                                              ->whereIn('statable_id', $resIds));
                })
                ->selectRaw('COALESCE(SUM(views),0) as v, COALESCE(SUM(downloads),0) as d')
                ->first();
        }

        $todayRow = ContentStatDaily::query()
            ->where('stat_date', $today)
            ->when($sectionId !== null, fn ($q) => $q->where('section_id', $sectionId))
            ->selectRaw('COALESCE(SUM(views),0) as v, COALESCE(SUM(downloads),0) as d')
            ->first();

        return [
            'views'           => (int) ($totals->v ?? 0),
            'downloads'       => (int) ($totals->d ?? 0),
            'views_today'     => (int) ($todayRow->v ?? 0),
            'downloads_today' => (int) ($todayRow->d ?? 0),
            'news'            => SectionNews::query()
                ->when($sectionId !== null, fn ($q) => $q->where('section_id', $sectionId))
                ->count(),
            'villages'        => Village::query()
                ->when($sectionId !== null, fn ($q) => $q->where('section_id', $sectionId))
                ->where('is_active', true)
                ->count(),
        ];
    }

    // ══════════════════════════════════════════════════════════════
    // پربازدیدترین محتوا
    // ══════════════════════════════════════════════════════════════
    private function topContent(string $modelClass, string $titleColumn, ?int $sectionId): Collection
    {
        // کمی بیشتر می‌گیریم تا بعد از فیلترِ «بخش»، به تعداد نهایی برسیم
        $stats = ContentStat::query()
            ->where('statable_type', $modelClass)
            ->where('views', '>', 0)
            ->orderByDesc('views')
            ->limit(self::TOP_LIMIT * 5)
            ->get();

        if ($stats->isEmpty()) {
            return collect();
        }

        $model = new $modelClass();

        $items = $modelClass::query()
            ->whereIn($model->getKeyName(), $stats->pluck('statable_id')->all())
            ->when($sectionId !== null, fn ($q) => $q->where('section_id', $sectionId))
            ->get()
            ->keyBy(fn ($m) => $m->getKey());

        return $stats
            ->map(function ($s) use ($items, $titleColumn) {
                $m = $items->get($s->statable_id);

                if (! $m) {
                    return null;
                }

                return [
                    'title'     => $m->{$titleColumn} ?: ('#' . $s->statable_id),
                    'views'     => (int) $s->views,
                    'downloads' => (int) $s->downloads,
                    'url'       => $this->publicUrl($m),
                ];
            })
            ->filter()
            ->take(self::TOP_LIMIT)
            ->values();
    }

    /** لینک عمومی محتوا (اگر وجود داشته باشد) */
    private function publicUrl($model): ?string
    {
        try {
            if ($model instanceof Village) {
                return route('public.village.show', $model->slug);
            }
        } catch (\Throwable $e) {
            return null;
        }

        return null;
    }

    // ══════════════════════════════════════════════════════════════
    // پردانلودترین‌ها
    // ══════════════════════════════════════════════════════════════
    private function topDownloads(?int $sectionId): Collection
    {
        $labels = [
            TariffSet::class                => 'تعرفه‌ی عوارض',
            VillageCouncilResolution::class => 'مصوبه‌ی شورا',
        ];

        $rows = ContentStat::query()
            ->where('downloads', '>', 0)
            ->whereIn('statable_type', array_keys($labels))
            ->orderByDesc('downloads')
            ->limit(60)
            ->get();

        $out = collect();

        foreach ($rows as $s) {
            /** @var \Illuminate\Database\Eloquent\Model|null $model */
            $model = $s->statable_type::find($s->statable_id);

            if (! $model) {
                continue;
            }

            // محدودسازی به بخش ادمین
            if ($sectionId !== null) {
                $villageId = $model->village_id ?? null;

                if (! $villageId
                    || ! Village::whereKey($villageId)->where('section_id', $sectionId)->exists()) {
                    continue;
                }
            }

            $title = match (true) {
                $model instanceof TariffSet => 'تعرفه‌ی ' . ($model->village?->name ?? '—')
                    . ' — سال ' . $model->jalali_year,
                $model instanceof VillageCouncilResolution => $model->title
                    ?: ('مصوبه #' . $model->id),
                default => '#' . $s->statable_id,
            };

            $out->push([
                'title'     => $title,
                'kind'      => $labels[$s->statable_type],
                'downloads' => (int) $s->downloads,
                'views'     => (int) $s->views,
            ]);

            if ($out->count() >= self::TOP_LIMIT) {
                break;
            }
        }

        return $out;
    }

    // ══════════════════════════════════════════════════════════════
    // نمودار روند
    // ══════════════════════════════════════════════════════════════

    /** بیشینه‌ی بازدید روزانه — برای مقیاس نمودار */
    private int $trendMax = 1;

    private function trend(?int $sectionId): array
    {
        $totals = ContentStatDaily::dailyTotals(self::TREND_DAYS, $sectionId);

        $max = 1;
        foreach ($totals as $row) {
            $max = max($max, $row['views']);
        }
        $this->trendMax = $max;

        $out = [];

        for ($i = self::TREND_DAYS - 1; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $key = $day->toDateString();
            $row = $totals[$key] ?? ['views' => 0, 'downloads' => 0];

            $out[] = [
                'date'      => $key,
                'label'     => \Morilog\Jalali\Jalalian::fromCarbon($day)->format('m/d'),
                'views'     => $row['views'],
                'downloads' => $row['downloads'],
                'percent'   => (int) round($row['views'] / $max * 100),
            ];
        }

        return $out;
    }
}

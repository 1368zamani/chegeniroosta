<?php

namespace App\Services\Tariff;

use App\Models\Dehyari;
use App\Models\FeeType;
use App\Models\TariffItem;
use App\Models\TariffSet;
use App\Models\TariffTitle;
use App\Models\Village;
use Illuminate\Support\Facades\Log;

/**
 * پل بین ماژول قدیم (Fee*) و ماژول جدید (Tariff*).
 *
 * نقش: وقتی کاربر می‌خواهد عوارض پرداخت کند، اول سعی می‌کند نرخ را از
 * ماژول جدید (TariffSet منتشرشده سال جاری) بگیرد. اگر پیدا نشد، به
 * ماژول قدیم (FeeTariff) برمی‌گردد.
 *
 * نگاشت بین FeeType قدیم و TariffTitle جدید بر اساس فایل config/tariff_bridge.php
 */
class TariffBridgeService
{
    /**
     * تلاش برای یافتن نرخ جدید بر اساس دهیاری + نوع عوارض + سال.
     *
     * @return array{found: bool, item?: TariffItem, set?: TariffSet, title?: TariffTitle}
     */
    public function findRate(int $dehyariId, int $feeTypeId, ?int $year = null): array
    {
        $dehyari = Dehyari::find($dehyariId);
        if (!$dehyari || !$dehyari->village_id) {
            return ['found' => false];
        }

        $year = $year ?? (int) \Morilog\Jalali\Jalalian::now()->getYear();

        // مجموعه منتشرشده سال جاری
        $set = TariffSet::where('village_id', $dehyari->village_id)
            ->where('jalali_year', $year)
            ->where('status', 'published')
            ->first();

        if (!$set) {
            // اگر سال جاری منتشر نشده، آخرین منتشرشده را بگیر
            $set = TariffSet::where('village_id', $dehyari->village_id)
                ->where('status', 'published')
                ->orderByDesc('jalali_year')
                ->first();
        }

        if (!$set) {
            return ['found' => false];
        }

        // نگاشت FeeType → TariffTitle
        $titleCode = $this->mapFeeTypeToTitleCode($feeTypeId);
        if (!$titleCode) {
            return ['found' => false];
        }

        $title = TariffTitle::where('code', $titleCode)->first();
        if (!$title) {
            return ['found' => false];
        }

        $item = TariffItem::where('tariff_set_id', $set->id)
            ->where('tariff_title_id', $title->id)
            ->where('is_active', true)
            ->first();

        if (!$item) {
            return ['found' => false];
        }

        return [
            'found' => true,
            'item'  => $item,
            'set'   => $set,
            'title' => $title,
        ];
    }

    /**
     * آیا نرخ جدید برای این دهیاری + نوع عوارض موجود است؟
     */
    public function hasNewRate(int $dehyariId, int $feeTypeId): bool
    {
        return $this->findRate($dehyariId, $feeTypeId)['found'];
    }

    /**
     * نگاشت FeeType.id → TariffTitle.code بر اساس slug یا نام.
     */
    private function mapFeeTypeToTitleCode(int $feeTypeId): ?string
    {
        $feeType = FeeType::find($feeTypeId);
        if (!$feeType) return null;

        $mappings = config('tariff_bridge.fee_type_to_title', []);

        // اول با slug
        if ($feeType->slug && isset($mappings['slugs'][$feeType->slug])) {
            return $mappings['slugs'][$feeType->slug];
        }

        // با نام فارسی
        if ($feeType->name && isset($mappings['names'][$feeType->name])) {
            return $mappings['names'][$feeType->name];
        }

        return null;
    }
}

<?php

namespace App\Observers;

use App\Models\Dehyari;
use App\Models\FeeTariff;
use App\Models\PaymentAccount;
use App\Models\Village;

/**
 * هر وقت روستای جدید ساخته شد:
 *  1) دهیاری خودکار می‌سازد
 *  2) تعرفه‌های سال جاری را از یک دهیاری مرجع کپی می‌کند
 *  3) حساب بانکی پیش‌فرض غیرفعال می‌سازد
 */
class VillageObserver
{
    public function created(Village $village): void
    {
        $dehyari = Dehyari::firstOrCreate(
            ['village_id' => $village->id],
            ['name' => 'دهیاری ' . $village->name, 'is_active' => true]
        );

        $reference = Dehyari::where('id', '!=', $dehyari->id)
            ->whereHas('village', fn($q) => $q->where('section_id', $village->section_id))
            ->first()
            ?? Dehyari::where('id', '!=', $dehyari->id)->first();

        if ($reference) {
            $year = (int) \Morilog\Jalali\Jalalian::now()->getYear();
            foreach (FeeTariff::where('dehyari_id', $reference->id)->where('year', $year)->get() as $t) {
                FeeTariff::create([
                    'dehyari_id'  => $dehyari->id,
                    'fee_type_id' => $t->fee_type_id,
                    'year'        => $year,
                    'base_amount' => $t->base_amount,
                    'unit_rate'   => $t->unit_rate,
                    'coefficient' => $t->coefficient,
                    'formula_type'=> $t->formula_type,
                    'unit'        => $t->unit,
                    'is_active'   => true,
                    'description' => 'کپی خودکار از ' . ($reference->name ?? '-'),
                ]);
            }
        }

        PaymentAccount::firstOrCreate(
            ['dehyari_id' => $dehyari->id, 'is_default' => true],
            [
                'bank_name'      => null,
                'account_holder' => $dehyari->name,
                'is_active'      => false,
            ]
        );
    }
}

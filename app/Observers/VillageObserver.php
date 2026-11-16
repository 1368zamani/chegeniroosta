<?php

namespace App\Observers;

use App\Models\BankAccount;
use App\Models\Dehyari;
use App\Models\Village;

/**
 * هر وقت روستای جدید ساخته شد:
 *  1) دهیاری خودکار می‌سازد
 *  2) یک حساب بانکی پیش‌فرض غیرفعال برای روستا می‌سازد
 *
 * ⚠️ کپی تعرفهٔ سال جاری از دهیاری مرجع حذف شد: مدل‌های قدیمی
 * (FeeTariff و PaymentAccount) دیگر وجود ندارند و این کد در عمل
 * با خطای «Class not found» ساخت روستا را متوقف می‌کرد. سیستم
 * تعرفه اکنون با TariffSet/TariffItem و TariffSetService کار می‌کند.
 */
class VillageObserver
{
    public function created(Village $village): void
    {
        Dehyari::firstOrCreate(
            ['village_id' => $village->id],
            ['name' => 'دهیاری '.$village->name, 'is_active' => true]
        );

        BankAccount::firstOrCreate(
            ['village_id' => $village->id, 'is_default' => true],
            [
                'bank_name' => 'melli',
                'account_holder' => $village->name,
                'is_active' => false,
            ]
        );
    }
}

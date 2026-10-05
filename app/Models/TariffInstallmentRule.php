<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TariffInstallmentRule extends Model
{
    protected $table = 'tariff_installment_rules';
    protected $fillable = [
        'tariff_set_id', 'tariff_title_id', 'kind',
        'down_payment_percent', 'max_installments',
        'monthly_interest_percent', 'max_total_interest_percent',
        'is_penalty_eligible', 'is_active',
    ];
    protected $casts = [
        'is_penalty_eligible' => 'boolean',
        'is_active'           => 'boolean',
    ];

    public function kindLabel(): string
    {
        return match ($this->kind) {
            'one_third_cash'       => '۱/۳ نقد + ۶ قسط بدون کارمزد',
            'quarter_down_payment' => '۲۵٪ پیش‌پرداخت + تا ۳۶ قسط با کارمزد',
            default                => $this->kind,
        };
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TariffInvoiceLine extends Model
{
    protected $fillable = [
        'invoice_id',
        'tariff_item_id',
        'title_name',
        'amount_before',
        'discount_percent',
        'discount_amount',
        'amount',
        'notes',
        'inputs',
        'sort_order',
    ];

    protected $casts = [
        'inputs'           => 'array',
        'amount_before'    => 'integer',
        'amount'           => 'integer',
        'discount_amount'  => 'integer',
        'discount_percent' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(TariffInvoice::class, 'invoice_id');
    }

    public function tariffItem(): BelongsTo
    {
        return $this->belongsTo(TariffItem::class, 'tariff_item_id');
    }

    /**
     * محاسبه مبلغ نهایی
     */
    public function calculateFinalAmount(): int
    {
        $discount = (int) round($this->amount_before * $this->discount_percent / 100);
        $this->discount_amount = $discount;
        $this->amount = $this->amount_before - $discount;
        return $this->amount;
    }

    /**
     * ساخت یک ردیف از روی آیتم تعرفه
     */
    public static function fromTariffItem(
        TariffItem $item,
        int $amount,
        float $discountPercent = 0,
        array $inputs = [],
        ?string $notes = null
    ): self {
        $line = new self();
        $line->tariff_item_id = $item->id;
        $line->title_name = $item->title?->title ?? 'نامشخص';
        $line->amount_before = $amount;
        $line->discount_percent = $discountPercent;
        $line->inputs = $inputs;
        $line->notes = $notes;
        $line->calculateFinalAmount();
        return $line;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TariffItem extends Model
{
    protected $table = 'tariff_items';

    protected $fillable = [
        'tariff_set_id', 'tariff_title_id', 'is_active',
        'previous_amount', 'increase_percent', 'params',
        'final_amount', 'unit', 'notes', 'formula_display',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'increase_percent' => 'decimal:2',
        'params'           => 'array',
        'previous_amount'  => 'integer',
        'final_amount'     => 'integer',
    ];

    public function set(): BelongsTo { return $this->belongsTo(TariffSet::class, 'tariff_set_id'); }
    public function title(): BelongsTo { return $this->belongsTo(TariffTitle::class, 'tariff_title_id'); }

    public function exceedsInflation(): bool
    {
        return (float) $this->increase_percent > (float) $this->set->inflation_rate;
    }

    public function scopeActive($q) { return $q->where('is_active', true); }

    public function param(string $key, $default = null)
    {
        $params = $this->params ?? [];
        return array_key_exists($key, $params) ? $params[$key] : $default;
    }
}

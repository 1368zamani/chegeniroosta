<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TariffTitle extends Model
{
    protected $table = 'tariff_titles';

    protected $fillable = [
        'code', 'type', 'name_fa', 'formula_key', 'statutory_note',
        'is_optional', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_optional' => 'boolean',
        'is_active'   => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(TariffItem::class, 'tariff_title_id');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeOfType($q, string $type)
    {
        return $q->where('type', $type);
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'levy'        => 'عوارض',
            'service_fee' => 'بهای خدمات',
            'penalty'     => 'جریمه',
            default       => $this->type,
        };
    }
}

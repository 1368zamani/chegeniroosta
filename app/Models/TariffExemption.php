<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Morilog\Jalali\Jalalian;

class TariffExemption extends Model
{
    protected $table = 'tariff_exemptions';

    protected $fillable = [
        'tariff_set_id', 'tariff_title_id', 'kind', 'discount_percent',
        'description', 'valid_from', 'valid_until', 'statutory_note',
        'created_by', 'is_active',
    ];

    protected $casts = [
        'discount_percent' => 'decimal:2',
        'is_active'        => 'boolean',
    ];

    public function set(): BelongsTo { return $this->belongsTo(TariffSet::class, 'tariff_set_id'); }
    public function title(): BelongsTo { return $this->belongsTo(TariffTitle::class, 'tariff_title_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function scopeActive($q) { return $q->where('is_active', true); }

    public function appliesNow(): bool
    {
        if (!$this->is_active) return false;
        $today = Jalalian::now()->format('Y-m-d');
        if ($this->valid_from && $today < $this->valid_from) return false;
        if ($this->valid_until && $today > $this->valid_until) return false;
        return true;
    }

    public function isFullExemption(): bool
    {
        return (float) $this->discount_percent >= 100;
    }

    public function kindLabel(): string
    {
        return match ($this->kind) {
            'disabled'       => 'معلولان',
            'elderly'        => 'سالمندان',
            'martyr_family'  => 'خانواده‌ی شهدا و ایثارگران',
            'clergy'         => 'روحانیون',
            'educational'    => 'مراکز آموزشی',
            'charity'        => 'خیریه‌ها',
            'custom'         => 'سایر (مصوب شورا)',
            default          => $this->kind,
        };
    }

    public static function kindOptions(): array
    {
        return [
            'disabled'      => 'معلولان',
            'elderly'       => 'سالمندان',
            'martyr_family' => 'خانواده‌ی شهدا و ایثارگران',
            'clergy'        => 'روحانیون',
            'educational'   => 'مراکز آموزشی',
            'charity'       => 'خیریه‌ها',
            'custom'        => 'سایر (مصوب شورا)',
        ];
    }

    /** اعمال تخفیف روی یک مبلغ پایه */
    public function applyDiscount(int $baseAmount): int
    {
        $pct = (float) $this->discount_percent;
        return (int) round($baseAmount * (100 - $pct) / 100);
    }
}

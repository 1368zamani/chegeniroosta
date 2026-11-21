<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LevyVillageParam extends Model
{
    protected $table = 'levy_village_params';

    protected $fillable = [
        'village_id', 'group', 'transaction_value',
        'construction_coefficient', 'density_coefficient',
        'density_area_source', 'source_note',
    ];

    protected $casts = [
        'transaction_value'        => 'integer',
        'construction_coefficient' => 'integer',
        'density_coefficient'      => 'integer',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'village_id');
    }

    /** ضریب نهایی (N) = ارزش معاملاتی × ۰.۱۴ */
    public function finalCoefficient(): int
    {
        return (int) round((float) $this->transaction_value * 0.14);
    }

    public function getFinalCoefficientAttribute(): int
    {
        return $this->finalCoefficient();
    }

    public function getAreaSourceLabelAttribute(): string
    {
        return $this->density_area_source === 'R' ? 'مساحت ملک (استثناء اکسل)' : 'مساحت ساخت';
    }
}

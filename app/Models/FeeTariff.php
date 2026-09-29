<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FeeTariff extends Model
{
    protected $table = 'fee_tariffs';

    protected $fillable = [
        'dehyari_id', 'fee_type_id', 'year', 'valid_from', 'valid_to',
        'description', 'base_amount', 'unit_rate', 'coefficient',
        'formula_type', 'formula_expression', 'unit', 'is_active',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'valid_from'   => 'date',
        'valid_to'     => 'date',
        'coefficient'  => 'decimal:4',
    ];

    public function dehyari(): BelongsTo { return $this->belongsTo(Dehyari::class); }
    public function feeType(): BelongsTo { return $this->belongsTo(FeeType::class); }
    public function approval(): HasOne { return $this->hasOne(FeeApproval::class); }

    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeForYear($q, $year) { return $q->where('year', $year); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankAccount extends Model
{
    protected $fillable = [
        'village_id',
        'bank_name',
        'account_holder',
        'iban',
        'card_number',
        'account_number',
        'is_default',
        'is_active',
        'note',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
    ];

    // ═══ روابط ═══
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    // ═══ Scope ها ═══
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // ═══ متدهای کمکی ═══
    public static function getDefaultForVillage(int $villageId): ?self
    {
        return static::where('village_id', $villageId)
            ->where('is_active', true)
            ->where('is_default', true)
            ->first()
            ?? static::where('village_id', $villageId)
                ->where('is_active', true)
                ->first();
    }

    public static function bankOptions(): array
    {
        return [
            'melli' => 'بانک ملی ایران',
            'post'  => 'پست بانک ایران',
        ];
    }

    public function bankLabel(): string
    {
        return static::bankOptions()[$this->bank_name] ?? $this->bank_name;
    }
}

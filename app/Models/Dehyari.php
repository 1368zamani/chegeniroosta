<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dehyari extends Model
{
    protected $table = 'dehyaris';

    protected $fillable = [
        'village_id', 'name', 'dehyari_code', 'dehyar_name',
        'phone', 'address', 'payment_note', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function village(): BelongsTo { return $this->belongsTo(Village::class); }
    public function tariffs(): HasMany { return $this->hasMany(FeeTariff::class); }
    public function accounts(): HasMany { return $this->hasMany(PaymentAccount::class); }
    public function bills(): HasMany { return $this->hasMany(Bill::class); }

    public function defaultAccount()
    {
        return $this->accounts()->where('is_active', true)->orderByDesc('is_default')->first();
    }
}

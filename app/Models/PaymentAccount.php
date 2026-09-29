<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAccount extends Model
{
    protected $table = 'payment_accounts';

    protected $fillable = [
        'dehyari_id', 'bank_name', 'account_number', 'card_number',
        'sheba', 'account_holder', 'is_default', 'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
    ];

    public function dehyari(): BelongsTo { return $this->belongsTo(Dehyari::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'bill_id', 'method', 'payer_name', 'payer_card', 'bank_reference', 'note',
        'gateway_name', 'transaction_id', 'gateway_payload',
        'amount_rial', 'paid_at', 'status', 'reviewed_by', 'reviewed_at', 'review_note',
    ];

    protected $casts = [
        'gateway_payload' => 'array',
        'paid_at'         => 'datetime',
        'reviewed_at'     => 'datetime',
    ];

    public function bill(): BelongsTo { return $this->belongsTo(Bill::class); }
    public function receipts(): HasMany { return $this->hasMany(PaymentReceipt::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
}

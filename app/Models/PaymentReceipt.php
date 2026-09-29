<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReceipt extends Model
{
    protected $table = 'payment_receipts';

    protected $fillable = ['payment_id', 'file_path', 'original_name', 'mime_type', 'size'];

    public function payment(): BelongsTo { return $this->belongsTo(Payment::class); }
}

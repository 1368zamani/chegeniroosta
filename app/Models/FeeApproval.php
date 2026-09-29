<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeApproval extends Model
{
    protected $table = 'fee_approvals';

    protected $fillable = [
        'fee_tariff_id', 'approval_number', 'approval_date',
        'authority', 'announcement_date', 'notes', 'document_path',
    ];

    protected $casts = [
        'approval_date'     => 'date',
        'announcement_date' => 'date',
    ];

    public function tariff(): BelongsTo { return $this->belongsTo(FeeTariff::class, 'fee_tariff_id'); }
}

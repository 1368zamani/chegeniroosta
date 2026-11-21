<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LevyPublicRequestItem extends Model
{
    protected $table = 'levy_public_request_items';

    protected $fillable = [
        'levy_public_request_id', 'tariff_title_id',
        'title_snapshot', 'unit', 'unit_price', 'quantity', 'amount', 'sort_order',
    ];

    protected $casts = [
        'unit_price' => 'integer',
        'quantity'   => 'integer',
        'amount'     => 'integer',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(LevyPublicRequest::class, 'levy_public_request_id');
    }

    public function title(): BelongsTo
    {
        return $this->belongsTo(TariffTitle::class, 'tariff_title_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeParameter extends Model
{
    protected $table = 'fee_parameters';

    protected $fillable = [
        'fee_type_id', 'key', 'label', 'input_type', 'options',
        'unit_label', 'default_value', 'is_required', 'is_multiplier', 'sort_order',
    ];

    protected $casts = [
        'options'       => 'array',
        'is_required'   => 'boolean',
        'is_multiplier' => 'boolean',
    ];

    public function feeType(): BelongsTo { return $this->belongsTo(FeeType::class); }
}

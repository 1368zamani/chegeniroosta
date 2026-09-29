<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillItem extends Model
{
    protected $table = 'bill_items';

    protected $fillable = ['bill_id', 'title', 'description', 'amount_rial', 'sort_order'];

    public function bill(): BelongsTo { return $this->belongsTo(Bill::class); }
}

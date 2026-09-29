<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeCategory extends Model
{
    protected $table = 'fee_categories';

    protected $fillable = ['slug', 'name', 'icon', 'description', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function feeTypes(): HasMany { return $this->hasMany(FeeType::class, 'category_id'); }

    public function scopeActive($q) { return $q->where('is_active', true); }
}

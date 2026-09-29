<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeType extends Model
{
    protected $table = 'fee_types';

    protected $fillable = ['category_id', 'slug', 'name', 'description', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function category(): BelongsTo { return $this->belongsTo(FeeCategory::class, 'category_id'); }
    public function tariffs(): HasMany { return $this->hasMany(FeeTariff::class); }
    public function parameters(): HasMany { return $this->hasMany(FeeParameter::class)->orderBy('sort_order'); }

    public function scopeActive($q) { return $q->where('is_active', true); }
}

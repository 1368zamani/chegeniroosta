<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillageFacility extends Model
{
    protected $fillable = ['village_id', 'name', 'icon', 'category', 'is_available', 'description', 'sort_order', 'is_active'];

    protected $casts = [
        'is_available' => 'boolean',
        'is_active'    => 'boolean',
    ];

    public function village() { return $this->belongsTo(Village::class); }

    /** فقط موارد فعال — برای نمایش در سایت */
    public function scopeActive($q) { return $q->where('is_active', true); }
}

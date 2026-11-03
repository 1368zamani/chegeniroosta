<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillageAttraction extends Model
{
    protected $fillable = ['village_id', 'name', 'category', 'description', 'image', 'address', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function village() { return $this->belongsTo(Village::class); }

    /** فقط موارد فعال — برای نمایش در سایت */
    public function scopeActive($q) { return $q->where('is_active', true); }
}

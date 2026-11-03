<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillageEvent extends Model
{
    protected $fillable = [
        'village_id', 'title', 'description', 'location',
        'event_date', 'event_time', 'event_type', 'is_important', 'is_active'
    ];

    protected $casts = [
        'event_date'   => 'date',
        'is_important' => 'boolean',
        'is_active'    => 'boolean',
    ];

    public function village() { return $this->belongsTo(Village::class); }

    /** فقط موارد فعال — برای نمایش در سایت */
    public function scopeActive($q) { return $q->where('is_active', true); }
}

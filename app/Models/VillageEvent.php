<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillageEvent extends Model
{
    protected $fillable = [
        'village_id', 'title', 'description', 'location',
        'event_date', 'event_time', 'event_type', 'is_important'
    ];

    protected $casts = [
        'event_date'   => 'date',
        'is_important' => 'boolean',
    ];

    public function village() { return $this->belongsTo(Village::class); }
}

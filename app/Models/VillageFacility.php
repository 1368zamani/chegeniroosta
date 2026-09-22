<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillageFacility extends Model
{
    protected $fillable = ['village_id', 'name', 'icon', 'category', 'is_available', 'description', 'sort_order'];

    protected $casts = ['is_available' => 'boolean'];

    public function village() { return $this->belongsTo(Village::class); }
}

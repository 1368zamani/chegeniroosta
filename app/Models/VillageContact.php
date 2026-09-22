<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillageContact extends Model
{
    protected $fillable = [
        'village_id',
        'address',
        'phone',
        'email',
        'working_hours',
        'map_embed_url',
    ];

    public function village()
    {
        return $this->belongsTo(Village::class);
    }
}

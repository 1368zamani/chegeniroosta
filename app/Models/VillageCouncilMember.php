<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillageCouncilMember extends Model
{
    protected $fillable = ['village_id', 'name', 'position', 'phone', 'photo', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function village() { return $this->belongsTo(Village::class); }
}

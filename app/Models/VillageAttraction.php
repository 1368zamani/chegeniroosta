<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillageAttraction extends Model
{
    protected $fillable = ['village_id', 'name', 'category', 'description', 'image', 'address', 'sort_order'];

    public function village() { return $this->belongsTo(Village::class); }
}

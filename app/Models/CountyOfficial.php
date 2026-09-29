<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountyOfficial extends Model
{
    protected $table = 'county_officials';

    protected $fillable = [
        'type', 'name', 'position', 'photo', 'bio', 'education',
        'experience', 'phone', 'email', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Scopes (بدون متد static هم‌نام)
    public function scopeGovernor($q) { return $q->where('type', 'governor'); }
    public function scopeDeputies($q) { return $q->where('type', 'deputy'); }
    public function scopeActive($q)   { return $q->where('is_active', true); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillageImage extends Model
{
    protected $table = 'village_images';

    protected $fillable = [
        'village_id',
        'title',
        'image',
        'description',
        'is_cover',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    /**
     * Accessor برای سازگاری با view هایی که از image_path استفاده می‌کنند
     */
    public function getImagePathAttribute()
    {
        return $this->attributes['image'] ?? null;
    }

    public function getCaptionAttribute()
    {
        return $this->attributes['title'] ?? null;
    }
}

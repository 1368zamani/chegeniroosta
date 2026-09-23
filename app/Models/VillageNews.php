<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillageNews extends Model
{
    protected $table = 'village_news';

    protected $fillable = [
        'village_id',
        'user_id',
        'title',
        'slug',
        'body',
        'excerpt',
        'image',
        'is_active',
        'is_pinned',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_pinned' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function images(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(VillageNewsImage::class, 'news_id')->orderBy('sort_order');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * همون content که قبلاً استفاده می‌شد
     */
    public function getContentAttribute()
    {
        return $this->body;
    }

    public function setContentAttribute($value)
    {
        $this->attributes['body'] = $value;
    }
}

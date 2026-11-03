<?php

namespace App\Models;

use App\Models\Concerns\HasStats;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SectionNews extends Model
{
    use HasStats;

    protected $table = 'section_news';

    protected $fillable = [
        'section_id',
        'user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'is_pinned',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function images(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SectionNewsImage::class, 'news_id')->orderBy('sort_order');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

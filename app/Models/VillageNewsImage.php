<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillageNewsImage extends Model
{
    protected $table = 'village_news_images';

    protected $fillable = ['news_id', 'image', 'caption', 'sort_order'];

    public function news(): BelongsTo
    {
        return $this->belongsTo(VillageNews::class, 'news_id');
    }
}

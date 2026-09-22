<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SectionGallery extends Model
{
    protected $table = 'section_gallery';

    protected $fillable = ['section_id', 'image_path', 'caption', 'sort_order'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }
}

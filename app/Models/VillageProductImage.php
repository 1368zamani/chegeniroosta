<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** تصویر یک محصول روستا */
class VillageProductImage extends Model
{
    protected $table = 'village_product_images';

    protected $fillable = ['product_id', 'image', 'caption', 'sort_order'];

    protected $casts = ['sort_order' => 'integer'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(VillageProduct::class, 'product_id');
    }
}

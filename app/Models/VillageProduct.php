<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * محصول روستا — برای فروش محصولات محلی.
 *
 * ادمین روستا آن را از پنل مدیریت می‌کند و در سایت روستا نمایش داده می‌شود.
 */
class VillageProduct extends Model
{
    protected $table = 'village_products';

    /** دسته‌بندی‌های آماده */
    public const CATEGORIES = [
        'عسل و فرآورده‌های زنبور',
        'لبنیات و دام',
        'محصولات کشاورزی',
        'خشکبار و میوه',
        'صنایع دستی',
        'گیاهان دارویی',
        'پوشاک و بافت',
        'سایر',
    ];

    protected $fillable = [
        'village_id',
        'user_id',
        'title',
        'slug',
        'category',
        'description',
        'short_description',
        'price',
        'unit',
        'quantity',
        'seller_name',
        'seller_phone',
        'delivery_info',
        'is_active',
        'is_featured',
        'is_available',
        'sort_order',
        'views',
    ];

    protected $casts = [
        'price'        => 'integer',
        'is_active'    => 'boolean',
        'is_featured'  => 'boolean',
        'is_available' => 'boolean',
        'sort_order'   => 'integer',
        'views'        => 'integer',
    ];

    // ══════════════════════════════════════════════════════
    // روابط
    // ══════════════════════════════════════════════════════

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(VillageProductImage::class, 'product_id')->orderBy('sort_order');
    }

    public function files(): HasMany
    {
        return $this->hasMany(VillageProductFile::class, 'product_id')->orderBy('sort_order');
    }

    // ══════════════════════════════════════════════════════
    // اسکوپ‌ها
    // ══════════════════════════════════════════════════════

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeForVillage($q, int $villageId)
    {
        return $q->where('village_id', $villageId);
    }

    public function scopeOrdered($q)
    {
        return $q->orderByDesc('is_featured')->orderBy('sort_order')->orderByDesc('id');
    }

    // ══════════════════════════════════════════════════════
    // کمکی‌ها
    // ══════════════════════════════════════════════════════

    /** «۲۵۰٬۰۰۰ تومان» یا «تماس بگیرید» */
    public function priceLabel(): string
    {
        if ($this->price === null || $this->price <= 0) {
            return 'تماس بگیرید';
        }

        return \App\Support\Number::fa(number_format((int) $this->price)) . ' تومان';
    }

    /** تصویر اصلی */
    public function mainImage(): ?string
    {
        $first = $this->images->first();

        return $first ? $first->image : null;
    }

    /** آیکون دسته‌بندی */
    public function categoryIcon(): string
    {
        $cat = (string) $this->category;

        if (str_contains($cat, 'عسل'))        return '🍯';
        if (str_contains($cat, 'لبنیات'))     return '🥛';
        if (str_contains($cat, 'دام'))        return '🐄';
        if (str_contains($cat, 'کشاورزی'))    return '🌾';
        if (str_contains($cat, 'خشکبار'))     return '🥜';
        if (str_contains($cat, 'میوه'))       return '🍎';
        if (str_contains($cat, 'صنایع'))      return '🧶';
        if (str_contains($cat, 'دارویی'))     return '🌿';
        if (str_contains($cat, 'پوشاک'))      return '🧵';

        return '📦';
    }

    /** ساخت اسلاگ یکتا در محدوده‌ی یک روستا */
    public static function uniqueSlug(int $villageId, string $title, ?int $ignoreId = null): string
    {
        $base = \Illuminate\Support\Str::slug($title);

        // Str::slug روی فارسی کار نمی‌کند → از خود متن استفاده می‌کنیم
        if ($base === '' || $base === null) {
            $base = preg_replace('/[^\p{L}\p{N}]+/u', '-', $title) ?: 'product';
            $base = trim($base, '-');
        }

        $slug = $base;
        $i = 1;

        while (true) {
            $q = static::where('village_id', $villageId)->where('slug', $slug);
            if ($ignoreId) {
                $q->where('id', '!=', $ignoreId);
            }
            if (! $q->exists()) {
                return $slug;
            }
            $slug = $base . '-' . (++$i);
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * کاتالوگ عناوین عوارض.
 *
 * در بازطراحی «عوارض»، فقط عناوینی که `category` دارند در صفحه‌ی
 * عمومی نمایش داده می‌شوند:
 *   waste        → عوارض پسماند
 *   construction → عوارض ساخت‌وساز
 *        subcategory: new_build (جدید) | renovation (تعمیری)
 *
 * ۲۸ عنوان قدیمی دیگر (مشاغل، آرامستان، پارکینگ و…) در دیتابیس
 * باقی می‌مانند و فاکتورهای قبلی را پشتیبانی می‌کنند، ولی از
 * صفحه‌ی عمومی کنار گذاشته شده‌اند.
 */
class TariffTitle extends Model
{
    protected $table = 'tariff_titles';

    /** دسته‌های اصلی */
    public const CATEGORY_WASTE        = 'waste';
    public const CATEGORY_CONSTRUCTION = 'construction';

    /** زیرگروه‌های ساخت‌وساز */
    public const SUB_NEW_BUILD  = 'new_build';
    public const SUB_RENOVATION = 'renovation';

    /** فهرست واحدهای مجاز محاسبه */
    public const UNITS = [
        'متر مربع',
        'متر طول',
        'متر مکعب',
        'واحد مسکونی / سال',
        'واحد تجاری / سال',
        'واحد صنعتی / سال',
        'هکتار / سال',
        'مورد',
        'نفر',
        'ساعت',
    ];

    protected $fillable = [
        'code', 'type', 'category', 'subcategory', 'name_fa', 'unit',
        'formula_key', 'statutory_note', 'description', 'base_amount',
        'effective_from', 'updated_by', 'is_optional', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_optional'    => 'boolean',
        'is_active'      => 'boolean',
        'base_amount'    => 'integer',
        'effective_from' => 'date',
        'sort_order'     => 'integer',
    ];

    // ══════════════════════════════════════════════════════
    // روابط
    // ══════════════════════════════════════════════════════

    public function items(): HasMany
    {
        return $this->hasMany(TariffItem::class, 'tariff_title_id');
    }

    public function changeLogs(): HasMany
    {
        return $this->hasMany(TariffChangeLog::class, 'tariff_title_id')->latest('id');
    }

    public function lastEditor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ══════════════════════════════════════════════════════
    // اسکوپ‌ها
    // ══════════════════════════════════════════════════════

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeOfType($q, string $t)
    {
        return $q->where('type', $t);
    }

    /** فقط عناوینی که در بازطراحی جدید دسته‌بندی شده‌اند */
    public function scopeCategorized($q)
    {
        return $q->whereNotNull('category');
    }

    public function scopeWaste($q)
    {
        return $q->where('category', self::CATEGORY_WASTE);
    }

    public function scopeConstruction($q)
    {
        return $q->where('category', self::CATEGORY_CONSTRUCTION);
    }

    public function scopeNewBuild($q)
    {
        return $q->where('subcategory', self::SUB_NEW_BUILD);
    }

    public function scopeRenovation($q)
    {
        return $q->where('subcategory', self::SUB_RENOVATION);
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }

    // ══════════════════════════════════════════════════════
    // برچسب‌ها
    // ══════════════════════════════════════════════════════

    public function typeLabel(): string
    {
        return match ($this->type) {
            'levy'        => 'عوارض محلی',
            'service_fee' => 'بهای خدمات',
            'penalty'     => 'جریمه',
            default       => (string) $this->type,
        };
    }

    public function categoryLabel(): string
    {
        return match ($this->category) {
            self::CATEGORY_WASTE        => 'عوارض پسماند',
            self::CATEGORY_CONSTRUCTION => 'عوارض ساخت‌وساز',
            default                     => 'بدون دسته',
        };
    }

    public function subcategoryLabel(): string
    {
        return match ($this->subcategory) {
            self::SUB_NEW_BUILD  => 'ساخت‌وساز جدید',
            self::SUB_RENOVATION => 'ساخت‌وساز تعمیری',
            default              => '',
        };
    }

    /** برچسب کامل: «پسماند» یا «ساخت‌وساز — جدید» */
    public function fullLabel(): string
    {
        if ($this->category === self::CATEGORY_WASTE) {
            return 'عوارض پسماند';
        }

        $sub = $this->subcategoryLabel();

        return $sub !== '' ? 'عوارض ساخت‌وساز — ' . $sub : 'عوارض ساخت‌وساز';
    }

    /** واحد محاسبه — با مقدار پیش‌فرض */
    public function unitLabel(): string
    {
        return $this->unit ?: 'مورد';
    }
}

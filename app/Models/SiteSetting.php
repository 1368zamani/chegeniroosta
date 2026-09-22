<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $table = 'site_settings';

    protected $fillable = ['key', 'value', 'group', 'type', 'label', 'description'];

    /**
     * دریافت مقدار یک تنظیم
     */
    public static function get(string $key, $default = null)
    {
        $settings = self::allCached();
        $value = $settings[$key] ?? null;

        if ($value === null) {
            return $default;
        }

        // دیکد JSON اگه لازم بود
        if (is_string($value) && self::isJson($value)) {
            return json_decode($value, true) ?? $default;
        }

        return $value;
    }

    /**
     * ذخیره مقدار
     */
    public static function set(string $key, $value, string $group = 'general', string $type = 'text'): void
    {
        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'type' => $type]
        );

        Cache::forget('site_settings_all');
    }

    /**
     * گرفتن همه تنظیمات از کش
     */
    public static function allCached(): array
    {
        return Cache::remember('site_settings_all', 3600, function () {
            return self::pluck('value', 'key')->toArray();
        });
    }

    /**
     * پاک کردن کش تنظیمات
     */
    public static function clearCache(): void
    {
        Cache::forget('site_settings_all');
    }


    /**
     * URL آرم - چک می‌کند در public یا storage
     */
    public static function emblemUrl($sectionLogo = null): string
    {
        // آرم اختصاصی بخش
        if ($sectionLogo) {
            if (file_exists(public_path($sectionLogo))) {
                return asset($sectionLogo);
            }
            if (file_exists(storage_path('app/public/' . $sectionLogo))) {
                return asset('storage/' . $sectionLogo);
            }
        }

        // آرم از تنظیمات
        $emblem = self::get('header_emblem');

        if ($emblem) {
            if (file_exists(storage_path('app/public/' . $emblem))) {
                return asset('storage/' . $emblem);
            }
            if (file_exists(public_path($emblem))) {
                return asset($emblem);
            }
        }

        // پیش‌فرض
        if (file_exists(public_path('images/gov/emblem.png'))) {
            return asset('images/gov/emblem.png');
        }

        return '';
    }

    private static function isJson(string $s): bool
    {
        $s = trim($s);
        return (str_starts_with($s, '{') && str_ends_with($s, '}'))
            || (str_starts_with($s, '[') && str_ends_with($s, ']'));
    }
}

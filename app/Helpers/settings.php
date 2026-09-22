<?php

use App\Models\SiteSetting;

if (!function_exists('setting')) {
    /**
     * گرفتن مقدار یک تنظیم سایت
     */
    function setting(string $key, $default = null)
    {
        try {
            return SiteSetting::get($key, $default);
        } catch (\Exception $e) {
            return $default;
        }
    }
}

if (!function_exists('setting_set')) {
    /**
     * ذخیره یک تنظیم
     */
    function setting_set(string $key, $value, string $group = 'general', string $type = 'text'): void
    {
        SiteSetting::set($key, $value, $group, $type);
    }
}



if (!function_exists('village_show')) {
    /**
     * چک می‌کند آیا یه بخش روستا باید نمایش داده شود
     */
    function village_show($village, string $section): bool
    {
        if (!$village) return true;
        
        $field = 'show_' . $section;
        
        if (!isset($village->$field)) return true;
        
        return (bool) $village->$field;
    }
}

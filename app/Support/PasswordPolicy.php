<?php

namespace App\Support;

/**
 * سیاست رمز عبور مشترک سامانه.
 *
 * قاعده: رمز باید هم حرف و هم عدد داشته باشد تا در برابر حدس‌زدن
 * و حمله‌ی فرهنگ‌نامه‌ای مقاوم باشد. حداقل طول ۸ کاراکتر.
 *
 * توجه: رمزهای اولیه‌ای که سوپرادمین برای تحویل به ادمین‌ها می‌سازد
 * (مثل 888631) از طریق فرم رد نمی‌شوند؛ این سیاست فقط روی رمزهایی
 * اعمال می‌شود که کاربر خودش انتخاب می‌کند.
 */
class PasswordPolicy
{
    /** حداقل طول رمز */
    public const MIN_LENGTH = 8;

    /** حداکثر طول (محدودیت bcrypt) */
    public const MAX_LENGTH = 72;

    /**
     * قواعد اعتبارسنجی برای فیلد رمز.
     *
     * @param  string  $field  نام فیلد (مثلاً password یا new_password)
     */
    public static function rules(string $field = 'password'): array
    {
        return [
            $field => [
                'required',
                'string',
                'min:' . self::MIN_LENGTH,
                'max:' . self::MAX_LENGTH,
                // حداقل یک حرف (لاتین یا فارسی)
                'regex:/\p{L}/u',
                // حداقل یک رقم (لاتین یا فارسی)
                'regex:/[0-9\x{06F0}-\x{06F9}]/u',
            ],
        ];
    }

    /** پیام‌های خطای فارسی */
    public static function messages(string $field = 'password'): array
    {
        return [
            "{$field}.required" => 'رمز عبور را وارد کنید.',
            "{$field}.string"   => 'رمز عبور معتبر نیست.',
            "{$field}.min"      => 'رمز عبور باید حداقل ' . self::MIN_LENGTH . ' کاراکتر باشد.',
            "{$field}.max"      => 'رمز عبور حداکثر ' . self::MAX_LENGTH . ' کاراکتر است.',
            "{$field}.regex"    => 'رمز عبور باید هم حرف و هم عدد داشته باشد.',
        ];
    }

    /**
     * بررسی ساده (برای استفاده خارج از Validator).
     */
    public static function passes(string $password): bool
    {
        $len = mb_strlen($password);

        if ($len < self::MIN_LENGTH || $len > self::MAX_LENGTH) {
            return false;
        }

        $hasLetter = preg_match('/\p{L}/u', $password) === 1;
        $hasDigit  = preg_match('/[0-9\x{06F0}-\x{06F9}]/u', $password) === 1;

        return $hasLetter && $hasDigit;
    }

    /** متن راهنما برای نمایش در فرم‌ها */
    public static function hint(): string
    {
        return 'حداقل ' . self::MIN_LENGTH . ' کاراکتر — باید ترکیبی از حرف و عدد باشد.';
    }
}

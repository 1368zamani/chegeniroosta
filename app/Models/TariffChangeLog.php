<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * تاریخچه‌ی تغییرات عوارض.
 *
 * چرا جدول جدا؟ اگر لاگ را داخل خودِ `tariff_titles` بگذاریم، با هر ویرایش
 * تاریخچه پاک می‌شود. اینجا تاریخچه‌ی کامل می‌ماند و می‌شود پرسید:
 * «مهر ۱۴۰۵ این مبلغ چقدر بوده و چه کسی عوضش کرده؟»
 */
class TariffChangeLog extends Model
{
    protected $table = 'tariff_change_logs';

    /** این جدول فقط created_at دارد */
    public $timestamps = false;

    public const ACTION_CREATE        = 'create';
    public const ACTION_UPDATE_AMOUNT = 'update_amount';
    public const ACTION_UPDATE_INFO   = 'update_info';
    public const ACTION_TOGGLE_ACTIVE = 'toggle_active';
    public const ACTION_DELETE        = 'delete';
    public const ACTION_RESTORE       = 'restore';

    protected $fillable = [
        'tariff_title_id', 'user_id', 'user_name', 'action',
        'old_amount', 'new_amount', 'old_values', 'new_values',
        'reason', 'ip_address', 'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'old_amount' => 'integer',
        'new_amount' => 'integer',
        'created_at' => 'datetime',
    ];

    public function title(): BelongsTo
    {
        return $this->belongsTo(TariffTitle::class, 'tariff_title_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ══════════════════════════════════════════════════════
    // ثبت لاگ — یک نقطه‌ی واحد برای همه‌ی تغییرات
    // ══════════════════════════════════════════════════════

    /**
     * @param array{
     *   old_amount?: int|null, new_amount?: int|null,
     *   old_values?: array|null, new_values?: array|null,
     *   reason?: string|null
     * } $extra
     */
    public static function record(
        TariffTitle $title,
        string $action,
        array $extra = []
    ): self {
        $user = auth()->user();

        return self::create([
            'tariff_title_id' => $title->id,
            'user_id'         => $user?->id,
            'user_name'       => $user?->name,
            'action'          => $action,
            'old_amount'      => $extra['old_amount'] ?? null,
            'new_amount'      => $extra['new_amount'] ?? null,
            'old_values'      => $extra['old_values'] ?? null,
            'new_values'      => $extra['new_values'] ?? null,
            'reason'          => $extra['reason'] ?? null,
            'ip_address'      => request()->ip(),
            'created_at'      => now(),
        ]);
    }

    // ══════════════════════════════════════════════════════
    // برچسب‌ها
    // ══════════════════════════════════════════════════════

    public function actionLabel(): string
    {
        return match ($this->action) {
            self::ACTION_CREATE        => 'ایجاد',
            self::ACTION_UPDATE_AMOUNT => 'تغییر مبلغ',
            self::ACTION_UPDATE_INFO   => 'ویرایش اطلاعات',
            self::ACTION_TOGGLE_ACTIVE => 'تغییر وضعیت',
            self::ACTION_DELETE        => 'غیرفعال‌سازی',
            self::ACTION_RESTORE       => 'فعال‌سازی مجدد',
            default                    => (string) $this->action,
        };
    }

    public function actionColor(): string
    {
        return match ($this->action) {
            self::ACTION_CREATE, self::ACTION_RESTORE => '#2e7d32',
            self::ACTION_UPDATE_AMOUNT                => '#e65100',
            self::ACTION_UPDATE_INFO                  => '#1565c0',
            self::ACTION_DELETE                       => '#c62828',
            default                                   => '#546e7a',
        };
    }

    /** توضیح خوانا از تغییر مبلغ: «۹۰۰٬۰۰۰ → ۹۵۰٬۰۰۰ (۵.۵٪+)» */
    public function amountChangeLabel(): ?string
    {
        if ($this->old_amount === null && $this->new_amount === null) {
            return null;
        }

        $fmt = fn (?int $v) => $v === null
            ? '—'
            : \App\Support\Number::fa(number_format($v));

        $out = $fmt($this->old_amount) . ' → ' . $fmt($this->new_amount);

        if ($this->old_amount && $this->new_amount && $this->old_amount !== $this->new_amount) {
            $pct  = (($this->new_amount - $this->old_amount) / $this->old_amount) * 100;
            $sign = $pct >= 0 ? '+' : '';
            // Number::fa فقط یک آرگومان می‌گیرد
            $out .= '  (' . $sign . \App\Support\Number::fa(number_format(abs($pct), 1)) . '٪)';
        }

        return $out;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TariffInvoice extends Model
{
    protected $fillable = [
        'tracking_code',
        'public_token',
        'village_id',
        'tariff_set_id',
        'payer_name',
        'payer_national_id',
        'payer_mobile',
        'subject',
        'total',
        'discount_total',
        'installment_plan',
        'is_installment',
        'issued_at',
        'due_date',
        'status',
        'review_note',
        'paid_at',
        'paid_amount',
        'village_request_id',
    ];

    protected $casts = [
        'installment_plan' => 'array',
        'is_installment'   => 'boolean',
        'issued_at'        => 'datetime',
        'due_date'         => 'date',
        'paid_at'          => 'datetime',
        'total'            => 'integer',
        'discount_total'   => 'integer',
        'paid_amount'      => 'integer',
    ];

    // ═══════════ روابط ═══════════
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function set(): BelongsTo
    {
        return $this->belongsTo(TariffSet::class, 'tariff_set_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(TariffInvoiceLine::class, 'invoice_id')->orderBy('sort_order');
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(TariffReceipt::class, 'invoice_id')->orderByDesc('created_at');
    }

    // ═══════════ Scope ها ═══════════
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending_review', 'receipt_uploaded']);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // ═══════════ متدهای کمکی ═══════════

    /**
     * ساخت کد پیگیری یکتا
     */
    public static function generateTrackingCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (static::where('tracking_code', $code)->exists());

        return $code;
    }

    /**
     * ساخت توکن عمومی
     */
    public static function generatePublicToken(): string
    {
        return Str::random(48);
    }

    /**
     * برچسب فارسی وضعیت
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending_review'   => 'در انتظار بررسی',
            'issued'           => 'صادر شده',
            'receipt_uploaded' => 'رسید بارگذاری شده',
            'paid'             => 'پرداخت‌شده',
            'cancelled'        => 'ابطال‌شده',
            default            => $this->status,
        };
    }

    /**
     * رنگ برچسب وضعیت
     */
    public function statusColor(): string
    {
        return match ($this->status) {
            'pending_review'   => 'bg-amber-100 text-amber-900',
            'issued'           => 'bg-blue-100 text-blue-800',
            'receipt_uploaded' => 'bg-purple-100 text-purple-800',
            'paid'             => 'bg-emerald-100 text-emerald-800',
            'cancelled'        => 'bg-gray-200 text-gray-700',
            default            => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * آیا مهلت پرداخت تمام شده؟
     */
    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && !in_array($this->status, ['paid', 'cancelled']);
    }

    /**
     * آیا اجازه آپلود رسید دارد؟
     */
    public function canUploadReceipt(): bool
    {
        return in_array($this->status, ['issued', 'pending_review', 'receipt_uploaded']);
    }

    /**
     * محاسبه مجدد جمع کل از روی ردیف‌ها
     */
    public function recalculateTotal(): self
    {
        $this->total = $this->lines()->sum('amount');
        $this->discount_total = $this->lines()->sum('discount_amount');
        $this->save();
        return $this;
    }

    /**
     * معادل تومان
     */
    public function tomanAmount(): int
    {
        return (int) ($this->total / 10);
    }

    // ═══════════ Route binding ═══════════
    public function getRouteKeyName(): string
    {
        return 'id';
    }
}

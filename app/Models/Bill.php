<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bill extends Model
{
    protected $table = 'bills';

    protected $fillable = [
        'dehyari_id', 'fee_tariff_id', 'bill_number', 'payment_id',
        'taxpayer_name', 'national_code', 'mobile', 'address',
        'fee_category_name', 'fee_type_name', 'year', 'unit',
        'formula_type', 'formula_display', 'parameters_snapshot', 'calculation_steps',
        'amount_rial', 'amount_paid', 'issue_date', 'due_date',
        'status', 'payment_bank', 'payment_account', 'payment_card', 'payment_sheba',
        'created_by',
    ];

    protected $casts = [
        'parameters_snapshot' => 'array',
        'calculation_steps'   => 'array',
        'issue_date'          => 'date',
        'due_date'            => 'date',
    ];

    public function dehyari(): BelongsTo { return $this->belongsTo(Dehyari::class); }
    public function tariff(): BelongsTo { return $this->belongsTo(FeeTariff::class, 'fee_tariff_id'); }
    public function items(): HasMany { return $this->hasMany(BillItem::class)->orderBy('sort_order'); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public static function generateBillNumber(): string
    {
        do {
            $n = 'BILL-' . date('ymd') . '-' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('bill_number', $n)->exists());
        return $n;
    }

    public static function generatePaymentId(): string
    {
        do {
            $n = 'PAY-' . date('ymd') . '-' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('payment_id', $n)->exists());
        return $n;
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'unpaid'         => 'پرداخت نشده',
            'pending_review' => 'در انتظار بررسی',
            'paid'           => 'پرداخت شده',
            'rejected'       => 'رد شده',
            'cancelled'      => 'لغو شده',
        ][$this->status] ?? $this->status;
    }

    /**
     * برچسب فارسی وضعیت قبض.
     */
    public static function statusLabel(?string $status): string
    {
        return match ($status) {
            'unpaid'          => 'پرداخت‌نشده',
            'pending_review'  => 'در انتظار تأیید',
            'paid'            => 'پرداخت‌شده',
            'rejected'        => 'رد‌شده',
            'cancelled'       => 'لغو‌شده',
            default           => (string) $status,
        };
    }

    /**
     * لیست همه وضعیت‌ها برای فیلتر در پنل ادمین.
     */
    public static function statuses(): array
    {
        return [
            'unpaid'         => 'پرداخت‌نشده',
            'pending_review' => 'در انتظار تأیید',
            'paid'           => 'پرداخت‌شده',
            'rejected'       => 'رد‌شده',
            'cancelled'      => 'لغو‌شده',
        ];
    }

}

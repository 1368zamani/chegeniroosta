<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TariffReceipt extends Model
{
    protected $fillable = [
        'invoice_id',
        'receipt_file',
        'receipt_mime',
        'receipt_size',
        'paid_amount',
        'paid_at',
        'paid_at_jalali',
        'bank_ref_no',
        'installment_no',
        'status',
        'reviewed_by',
        'reviewed_at',
        'reject_reason',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'paid_at'     => 'date',
        'reviewed_at' => 'datetime',
        'paid_amount' => 'integer',
        'receipt_size'=> 'integer',
        'installment_no' => 'integer',
    ];

    // ═══ روابط ═══
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(TariffInvoice::class, 'invoice_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ═══ متدهای کمکی ═══
    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'  => 'در انتظار بررسی',
            'approved' => 'تایید شده',
            'rejected' => 'رد شده',
            default    => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'pending'  => 'bg-amber-100 text-amber-900',
            'approved' => 'bg-emerald-100 text-emerald-800',
            'rejected' => 'bg-red-100 text-red-800',
            default    => 'bg-gray-100',
        };
    }

    /**
     * مسیر کامل فایل رسید
     */
    public function fileUrl(): ?string
    {
        if (!$this->receipt_file) {
            return null;
        }
        return asset('storage/' . $this->receipt_file);
    }

    /**
     * تایید رسید
     */
    public function approve(int $userId): self
    {
        $this->status = 'approved';
        $this->reviewed_by = $userId;
        $this->reviewed_at = now();
        $this->save();

        // آپدیت فیش
        $this->invoice->status = 'paid';
        $this->invoice->paid_at = now();
        $this->invoice->paid_amount = $this->paid_amount;
        $this->invoice->save();

        return $this;
    }

    /**
     * رد رسید
     */
    public function reject(int $userId, string $reason): self
    {
        $this->status = 'rejected';
        $this->reviewed_by = $userId;
        $this->reviewed_at = now();
        $this->reject_reason = $reason;
        $this->save();

        // برگرداندن فیش به حالت صادرشده
        if ($this->invoice->status === 'receipt_uploaded') {
            $this->invoice->status = 'issued';
            $this->invoice->save();
        }

        return $this;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LevyPublicRequest extends Model
{
    protected $table = 'levy_public_requests';

    protected $fillable = [
        'tracking_code', 'village_id', 'type',
        'payer_name', 'payer_mobile', 'national_id', 'notes',
        'construction_area', 'property_area',
        'transaction_value', 'final_coefficient', 'residential_levy', 'density_levy', 'final_levy',
        'amount', 'account_no', 'iban', 'account_holder', 'status',
        'receipt_path', 'reviewed_by', 'reviewed_at', 'review_note',
    ];

    protected $casts = [
        'construction_area'  => 'decimal:2',
        'property_area'      => 'decimal:2',
        'transaction_value'  => 'integer',
        'final_coefficient'  => 'integer',
        'residential_levy'   => 'integer',
        'density_levy'       => 'integer',
        'final_levy'         => 'integer',
        'amount'             => 'integer',
        'reviewed_at'        => 'datetime',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(LevyPublicRequestItem::class)->orderBy('sort_order');
    }

    public function events(): HasMany
    {
        return $this->hasMany(LevyPublicRequestEvent::class)->orderByDesc('id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function typeLabel(): string
    {
        return $this->type === 'construction' ? 'عوارض احداث بنا' : 'عوارض پسماند';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'awaiting_receipt' => 'در انتظار پرداخت و رسید',
            'under_review'     => 'رسید ارسال شد — در بررسی',
            'approved'         => 'تأییدشده',
            'rejected'         => 'ردشده',
            default            => (string) $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'awaiting_receipt' => 'bg-amber-100 text-amber-800',
            'under_review'     => 'bg-blue-100 text-blue-800',
            'approved'         => 'bg-emerald-100 text-emerald-800',
            'rejected'         => 'bg-rose-100 text-rose-800',
            default            => 'bg-slate-100 text-slate-700',
        };
    }

    public static function generateTrackingCode(): string
    {
        do {
            $code = 'LV-' . now()->format('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        } while (static::where('tracking_code', $code)->exists());

        return $code;
    }

    public function addEvent(string $action, ?string $note = null, ?int $userId = null): void
    {
        $this->events()->create([
            'action'  => $action,
            'note'    => $note,
            'user_id' => $userId ?? auth()->id(),
        ]);
    }
}

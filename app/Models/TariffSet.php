<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TariffSet extends Model
{
    protected $table = 'tariff_sets';

    protected $fillable = [
        'village_id', 'jalali_year', 'inflation_rate', 'status',
        'approved_at', 'announced_at', 'locked_at',
        'approval_document_path', 'approval_notes',
        'created_by', 'deadline_approval', 'deadline_announcement',
    ];

    protected $casts = [
        'inflation_rate' => 'decimal:2',
        'approved_at'    => 'datetime',
        'announced_at'   => 'datetime',
        'locked_at'      => 'datetime',
    ];

    public function village(): BelongsTo { return $this->belongsTo(Village::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function parameters(): HasMany { return $this->hasMany(TariffParameter::class, 'tariff_set_id'); }
    public function items(): HasMany { return $this->hasMany(TariffItem::class, 'tariff_set_id'); }

    public function scopePublished($q) { return $q->where('status', 'published'); }

    public function isLocked(): bool
    {
        return in_array($this->status, ['finalized', 'published'], true) || $this->locked_at !== null;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'draft'                     => 'پیش‌نویس',
            'proposed'                  => 'پیشنهادی دهیار',
            'village_council_approved'  => 'تصویب شورای روستا',
            'district_council_approved' => 'تأیید شورای بخش',
            'finalized'                 => 'قطعی‌شده',
            'published'                 => 'اعلام عمومی',
            default                     => $this->status,
        };
    }

    public static function latestPublishedFor(int $villageId): ?self
    {
        return self::where('village_id', $villageId)
            ->where('status', 'published')
            ->orderByDesc('jalali_year')
            ->first();
    }
}

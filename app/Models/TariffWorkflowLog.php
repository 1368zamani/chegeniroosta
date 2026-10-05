<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TariffWorkflowLog extends Model
{
    protected $table = 'tariff_workflow_logs';

    protected $fillable = [
        'tariff_set_id', 'user_id', 'from_status', 'to_status',
        'user_role', 'ip', 'note', 'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function set(): BelongsTo { return $this->belongsTo(TariffSet::class, 'tariff_set_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public static function record(TariffSet $set, ?string $from, string $to, ?User $user, ?string $note = null, array $context = []): self
    {
        return self::create([
            'tariff_set_id' => $set->id,
            'user_id'       => $user?->id,
            'from_status'   => $from,
            'to_status'     => $to,
            'user_role'     => $user?->role,
            'ip'            => request()?->ip(),
            'note'          => $note,
            'context'       => $context,
        ]);
    }

    public function toLabel(): string
    {
        return match ($this->to_status) {
            'draft'                     => 'پیش‌نویس',
            'proposed'                  => 'پیشنهادی دهیار',
            'village_council_approved'  => 'تصویب شورای روستا',
            'district_council_approved' => 'تأیید شورای بخش',
            'finalized'                 => 'قطعی‌شده',
            'published'                 => 'اعلام عمومی',
            default                     => $this->to_status,
        };
    }
}

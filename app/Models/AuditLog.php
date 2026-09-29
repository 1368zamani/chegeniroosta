<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id', 'action', 'entity_type', 'entity_id',
        'changes', 'amount_rial', 'ip_address', 'description',
    ];

    protected $casts = ['changes' => 'array'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public static function record(string $action, string $entityType, $entityId = null, array $data = []): self
    {
        return static::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'changes'     => $data['changes'] ?? null,
            'amount_rial' => $data['amount_rial'] ?? null,
            'ip_address'  => request()->ip(),
            'description' => $data['description'] ?? null,
        ]);
    }
}

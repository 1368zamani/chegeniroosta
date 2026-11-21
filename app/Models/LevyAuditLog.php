<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LevyAuditLog extends Model
{
    protected $table = 'levy_audit_logs';
    public $timestamps = false;

    protected $fillable = ['levy_file_id', 'user_id', 'action', 'changes', 'ip', 'created_at'];

    protected $casts = [
        'changes'    => 'array',
        'created_at' => 'datetime',
    ];

    public function file(): BelongsTo
    {
        return $this->belongsTo(LevyFile::class, 'levy_file_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}

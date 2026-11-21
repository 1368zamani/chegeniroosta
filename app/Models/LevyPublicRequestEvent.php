<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LevyPublicRequestEvent extends Model
{
    protected $table = 'levy_public_request_events';

    // جدول رویدادها فقط ستون created_at دارد (بدون updated_at)
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'levy_public_request_id', 'action', 'user_id', 'note', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(LevyPublicRequest::class, 'levy_public_request_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

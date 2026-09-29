<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillageCouncilResolution extends Model
{
    protected $table = 'village_council_resolutions';

    protected $fillable = [
        'village_id', 'user_id', 'session_number', 'session_date',
        'title', 'description', 'status', 'progress_percent',
        'budget', 'deadline', 'admin_note', 'attachment',
        'is_public', 'sort_order',
    ];

    protected $casts = [
        'session_date' => 'date',
        'deadline'     => 'date',
        'is_public'    => 'boolean',
        'budget'       => 'decimal:0',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'approved'    => 'تصویب شده',
            'in_progress' => 'در حال اجرا',
            'completed'   => 'اجرا شده',
            'suspended'   => 'متوقف شده',
            'rejected'    => 'رد شده',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return [
            'approved'    => '#1e40af',
            'in_progress' => '#f59e0b',
            'completed'   => '#16a34a',
            'suspended'   => '#6b7280',
            'rejected'    => '#dc2626',
        ][$this->status] ?? '#6b7280';
    }

    public function getStatusBgAttribute(): string
    {
        return [
            'approved'    => '#dbeafe',
            'in_progress' => '#fef3c7',
            'completed'   => '#d1fae5',
            'suspended'   => '#e5e7eb',
            'rejected'    => '#fee2e2',
        ][$this->status] ?? '#e5e7eb';
    }
}

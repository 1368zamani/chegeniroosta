<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillageProject extends Model
{
    protected $fillable = [
        'village_id', 'title', 'description', 'status', 'progress',
        'contractor', 'budget', 'start_date', 'end_date',
        'before_image', 'after_image', 'cover_image', 'sort_order', 'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'progress'   => 'integer',
        'is_active'  => 'boolean',
    ];

    public function village() { return $this->belongsTo(Village::class); }

    /** فقط موارد فعال — برای نمایش در سایت */
    public function scopeActive($q) { return $q->where('is_active', true); }

    public function getStatusLabelAttribute(): string
    {
        return [
            'planned'     => 'در برنامه',
            'in_progress' => 'در حال اجرا',
            'done'        => 'انجام شده',
            'paused'      => 'متوقف',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return [
            'planned'     => '#78909c',
            'in_progress' => '#f9a825',
            'done'        => '#2e7d32',
            'paused'      => '#c62828',
        ][$this->status] ?? '#78909c';
    }
}

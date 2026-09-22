<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillageRequest extends Model
{
    protected $fillable = [
        'village_id', 'tracking_code', 'full_name', 'phone',
        'subject', 'description', 'attachment', 'status',
        'admin_response', 'resolved_at', 'ip_address'
    ];

    protected $casts = ['resolved_at' => 'datetime'];

    public function village() { return $this->belongsTo(Village::class); }

    public function getStatusLabelAttribute(): string
    {
        return [
            'submitted'  => 'ثبت شده',
            'reviewing'  => 'در حال بررسی',
            'assigned'   => 'ارجاع داده شده',
            'processing' => 'در حال پیگیری',
            'resolved'   => 'پاسخ داده شده',
            'rejected'   => 'رد شده',
        ][$this->status] ?? $this->status;
    }

    public static function generateTrackingCode(): string
    {
        do {
            $code = 'REQ-' . date('y') . '-' . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('tracking_code', $code)->exists());
        return $code;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * فایل قابل دانلود یک روستا.
 *
 * ادمین روستا آن را از پنل بارگذاری می‌کند و در پایین صفحه‌ی
 * «ثبت درخواست» شهروند نمایش داده می‌شود.
 */
class VillageDownload extends Model
{
    protected $table = 'village_downloads';

    protected $fillable = [
        'village_id',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'is_active',
        'sort_order',
        'download_count',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'file_size'      => 'integer',
        'download_count' => 'integer',
        'sort_order'     => 'integer',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    // ══════════════════════════════════════════════════════
    // اسکوپ‌ها
    // ══════════════════════════════════════════════════════

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeForVillage($q, int $villageId)
    {
        return $q->where('village_id', $villageId);
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort_order')->orderByDesc('id');
    }

    // ══════════════════════════════════════════════════════
    // کمکی‌ها
    // ══════════════════════════════════════════════════════

    /** حجم خوانا: «۲.۴ مگابایت» */
    public function sizeLabel(): string
    {
        $bytes = (int) $this->file_size;

        if ($bytes <= 0) {
            return '—';
        }

        $units = ['بایت', 'کیلوبایت', 'مگابایت', 'گیگابایت'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        $num = $i === 0 ? (string) (int) $bytes : number_format($bytes, 1);

        return \App\Support\Number::fa($num) . ' ' . $units[$i];
    }

    /** آیکون بر اساس نوع فایل */
    public function icon(): string
    {
        $mime = (string) $this->mime_type;
        $ext  = strtolower(pathinfo((string) $this->file_name, PATHINFO_EXTENSION));

        if (str_contains($mime, 'pdf') || $ext === 'pdf') {
            return '📕';
        }
        if (str_starts_with($mime, 'image/') || in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            return '🖼';
        }
        if (in_array($ext, ['doc', 'docx'], true)) {
            return '📘';
        }
        if (in_array($ext, ['xls', 'xlsx', 'csv'], true)) {
            return '📗';
        }
        if (in_array($ext, ['zip', 'rar', '7z'], true)) {
            return '🗜';
        }

        return '📄';
    }
}

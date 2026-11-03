<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** فایل ضمیمه‌ی محصول (کاتالوگ، مشخصات فنی، مجوز و…) */
class VillageProductFile extends Model
{
    protected $table = 'village_product_files';

    protected $fillable = [
        'product_id',
        'title',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'download_count',
        'sort_order',
    ];

    protected $casts = [
        'file_size'      => 'integer',
        'download_count' => 'integer',
        'sort_order'     => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(VillageProduct::class, 'product_id');
    }

    /** حجم خوانا */
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

        if (str_contains($mime, 'pdf') || $ext === 'pdf')               return '📕';
        if (str_starts_with($mime, 'image/'))                            return '🖼';
        if (in_array($ext, ['doc', 'docx'], true))                       return '📘';
        if (in_array($ext, ['xls', 'xlsx', 'csv'], true))                return '📗';
        if (in_array($ext, ['zip', 'rar', '7z'], true))                  return '🗜';

        return '📄';
    }
}

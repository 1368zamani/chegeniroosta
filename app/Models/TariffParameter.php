<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TariffParameter extends Model
{
    protected $table = 'tariff_parameters';

    protected $fillable = ['tariff_set_id', 'key', 'value', 'unit', 'description'];

    public function set(): BelongsTo
    {
        return $this->belongsTo(TariffSet::class, 'tariff_set_id');
    }

    /** خواندن یک پارامتر با تبدیل هوشمند */
    public function typedValue(): int|float|string|null
    {
        $v = $this->value;
        if ($v === null) return null;
        if (is_numeric($v)) {
            return str_contains($v, '.') ? (float) $v : (int) $v;
        }
        return $v;
    }

    /** خواندن مقدار یک پارامتر از یک مجموعه با کلید */
    public static function get(TariffSet $set, string $key, $default = null)
    {
        $p = self::where('tariff_set_id', $set->id)->where('key', $key)->first();
        return $p ? $p->typedValue() : $default;
    }

    /** ذخیره یا به‌روزرسانی یک پارامتر */
    public static function put(TariffSet $set, string $key, $value, ?string $unit = null): self
    {
        return self::updateOrCreate(
            ['tariff_set_id' => $set->id, 'key' => $key],
            ['value' => $value, 'unit' => $unit]
        );
    }
}
